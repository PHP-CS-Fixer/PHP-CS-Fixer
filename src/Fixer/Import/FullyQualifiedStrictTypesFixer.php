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
 * @author Michael Vorisek <https://github.com/mvorisek>
 */
final class FullyQualifiedStrictTypesFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    private const REGEX_CLASS = '(?:\\\\?+'.TypeExpression::REGEX_IDENTIFIER
        .'(\\\\'.TypeExpression::REGEX_IDENTIFIER.')*+)';

    /**
     * @var array{
     *     const?: list<class-string>,
     *     class?: list<class-string>,
     *     function?: list<class-string>
     * }|null
     */
    private ?array $discoveredSymbols;

    /**
     * @var array{
     *     const?: array<string, class-string>,
     *     class?: array<string, class-string>,
     *     function?: array<string, class-string>
     * }
     */
    private array $symbolsForImport = [];

    /** @var array<string, string> */
    private array $cacheUsesLast = [];

    /** @var array<string, string> */
    private array $cacheUseNameByShortNameLower;

    /** @var array<string, string> */
    private array $cacheUseShortNameByNameLower;

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
            ...(\defined('T_ATTRIBUTE') ? [T_ATTRIBUTE] : []), // @TODO: drop condition when PHP 8.0+ is required
            T_CATCH,
            T_DOUBLE_COLON,
            T_DOC_COMMENT,
            T_EXTENDS,
            T_FUNCTION,
            T_IMPLEMENTS,
            T_INSTANCEOF,
            T_NEW,
            T_VARIABLE,
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

        $this->symbolsForImport = [];

        foreach ($tokens->getNamespaceDeclarations() as $namespaceIndex => $namespace) {
            $namespace = $tokens->getNamespaceDeclarations()[$namespaceIndex];

            $namespaceName = $namespace->getFullName();
            $uses = [];
            $lastUse = null;

            foreach ($namespaceUsesAnalyzer->getDeclarationsInNamespace($tokens, $namespace) as $use) {
                if (!$use->isClass()) {
                    continue;
                }
                $uses[ltrim($use->getFullName(), '\\')] = $use->getShortName();
                $lastUse = $use;
            }

            $indexDiff = 0;
            foreach (true === $this->configuration['import_symbols'] ? [true, false] : [false] as $discoverSymbolsPhase) {
                $this->discoveredSymbols = $discoverSymbolsPhase ? [] : null;

                $classyKinds = [T_CLASS, T_INTERFACE, T_TRAIT];
                if (\defined('T_ENUM')) { // @TODO: drop condition when PHP 8.1+ is required
                    $classyKinds[] = T_ENUM;
                }

                for ($index = $namespace->getScopeStartIndex(); $index < $namespace->getScopeEndIndex() + $indexDiff; ++$index) {
                    $origSize = \count($tokens);

                    if ($discoverSymbolsPhase && $tokens[$index]->isGivenKind($classyKinds)) {
                        $this->fixNextName($tokens, $index, $uses, $namespaceName);
                    } elseif ($tokens[$index]->isGivenKind(T_FUNCTION)) {
                        $this->fixFunction($functionsAnalyzer, $tokens, $index, $uses, $namespaceName);
                    } elseif ($tokens[$index]->isGivenKind([T_EXTENDS, T_IMPLEMENTS])) {
                        $this->fixExtendsImplements($tokens, $index, $uses, $namespaceName);
                    } elseif ($tokens[$index]->isGivenKind(T_CATCH)) {
                        $this->fixCatch($tokens, $index, $uses, $namespaceName);
                    } elseif ($tokens[$index]->isGivenKind(T_DOUBLE_COLON)) {
                        $this->fixPrevName($tokens, $index, $uses, $namespaceName);
                    } elseif ($tokens[$index]->isGivenKind([T_INSTANCEOF, T_NEW, CT::T_USE_TRAIT])) {
                        $this->fixNextName($tokens, $index, $uses, $namespaceName);
                    } elseif ($tokens[$index]->isGivenKind(T_VARIABLE)) {
                        $prevIndex = $tokens->getPrevMeaningfulToken($index);
                        if (null !== $prevIndex && $tokens[$prevIndex]->isGivenKind(T_STRING)) {
                            $this->fixPrevName($tokens, $index, $uses, $namespaceName);
                        }
                    } elseif (\defined('T_ATTRIBUTE') && $tokens[$index]->isGivenKind(T_ATTRIBUTE)) { // @TODO: drop const check when PHP 8.0+ is required
                        $this->fixNextName($tokens, $index, $uses, $namespaceName);
                    } elseif ($discoverSymbolsPhase && !\defined('T_ATTRIBUTE') && $tokens[$index]->isComment() && Preg::match('/#\[\s*('.self::REGEX_CLASS.')/', $tokens[$index]->getContent(), $matches)) { // @TODO: drop when PHP 8.0+ is required
                        $this->determineShortType($matches[1], $uses, $namespaceName);
                    } elseif ($tokens[$index]->isGivenKind(T_DOC_COMMENT)) {
                        $this->fixPhpDoc($tokens, $index, $uses, $namespaceName);
                    }

                    $indexDiff += \count($tokens) - $origSize;
                }

                if ($discoverSymbolsPhase) {
                    $this->setupUsesFromDiscoveredSymbols($uses, $namespaceName);
                }
            }

            if ([] !== $this->symbolsForImport) {
                if (null !== $lastUse) {
                    $atIndex = $lastUse->getEndIndex() + 1;
                } elseif (0 !== $namespace->getEndIndex()) {
                    $atIndex = $namespace->getEndIndex() + 1;
                } else {
                    $firstTokenIndex = $tokens->getNextMeaningfulToken($namespace->getScopeStartIndex());
                    if (null !== $firstTokenIndex && $tokens[$firstTokenIndex]->isGivenKind(T_DECLARE)) {
                        $atIndex = $tokens->getNextTokenOfKind($firstTokenIndex, [';']) + 1;
                    } else {
                        $atIndex = $namespace->getScopeStartIndex() + 1;
                    }
                }

                // Insert all registered FQCNs
                $this->createImportProcessor()->insertImports($tokens, $this->symbolsForImport, $atIndex);

                $this->symbolsForImport = [];
            }
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function refreshUsesCache(array $uses): void
    {
        if ($this->cacheUsesLast === $uses) {
            return;
        }

        $this->cacheUsesLast = $uses;
        $this->cacheUseNameByShortNameLower = [];
        $this->cacheUseShortNameByNameLower = [];
        foreach ($uses as $useLongName => $useShortName) {
            $this->cacheUseNameByShortNameLower[strtolower($useShortName)] = $useLongName;
            $this->cacheUseShortNameByNameLower[strtolower($useLongName)] = $useShortName;
        }
    }

    /**
     * Resolve absolute or relative symbol to normalized FQCN.
     *
     * @param array<string, string> $uses
     */
    private function resolveSymbol(string $symbol, array $uses, string $namespaceName): string
    {
        if (str_starts_with($symbol, '\\')) {
            return substr($symbol, 1);
        }

        $this->refreshUsesCache($uses);

        $symbolArr = explode('\\', $symbol, 2);
        $shortStartNameLower = strtolower($symbolArr[0]);
        if (isset($this->cacheUseNameByShortNameLower[$shortStartNameLower])) {
            return $this->cacheUseNameByShortNameLower[$shortStartNameLower].(isset($symbolArr[1]) ? '\\'.$symbolArr[1] : '');
        }

        return ('' !== $namespaceName ? $namespaceName.'\\' : '').$symbol;
    }

    /**
     * Shorten normalized FQCN as much as possible.
     *
     * @param array<string, string> $uses
     */
    private function shortenSymbol(string $fqcn, array $uses, string $namespaceName): string
    {
        $this->refreshUsesCache($uses);

        $res = null;

        // try to shorten the name using namespace
        $iMin = 0;
        if (str_starts_with($fqcn, $namespaceName.'\\')) {
            $tmpRes = substr($fqcn, \strlen($namespaceName) + 1);
            if (!isset($this->cacheUseNameByShortNameLower[strtolower(explode('\\', $tmpRes, 2)[0])])) {
                $res = $tmpRes;
                $iMin = substr_count($namespaceName, '\\') + 1;
            }
        }

        // try to shorten the name using uses
        $tmp = $fqcn;
        for ($i = substr_count($fqcn, '\\'); $i >= $iMin; --$i) {
            if (isset($this->cacheUseShortNameByNameLower[strtolower($tmp)])) {
                $res = $this->cacheUseShortNameByNameLower[strtolower($tmp)].substr($fqcn, \strlen($tmp));

                break;
            }

            if ($i > 0) {
                $tmp = substr($tmp, 0, strrpos($tmp, '\\'));
            }
        }

        // shortening is not possible, add leading backslash if needed
        if (null === $res) {
            $res = $fqcn;
            if ('' !== $namespaceName
                || true === $this->configuration['leading_backslash_in_global_namespace']
                || isset($this->cacheUseNameByShortNameLower[strtolower(explode('\\', $res, 2)[0])])
            ) {
                $res = '\\'.$res;
            }
        }

        return $res;
    }

    /**
     * @param array<string, string> $uses
     */
    private function setupUsesFromDiscoveredSymbols(array &$uses, string $namespaceName): void
    {
        foreach ($this->discoveredSymbols as $kind => $discoveredSymbols) {
            $discoveredFqcnByShortNameLower = [];

            if ('' === $namespaceName) {
                foreach ($discoveredSymbols as $symbol) {
                    if (!str_starts_with($symbol, '\\')) {
                        $shortStartName = explode('\\', ltrim($symbol, '\\'), 2)[0];
                        $shortStartNameLower = strtolower($shortStartName);
                        $discoveredFqcnByShortNameLower[$shortStartNameLower] = $this->resolveSymbol($shortStartName, $uses, $namespaceName);
                    }
                }
            }

            foreach ($uses as $useLongName => $useShortName) {
                $discoveredFqcnByShortNameLower[strtolower($useShortName)] = $useLongName;
            }

            uasort($discoveredSymbols, static fn ($a, $b) => substr_count($a, '\\') <=> substr_count($b, '\\'));
            foreach ($discoveredSymbols as $symbol) {
                $shortEndNameLower = strtolower(str_contains($symbol, '\\') ? substr($symbol, strrpos($symbol, '\\') + 1) : $symbol);
                if (!isset($discoveredFqcnByShortNameLower[$shortEndNameLower])) {
                    if ('' !== $namespaceName && !str_starts_with($symbol, '\\') && str_contains($symbol, '\\')) { // @TODO add option to force all classes to be imported
                        continue;
                    }

                    $discoveredFqcnByShortNameLower[$shortEndNameLower] = $this->resolveSymbol($symbol, $uses, $namespaceName);
                }
                // else short name collision - keep unimported
            }

            foreach ($uses as $useLongName => $useShortName) {
                $discoveredLongName = $discoveredFqcnByShortNameLower[strtolower($useShortName)] ?? null;
                if (strtolower($discoveredLongName) === strtolower($useLongName)) {
                    unset($discoveredFqcnByShortNameLower[strtolower($useShortName)]);
                }
            }

            foreach ($discoveredFqcnByShortNameLower as $fqcn) {
                $shortenedName = ltrim($this->shortenSymbol($fqcn, [], $namespaceName), '\\');
                if (str_contains($shortenedName, '\\')) { // prevent importing non-namespaced names in global namespace
                    $shortEndName = str_contains($fqcn, '\\') ? substr($fqcn, strrpos($fqcn, '\\') + 1) : $fqcn;
                    $uses[$fqcn] = $shortEndName;
                    $this->symbolsForImport[$kind][$shortEndName] = $fqcn;
                }
            }

            if (isset($this->symbolsForImport[$kind])) {
                ksort($this->symbolsForImport[$kind], SORT_NATURAL);
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
        $allowedTags = $this->configuration['phpdoc_tags'];

        if ([] === $allowedTags) {
            return;
        }

        $phpDoc = $tokens[$index];
        $phpDocContent = $phpDoc->getContent();
        $phpDocContentNew = Preg::replaceCallback('/([*{]\h*@)(\S+)(\h+)('.TypeExpression::REGEX_TYPES.')(?!(?!\})\S)/', function ($matches) use ($allowedTags, $uses, $namespaceName) {
            if (!\in_array($matches[2], $allowedTags, true)) {
                return $matches[0];
            }

            // @TODO parse the complex type using TypeExpression and fix all names inside (like `int|string` or `list<int|string>`)
            if (!Preg::match('/^[a-zA-Z0-9_\\\\]+(\|null)?$/', $matches[4])) {
                return $matches[0];
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
    private function fixExtendsImplements(Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        // We handle `extends` and `implements` with similar logic, but we need to exit the loop under different conditions.
        $isExtends = $tokens[$index]->equals([T_EXTENDS]);
        $index = $tokens->getNextMeaningfulToken($index);

        $typeStartIndex = null;
        $typeEndIndex = null;

        while (true) {
            if ($tokens[$index]->equalsAny([',', '{', [T_IMPLEMENTS]])) {
                if (null !== $typeStartIndex) {
                    $index += $this->shortenClassIfPossible($tokens, $typeStartIndex, $typeEndIndex, $uses, $namespaceName);
                }
                $typeStartIndex = null;

                if ($tokens[$index]->equalsAny($isExtends ? [[T_IMPLEMENTS], '{'] : ['{'])) {
                    break;
                }
            } else {
                if (null === $typeStartIndex) {
                    $typeStartIndex = $index;
                }
                $typeEndIndex = $index;
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

        $typeStartIndex = null;
        $typeEndIndex = null;

        while (true) {
            if ($tokens[$index]->equalsAny([')', [T_VARIABLE], [CT::T_TYPE_ALTERNATION]])) {
                if (null === $typeStartIndex) {
                    break;
                }

                $index += $this->shortenClassIfPossible($tokens, $typeStartIndex, $typeEndIndex, $uses, $namespaceName);
                $typeStartIndex = null;

                if ($tokens[$index]->equals(')')) {
                    break;
                }
            } else {
                if (null === $typeStartIndex) {
                    $typeStartIndex = $index;
                }
                $typeEndIndex = $index;
            }

            $index = $tokens->getNextMeaningfulToken($index);
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixPrevName(Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $typeStartIndex = null;
        $typeEndIndex = null;

        while (true) {
            $index = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$index]->isObjectOperator()) {
                break;
            }

            if ($tokens[$index]->equalsAny([[T_STRING], [T_NS_SEPARATOR]])) {
                $typeStartIndex = $index;
                if (null === $typeEndIndex) {
                    $typeEndIndex = $index;
                }
            } else {
                if (null !== $typeEndIndex) {
                    $index += $this->shortenClassIfPossible($tokens, $typeStartIndex, $typeEndIndex, $uses, $namespaceName);
                }

                break;
            }
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function fixNextName(Tokens $tokens, int $index, array $uses, string $namespaceName): void
    {
        $typeStartIndex = null;
        $typeEndIndex = null;

        while (true) {
            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->equalsAny([[T_STRING], [T_NS_SEPARATOR]])) {
                if (null === $typeStartIndex) {
                    $typeStartIndex = $index;
                }
                $typeEndIndex = $index;
            } else {
                if (null !== $typeStartIndex) {
                    $index += $this->shortenClassIfPossible($tokens, $typeStartIndex, $typeEndIndex, $uses, $namespaceName);
                }

                break;
            }
        }
    }

    /**
     * @param array<string, string> $uses
     */
    private function shortenClassIfPossible(Tokens $tokens, int $typeStartIndex, int $typeEndIndex, array $uses, string $namespaceName): int
    {
        $content = $tokens->generatePartialCode($typeStartIndex, $typeEndIndex);
        $newTokens = $this->determineShortType($content, $uses, $namespaceName);
        if (null === $newTokens) {
            return 0;
        }

        $tokens->overrideRange($typeStartIndex, $typeEndIndex, $newTokens);

        return \count($newTokens) - ($typeEndIndex - $typeStartIndex) - 1;
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

        foreach ($types as [$startIndex, $endIndex]) {
            $content = $tokens->generatePartialCode($startIndex, $endIndex);
            $newTokens = $this->determineShortType($content, $uses, $namespaceName);
            if (null !== $newTokens) {
                $tokens->overrideRange($startIndex, $endIndex, $newTokens);
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
        if ((new TypeAnalysis($typeName))->isReservedType()) {
            return null;
        }

        if (null !== $this->discoveredSymbols) {
            $this->discoveredSymbols['class'][] = $typeName;

            return null;
        }

        $fqcn = $this->resolveSymbol($typeName, $uses, $namespaceName);
        $shortenedType = $this->shortenSymbol($fqcn, $uses, $namespaceName);
        if ($shortenedType === $typeName) {
            return null;
        }

        return $this->namespacedStringToTokens($shortenedType);
    }

    /**
     * @return iterable<array{int, int}>
     */
    private function getTypes(Tokens $tokens, int $index, int $endIndex): iterable
    {
        $skipNextYield = false;
        $typeStartIndex = $typeEndIndex = null;
        while (true) {
            if ($tokens[$index]->isGivenKind(CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN)) {
                $index = $tokens->getNextMeaningfulToken($index);
                $typeStartIndex = $typeEndIndex = null;

                continue;
            }

            if (
                $tokens[$index]->isGivenKind([CT::T_TYPE_ALTERNATION, CT::T_TYPE_INTERSECTION, CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE])
                || $index > $endIndex
            ) {
                if (!$skipNextYield && null !== $typeStartIndex) {
                    $origCount = \count($tokens);

                    yield [$typeStartIndex, $typeEndIndex];

                    $endIndex += \count($tokens) - $origCount;

                    // type tokens were possibly updated, restart type match
                    $skipNextYield = true;
                    $index = $typeEndIndex = $typeStartIndex;
                } else {
                    $skipNextYield = false;
                    $index = $tokens->getNextMeaningfulToken($index);
                    $typeStartIndex = $typeEndIndex = null;
                }

                if ($index > $endIndex) {
                    break;
                }

                continue;
            }

            if (null === $typeStartIndex) {
                $typeStartIndex = $index;
            }
            $typeEndIndex = $index;

            $index = $tokens->getNextMeaningfulToken($index);
        }
    }

    /**
     * @return list<Token>
     */
    private function namespacedStringToTokens(string $input): array
    {
        $tokens = [];

        if (str_starts_with($input, '\\')) {
            $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            $input = substr($input, 1);
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
}
