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
use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
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
use PhpCsFixer\Tokenizer\Processor\ImportProcessor;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 * @author Tomas Jadrny <developer@tomasjadrny.cz>
 * @author Greg Korba <greg@codito.dev>
 * @author SpacePossum <possumfromspace@gmail.com>
 */
final class FullyQualifiedStrictTypesFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var array{
     *     const?: array<string, class-string>,
     *     class?: array<string, class-string>,
     *     function?: array<string, class-string>
     * }
     */
    private array $symbolsForImport = [];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes the leading part of fully qualified symbol references if a given symbol is imported or belongs to the current namespace.',
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
                new CodeSample(
                    '<?php

namespace Foo\Test;

class Foo extends \Other\BaseClass implements \Other\Interface1, \Other\Interface2
{
    /** @var \Other\PropertyPhpDoc */
    private $array;
    public function __construct(\Other\FunctionArgument $arg) {}
    public function foo(): \Other\FunctionReturnType
    {
        try {
            \Other\StaticFunctionCall::bar();
        } catch (\Other\CaughtThrowable $e) {}
    }
}
',
                    ['import_symbols' => true]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer, OrderedImportsFixer, StatementIndentationFixer.
     * Must run after PhpdocToReturnTypeFixer.
     */
    public function getPriority(): int
    {
        return 7;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([
            CT::T_USE_TRAIT,
            T_CATCH,
            T_DOUBLE_COLON,
            T_DOC_COMMENT,
            T_EXTENDS,
            T_FUNCTION,
            T_IMPLEMENTS,
            T_INSTANCEOF,
            T_NEW,
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
            (new FixerOptionBuilder(
                'import_symbols',
                'Whether FQCNs found during analysis should be automatically imported.'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder(
                'phpdoc_tags',
                'Collection of PHPDoc annotation tags where FQCNs should be processed. As of now only simple tags with `@tag \F\Q\C\N` format are supported (no complex types).'
            ))
                ->setAllowedTypes(['array'])
                ->setDefault([
                    'param',
                    'phpstan-param',
                    'phpstan-property',
                    'phpstan-property-read',
                    'phpstan-property-write',
                    'phpstan-return',
                    'phpstan-var',
                    'property',
                    'property-read',
                    'property-write',
                    'psalm-param',
                    'psalm-property',
                    'psalm-property-read',
                    'psalm-property-write',
                    'psalm-return',
                    'psalm-var',
                    'return',
                    'see',
                    'throws',
                    'var',
                ])
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
            $lastUse = null;

            foreach ($namespaceUsesAnalyzer->getDeclarationsInNamespace($tokens, $namespace) as $use) {
                $uses[$this->normaliseSymbolName($use->getFullName())] = $use->getShortName();
                $lastUse = $use;
            }

            for ($index = $namespace->getScopeStartIndex(); $index < $namespace->getScopeEndIndex(); ++$index) {
                if ($tokens[$index]->isGivenKind(T_FUNCTION)) {
                    $this->fixFunction($functionsAnalyzer, $tokens, $index, $uses, $namespaceName);
                } elseif ($tokens[$index]->isGivenKind([T_EXTENDS, T_IMPLEMENTS])) {
                    $this->fixExtendsImplements($tokens, $index, $uses, $namespaceName);
                } elseif ($tokens[$index]->isGivenKind(T_CATCH)) {
                    $this->fixCatch($tokens, $index, $uses, $namespaceName);
                } elseif ($tokens[$index]->isGivenKind(T_DOUBLE_COLON)) {
                    $this->fixPrevName($tokens, $index, $uses, $namespaceName);
                } elseif ($tokens[$index]->isGivenKind([T_INSTANCEOF, T_NEW, CT::T_USE_TRAIT])) {
                    $this->fixNextName($tokens, $index, $uses, $namespaceName);
                }

                if ($tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
                    $this->fixPhpDoc($tokens, $index, $uses, $namespaceName);
                }
            }

            if (true === $this->configuration['import_symbols'] && [] !== $this->symbolsForImport) {
                $atIndex = (null !== $lastUse) ? $lastUse->getEndIndex() + 1 : $namespace->getEndIndex() + 1;

                // Insert all registered FQCNs
                $this->createImportProcessor()->insertImports($tokens, $this->symbolsForImport, $atIndex);

                $this->symbolsForImport = [];
            }
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixFunction(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index, array &$uses, string $namespaceName): void
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
    private function fixPhpDoc(Tokens $tokens, int $index, array &$uses, string $namespaceName): void
    {
        $allowedTags = $this->configuration['phpdoc_tags'];

        if ([] === $allowedTags) {
            return;
        }

        $phpDoc = $tokens[$index];
        $phpDocContent = $phpDoc->getContent();
        $phpDocContentNew = Preg::replaceCallback('/([*{]\h*@)(\S+)(\h+)('.TypeExpression::REGEX_TYPES.')(?!(?!\})\S)/', function ($matches) use ($allowedTags, &$uses, $namespaceName) {
            if (!\in_array($matches[2], $allowedTags, true)) {
                return $matches[0];
            }

            // @TODO parse the complex type using TypeExpression and fix all names inside (like `int|string` or `list<int|string>`)
            if (!Preg::match('/^[a-zA-Z0-9_\\\\]+(\|null)?$/', $matches[4])) {
                return $matches[0];
            }

            if (true === $this->configuration['import_symbols']) {
                $this->registerSymbolForImport('class', $matches[4], $uses, $namespaceName);
            }

            $shortTokens = $this->determineShortType($matches[4], $uses, $namespaceName);
            if (null === $shortTokens) {
                return $matches[0];
            }

            return $matches[1].$matches[2].$matches[3].implode('', array_map(
                static fn (Token $token) => $token->getContent(),
                $shortTokens
            ));
        }, $phpDocContent);

        if ($phpDocContentNew !== $phpDocContent) {
            $tokens[$index] = new Token([T_DOC_COMMENT, $phpDocContentNew]);
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixExtendsImplements(Tokens $tokens, int $index, array &$uses, string $namespaceName): void
    {
        // We handle `extends` and `implements` with similar logic, but we need to exit the loop under different conditions.
        $isExtends = $tokens[$index]->equals([T_EXTENDS]);
        $index = $tokens->getNextMeaningfulToken($index);
        $extend = ['content' => '', 'tokens' => []];

        while (true) {
            if ($tokens[$index]->equalsAny([',', '{', [T_IMPLEMENTS]])) {
                if ([] !== $extend['tokens']) {
                    $this->shortenClassIfPossible($tokens, $extend, $uses, $namespaceName);
                }

                if ($tokens[$index]->equalsAny($isExtends ? [[T_IMPLEMENTS], '{'] : ['{'])) {
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
    private function fixCatch(Tokens $tokens, int $index, array &$uses, string $namespaceName): void
    {
        $index = $tokens->getNextMeaningfulToken($index); // '('
        $index = $tokens->getNextMeaningfulToken($index); // first part of first exception class to be caught

        $caughtExceptionClass = ['content' => '', 'tokens' => []];

        while (true) {
            if ($tokens[$index]->equalsAny([')', [T_VARIABLE], [CT::T_TYPE_ALTERNATION]])) {
                if ([] === $caughtExceptionClass['tokens']) {
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
    private function fixPrevName(Tokens $tokens, int $index, array &$uses, string $namespaceName): void
    {
        $classConstantRef = ['content' => '', 'tokens' => []];

        while (true) {
            $index = $tokens->getPrevMeaningfulToken($index);

            if ($tokens[$index]->equalsAny([[T_STRING], [T_NS_SEPARATOR]])) {
                $classConstantRef['tokens'][] = $index;
                $classConstantRef['content'] = $tokens[$index]->getContent().$classConstantRef['content'];
            } else {
                $classConstantRef['tokens'] = array_reverse($classConstantRef['tokens']);
                if ([] !== $classConstantRef['tokens']) {
                    $this->shortenClassIfPossible($tokens, $classConstantRef, $uses, $namespaceName);
                }

                break;
            }
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixNextName(Tokens $tokens, int $index, array &$uses, string $namespaceName): void
    {
        $classConstantRef = ['content' => '', 'tokens' => []];

        while (true) {
            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->equalsAny([[T_STRING], [T_NS_SEPARATOR]])) {
                $classConstantRef['tokens'][] = $index;
                $classConstantRef['content'] .= $tokens[$index]->getContent();
            } else {
                if ([] !== $classConstantRef['tokens']) {
                    $this->shortenClassIfPossible($tokens, $classConstantRef, $uses, $namespaceName);
                }

                break;
            }
        }
    }

    /**
     * @param array{content: string, tokens: array<int>} $class
     * @param array<string, string>                      $uses
     */
    private function shortenClassIfPossible(Tokens $tokens, array $class, array &$uses, string $namespaceName): void
    {
        $longTypeContent = $class['content'];

        if (true === $this->configuration['import_symbols']) {
            $this->registerSymbolForImport('class', $longTypeContent, $uses, $namespaceName);
        }

        if (str_starts_with($longTypeContent, '\\') || '' === $namespaceName) {
            $typeName = ltrim($longTypeContent, '\\');
            $typeNameLower = strtolower($typeName);

            if (isset($uses[$typeNameLower])) {
                // if the type without leading "\" equals any of the full "uses" long names, it can be replaced with the short one
                $this->replaceClassWithShort($tokens, $class, $uses[$typeNameLower]);
            } elseif ('' === $namespaceName) {
                $inUses = false;
                foreach ($uses as $useShortName) {
                    if (strtolower($useShortName) === $typeNameLower) {
                        $inUses = true;

                        break;
                    }
                }

                if (!$inUses) {
                    if (true === $this->configuration['leading_backslash_in_global_namespace']) {
                        if ($typeName === $longTypeContent) {
                            $tokens->insertAt($class['tokens'][0], new Token([T_NS_SEPARATOR, '\\']));
                        }
                    } else {
                        // if we are in the global namespace and the type is not imported the leading '\' can be removed
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
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function replaceByShortType(Tokens $tokens, TypeAnalysis $type, array &$uses, string $namespaceName): void
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

            if (true === $this->configuration['import_symbols']) {
                $this->registerSymbolForImport('class', $typeName, $uses, $namespaceName);
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

    /**
     * We need to create import processor dynamically (not in costructor), because actual whitespace configuration
     * is set later, not when fixer's instance is created.
     */
    private function createImportProcessor(): ImportProcessor
    {
        return new ImportProcessor($this->whitespacesConfig);
    }

    /**
     * @param "class"|"const"|"function" $kind
     * @param class-string               $symbol
     * @param array<string, string>      $uses
     */
    private function registerSymbolForImport(string $kind, string $symbol, array &$uses, string $namespaceName): void
    {
        $normalisedName = $this->normaliseSymbolName($symbol);

        // Do NOT register symbol for importing if:
        if (
            // we already have the symbol in existing imports
            isset($uses[$normalisedName])
            // or if the symbol is not a FQCN
            || !str_starts_with($symbol, '\\')
            // or if it's a global symbol
            || strpos($symbol, '\\') === strrpos($symbol, '\\')
        ) {
            return;
        }

        $shortSymbol = substr($symbol, strrpos($symbol, '\\') + 1);
        $importedShortNames = array_map(
            static fn (string $name): string => strtolower($name),
            array_values($uses)
        );

        // If symbol
        if (\in_array(strtolower($shortSymbol), $importedShortNames, true)) {
            return;
        }

        $this->symbolsForImport[$kind][$normalisedName] = ltrim($symbol, '\\');
        ksort($this->symbolsForImport[$kind], SORT_NATURAL);

        // We must fake that the symbol is imported, so that it can be shortened.
        $uses[$normalisedName] = $shortSymbol;
    }

    /**
     * @param class-string $name
     */
    private function normaliseSymbolName(string $name): string
    {
        return strtolower(ltrim($name, '\\'));
    }
}
