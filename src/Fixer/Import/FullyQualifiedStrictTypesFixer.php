<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 * @author Tomas Jadrny <developer@tomasjadrny.cz>
 * @author Greg Korba <greg@codito.dev>
 * @author SpacePossum <possumfromspace@gmail.com>
 */
final class FullyQualifiedStrictTypesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes the leading part of fully qualified symbol references if a given symbol is imported or belongs to the current namespace. Fixes function arguments, exceptions in `catch` block, `extend` and `implements` of classes and interfaces.',
            [
                new CodeSample(
                    '<?php

use Foo\Bar;
use Foo\Bar\Baz;
use Foo\OtherClass;
use Foo\SomeContract;
use Foo\SomeException;

/**
 * @see \Foo\Bar\Baz
 */
class SomeClass extends \Foo\OtherClass implements \Foo\SomeContract
{
    /**
     * @var \Foo\Bar\Baz
     */
    public $baz;

    /**
     * @param \Foo\Bar\Baz $baz
     */
    public function __construct($baz) {
        $this->baz = $baz;
    }

    /**
     * @return \Foo\Bar\Baz
     */
    public function getBaz() {
        return $this->baz;
    }

    public function doX(\Foo\Bar $foo, \Exception $e): \Foo\Bar\Baz
    {
        try {}
        catch (\Foo\SomeException $e) {}
    }
}
'
                ),
                new CodeSample(
                    '<?php

class SomeClass
{
    public function doY(Foo\NotImported $u, \Foo\NotImported $v)
    {
    }
}
',
                    ['leading_backslash_in_global_namespace' => true]
                ),
                new CodeSample(
                    '<?php
namespace {
    use Foo\A;
    try {
        foo();
    } catch (\Exception|\Foo\A $e) {
    }
}
namespace Foo\Bar {
    class SomeClass implements \Foo\Bar\Baz
    {
    }
}
',
                    ['leading_backslash_in_global_namespace' => true]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer.
     * Must run after PhpdocToReturnTypeFixer.
     */
    public function getPriority(): int
    {
        return 7;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([
            T_FUNCTION,
            T_DOC_COMMENT,
            T_IMPLEMENTS,
            T_EXTENDS,
            T_CATCH,
            T_DOUBLE_COLON,
        ]);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(
                'leading_backslash_in_global_namespace',
                'Whether FQCN is prefixed with backslash when that FQCN is used in global namespace context.'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $namespaceUsesAnalyzer = new NamespaceUsesAnalyzer();
        $functionsAnalyzer = new FunctionsAnalyzer();

        foreach ($tokens->getNamespaceDeclarations() as $namespace) {
            $namespaceName = strtolower($namespace->getFullName());
            $uses = [];

            foreach ($namespaceUsesAnalyzer->getDeclarationsInNamespace($tokens, $namespace) as $use) {
                $uses[strtolower(ltrim($use->getFullName(), '\\'))] = $use->getShortName();
            }

            for ($index = $namespace->getScopeStartIndex(); $index < $namespace->getScopeEndIndex(); ++$index) {
                if ($tokens[$index]->isGivenKind(T_FUNCTION)) {
                    $this->fixFunction($functionsAnalyzer, $tokens, $index, $uses, $namespaceName);
                } elseif ($tokens[$index]->isGivenKind([T_EXTENDS, T_IMPLEMENTS])) {
                    $this->fixExtendsImplements($tokens, $index, $uses, $namespaceName);
                } elseif ($tokens[$index]->isGivenKind(T_CATCH)) {
                    $this->fixCatch($tokens, $index, $uses, $namespaceName);
                } elseif ($tokens[$index]->isGivenKind(T_DOUBLE_COLON)) {
                    $this->fixClassStaticAccess($tokens, $index, $uses, $namespaceName);
                }

                if ($tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
                    $this->fixPhpDoc($tokens, $index, $uses, $namespaceName);
                }
            }
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixFunction(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $arguments = $functionsAnalyzer->getFunctionArguments($tokens, $index);

        foreach ($arguments as $i => $argument) {
            $argument = $functionsAnalyzer->getFunctionArguments($tokens, $index)[$i];

            if ($argument->hasTypeAnalysis()) {
                $this->replaceByShortType($tokens, $argument->getTypeAnalysis(), $uses, $namespaceName);
            }
        }

        $returnTypeAnalysis = $functionsAnalyzer->getFunctionReturnType($tokens, $index);

        if (null !== $returnTypeAnalysis) {
            $this->replaceByShortType($tokens, $returnTypeAnalysis, $uses, $namespaceName);
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixPhpDoc(Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $phpDoc = $tokens[$index];
        $phpDocContent = $phpDoc->getContent();
        Preg::matchAll('#@([^\s]+)\s+([^\s]+)#', $phpDocContent, $matches);

        if ([] !== $matches) {
            foreach ($matches[2] as $i => $typeName) {
                if (!\in_array($matches[1][$i], ['param', 'return', 'see', 'throws', 'var'], true)) {
                    continue;
                }

                $shortTokens = $this->determineShortType($typeName, $uses, $namespaceName);

                if (null !== $shortTokens) {
                    // Replace tag+type in order to avoid replacing type multiple times (when same type is used in multiple places)
                    $phpDocContent = str_replace(
                        $matches[0][$i],
                        '@'.$matches[1][$i].' '.implode('', array_map(
                            static fn (Token $token) => $token->getContent(),
                            $shortTokens
                        )),
                        $phpDocContent
                    );
                }
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $phpDocContent]);
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixExtendsImplements(Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $index = $tokens->getNextMeaningfulToken($index);
        $extend = ['content' => '', 'tokens' => []];

        while (true) {
            if ($tokens[$index]->equalsAny([',', '{', [T_IMPLEMENTS]])) {
                $this->shortenClassIfPossible($tokens, $extend, $uses, $namespaceName);

                if ($tokens[$index]->equals('{')) {
                    break;
                }

                $extend = ['content' => '', 'tokens' => []];
            } else {
                $extend['tokens'][] = $index;
                $extend['content'] .= $tokens[$index]->getContent();
            }

            $index = $tokens->getNextMeaningfulToken($index);
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixCatch(Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $index = $tokens->getNextMeaningfulToken($index); // '('
        $index = $tokens->getNextMeaningfulToken($index); // first part of first exception class to be caught

        $caughtExceptionClass = ['content' => '', 'tokens' => []];

        while (true) {
            if ($tokens[$index]->equalsAny([')', [T_VARIABLE], [CT::T_TYPE_ALTERNATION]])) {
                if (0 === \count($caughtExceptionClass['tokens'])) {
                    break;
                }

                $this->shortenClassIfPossible($tokens, $caughtExceptionClass, $uses, $namespaceName);

                if ($tokens[$index]->equals(')')) {
                    break;
                }

                $caughtExceptionClass = ['content' => '', 'tokens' => []];
            } else {
                $caughtExceptionClass['tokens'][] = $index;
                $caughtExceptionClass['content'] .= $tokens[$index]->getContent();
            }

            $index = $tokens->getNextMeaningfulToken($index);
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixClassStaticAccess(Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $classConstantRef = ['content' => '', 'tokens' => []];

        while (true) {
            $index = $tokens->getPrevMeaningfulToken($index);

            if ($tokens[$index]->equalsAny([[T_STRING], [T_NS_SEPARATOR]])) {
                $classConstantRef['tokens'][] = $index;
                $classConstantRef['content'] = $tokens[$index]->getContent().$classConstantRef['content'];
            } else {
                $classConstantRef['tokens'] = array_reverse($classConstantRef['tokens']);
                $this->shortenClassIfPossible($tokens, $classConstantRef, $uses, $namespaceName);

                break;
            }
        }
    }

    /**
     * @param array{content: string, tokens: array<int>} $class
     * @param array<string, string>                      $uses
     */
    private function shortenClassIfPossible(Tokens $tokens, array $class, array $uses, string $namespaceName): void
    {
        $longTypeContent = $class['content'];

        if (str_starts_with($longTypeContent, '\\')) {
            $typeName = substr($longTypeContent, 1);
            $typeNameLower = strtolower($typeName);

            if (isset($uses[$typeNameLower])) {
                // if the type without leading "\" equals any of the full "uses" long names, it can be replaced with the short one
                $this->replaceClassWithShort($tokens, $class, $uses[$typeNameLower]);
            } elseif ('' === $namespaceName) {
                if (true === $this->configuration['leading_backslash_in_global_namespace']) {
                    // if we are in the global namespace and the type is not imported the leading '\' can be removed
                    $inUses = false;

                    foreach ($uses as $useShortName) {
                        if (strtolower($useShortName) === $typeNameLower) {
                            $inUses = true;

                            break;
                        }
                    }

                    if (!$inUses) {
                        $this->replaceClassWithShort($tokens, $class, $typeName);
                    }
                }
            } elseif (
                $typeNameLower !== $namespaceName
                && str_starts_with($typeNameLower, $namespaceName)
                && '\\' === $typeNameLower[\strlen($namespaceName)]
            ) {
                // if the type starts with namespace and the type is not the same as the namespace it can be shortened
                $typeNameShort = substr($typeName, \strlen($namespaceName) + 1);
                $this->replaceClassWithShort($tokens, $class, $typeNameShort);
            }
        } // else: no shorter type possible
    }

    /**
     * @param array<string, string> $uses
     */
    private function replaceByShortType(Tokens $tokens, TypeAnalysis $type, array $uses, string $namespaceName): void
    {
        $typeStartIndex = $type->getStartIndex();

        if ($tokens[$typeStartIndex]->isGivenKind(CT::T_NULLABLE_TYPE)) {
            $typeStartIndex = $tokens->getNextMeaningfulToken($typeStartIndex);
        }

        $types = $this->getTypes($tokens, $typeStartIndex, $type->getEndIndex());

        foreach ($types as $typeName => [$startIndex, $endIndex]) {
            if ((new TypeAnalysis($typeName))->isReservedType()) {
                return;
            }

            $shortType = $this->determineShortType($typeName, $uses, $namespaceName);

            if (null !== $shortType) {
                $tokens->overrideRange($startIndex, $endIndex, $shortType);
            }
        }
    }

    /**
     * Determines short type based on FQCN, current namespace and imports (`use` declarations).
     *
     * @param array<string, string> $uses
     *
     * @return null|Token[]
     */
    private function determineShortType(string $typeName, array $uses, string $namespaceName): ?array
    {
        $withLeadingBackslash = str_starts_with($typeName, '\\');
        if ($withLeadingBackslash) {
            $typeName = substr($typeName, 1);
        }
        $typeNameLower = strtolower($typeName);
        $namespaceNameLength = \strlen($namespaceName);

        if (isset($uses[$typeNameLower]) && ($withLeadingBackslash || '' === $namespaceName)) {
            // if the type without leading "\" equals any of the full "uses" long names, it can be replaced with the short one
            return $this->namespacedStringToTokens($uses[$typeNameLower]);
        }

        if ('' === $namespaceName) {
            // if we are in the global namespace and the type is not imported the leading '\' can be removed (TODO nice config candidate)
            foreach ($uses as $useShortName) {
                if (strtolower($useShortName) === $typeNameLower) {
                    return null;
                }
            }

            // if we are in the global namespace and the type is not imported,
            // we enforce/remove leading backslash (depending on the configuration)
            if (true === $this->configuration['leading_backslash_in_global_namespace']) {
                if (!$withLeadingBackslash && !isset($uses[$typeNameLower])) {
                    return $this->namespacedStringToTokens($typeName, true);
                }
            } else {
                return $this->namespacedStringToTokens($typeName);
            }
        }
        if (!str_contains($typeName, '\\')) {
            // If we're NOT in the global namespace, there's no related import,
            // AND used type is from global namespace, then it can't be shortened.
            return null;
        }
        if ($typeNameLower !== $namespaceName && str_starts_with($typeNameLower, $namespaceName.'\\')) {
            // if the type starts with namespace and the type is not the same as the namespace it can be shortened
            $typeNameShort = substr($typeName, $namespaceNameLength + 1);

            // if short names are the same, but long one are different then it cannot be shortened
            foreach ($uses as $useLongName => $useShortName) {
                if (
                    strtolower($typeNameShort) === strtolower($useShortName)
                    && strtolower($typeName) !== strtolower($useLongName)
                ) {
                    return null;
                }
            }

            return $this->namespacedStringToTokens($typeNameShort);
        }

        return null;
    }

    /**
     * @param array{content: string, tokens: array<int>} $class
     */
    private function replaceClassWithShort(Tokens $tokens, array $class, string $short): void
    {
        $i = 0; // override the tokens

        foreach ($this->namespacedStringToTokens($short) as $shortToken) {
            $tokens[$class['tokens'][$i]] = $shortToken;
            ++$i;
        }

        // clear the leftovers
        for ($j = \count($class['tokens']) - 1; $j >= $i; --$j) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($class['tokens'][$j]);
        }
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    private function getTypes(Tokens $tokens, int $index, int $endIndex): iterable
    {
        $skipNextYield = false;
        $typeStartIndex = $typeEndIndex = null;
        $type = null;
        while (true) {
            if ($tokens[$index]->isGivenKind(CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN)) {
                $index = $tokens->getNextMeaningfulToken($index);
                $typeStartIndex = $typeEndIndex = null;
                $type = null;

                continue;
            }

            if (
                $tokens[$index]->isGivenKind([CT::T_TYPE_ALTERNATION, CT::T_TYPE_INTERSECTION, CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE])
                || $index > $endIndex
            ) {
                if (!$skipNextYield && null !== $typeStartIndex) {
                    $origCount = \count($tokens);

                    yield $type => [$typeStartIndex, $typeEndIndex];

                    $endIndex += \count($tokens) - $origCount;

                    // type tokens were possibly updated, restart type match
                    $skipNextYield = true;
                    $index = $typeEndIndex = $typeStartIndex;
                    $type = null;
                } else {
                    $skipNextYield = false;
                    $index = $tokens->getNextMeaningfulToken($index);
                    $typeStartIndex = $typeEndIndex = null;
                    $type = null;
                }

                if ($index > $endIndex) {
                    break;
                }

                continue;
            }

            if (null === $typeStartIndex) {
                $typeStartIndex = $index;
                $type = '';
            }

            $typeEndIndex = $index;
            $type .= $tokens[$index]->getContent();

            $index = $tokens->getNextMeaningfulToken($index);
        }
    }

    /**
     * @return list<Token>
     */
    private function namespacedStringToTokens(string $input, bool $withLeadingBackslash = false): array
    {
        $tokens = [];

        if ($withLeadingBackslash) {
            $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
        }

        $parts = explode('\\', $input);
        foreach ($parts as $index => $part) {
            $tokens[] = new Token([T_STRING, $part]);

            if ($index !== \count($parts) - 1) {
                $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            }
        }

        return $tokens;
    }
}
