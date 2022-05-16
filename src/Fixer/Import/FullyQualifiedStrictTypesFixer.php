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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 */
final class FullyQualifiedStrictTypesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes the leading part of fully qualified symbol references if a given symbol is imported or belongs to the current namespace. Fixes function arguments, caught exception `classes`, `extend` and `implements` of `classes` and `interfaces` to short version.',
            [
                new CodeSample(
                    '<?php
use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo, \Exception $e): \Foo\Bar\Baz
    {
    }
}
'
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
                    ['shorten_globals_in_global_ns' => true],
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

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_FUNCTION, T_IMPLEMENTS, T_EXTENDS, T_CATCH, T_DOUBLE_COLON]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $namespacesAnalyzer = new NamespacesAnalyzer();
        $namespaceUsesAnalyzer = new NamespaceUsesAnalyzer();
        $functionsAnalyzer = new FunctionsAnalyzer();

        foreach ($namespacesAnalyzer->getDeclarations($tokens) as $namespace) {
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
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('shorten_globals_in_global_ns', 'remove leading `\` when in global namespace.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixFunction(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $arguments = $functionsAnalyzer->getFunctionArguments($tokens, $index);

        foreach ($arguments as $argument) {
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
                if (true === $this->configuration['shorten_globals_in_global_ns']) {
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
        if ($type->isReservedType()) {
            return;
        }

        $typeStartIndex = $type->getStartIndex();

        if ($tokens[$typeStartIndex]->isGivenKind(CT::T_NULLABLE_TYPE)) {
            $typeStartIndex = $tokens->getNextMeaningfulToken($typeStartIndex);
        }

        $namespaceNameLength = \strlen($namespaceName);
        $types = $this->getTypes($tokens, $typeStartIndex, $type->getEndIndex());

        foreach ($types as $typeName => [$startIndex, $endIndex]) {
            if (!str_starts_with($typeName, '\\')) {
                continue; // no shorter type possible
            }

            $typeName = substr($typeName, 1);
            $typeNameLower = strtolower($typeName);

            if (isset($uses[$typeNameLower])) {
                // if the type without leading "\" equals any of the full "uses" long names, it can be replaced with the short one
                $tokens->overrideRange($startIndex, $endIndex, $this->namespacedStringToTokens($uses[$typeNameLower]));
            } elseif ('' === $namespaceName) {
                if (true === $this->configuration['shorten_globals_in_global_ns']) {
                    foreach ($uses as $useShortName) {
                        if (strtolower($useShortName) === $typeNameLower) {
                            continue 2;
                        }
                    }

                    $tokens->overrideRange($startIndex, $endIndex, $this->namespacedStringToTokens($typeName));
                }
            } elseif (
                $typeNameLower !== $namespaceName
                && str_starts_with($typeNameLower, $namespaceName)
                && '\\' === $typeNameLower[\strlen($namespaceName)]
            ) {
                // if the type starts with namespace and the type is not the same as the namespace it can be shortened
                $typeNameShort = substr($typeName, $namespaceNameLength + 1);
                $tokens->overrideRange($startIndex, $endIndex, $this->namespacedStringToTokens($typeNameShort));
            }
        }
    }

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
        $index = $typeStartIndex = $typeEndIndex = $tokens->getNextMeaningfulToken($index - 1);
        $type = $tokens[$index]->getContent();

        while (true) {
            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->isGivenKind([CT::T_TYPE_ALTERNATION, CT::T_TYPE_INTERSECTION])) {
                yield $type => [$typeStartIndex, $typeEndIndex];

                $index = $typeStartIndex = $typeEndIndex = $tokens->getNextMeaningfulToken($index);
                $type = $tokens[$index]->getContent();

                continue;
            }

            if ($index > $endIndex || !$tokens[$index]->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
                yield $type => [$typeStartIndex, $typeEndIndex];

                break;
            }

            $typeEndIndex = $index;
            $type .= $tokens[$index]->getContent();
        }
    }

    /**
     * @return Token[]
     */
    private function namespacedStringToTokens(string $input): array
    {
        $tokens = [];
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
