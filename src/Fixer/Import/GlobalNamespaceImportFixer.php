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
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\ClassyAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class GlobalNamespaceImportFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Imports or fully qualifies global classes/functions/constants.',
            [
                new CodeSample(
                    '<?php

namespace Foo;

$d = new \DateTimeImmutable();
'
                ),
                new CodeSample(
                    '<?php

namespace Foo;

if (\count($x)) {
    /** @var \DateTimeImmutable $d */
    $d = new \DateTimeImmutable();
    $p = \M_PI;
}
',
                    ['import_classes' => true, 'import_constants' => true, 'import_functions' => true]
                ),
                new CodeSample(
                    '<?php

namespace Foo;

use DateTimeImmutable;
use function count;
use const M_PI;

if (count($x)) {
    /** @var DateTimeImmutable $d */
    $d = new DateTimeImmutable();
    $p = M_PI;
}
',
                    ['import_classes' => false, 'import_constants' => false, 'import_functions' => false]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoUnusedImportsFixer, OrderedImportsFixer.
     * Must run after NativeConstantInvocationFixer, NativeFunctionInvocationFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_DOC_COMMENT, T_NS_SEPARATOR, T_USE])
            && $tokens->isTokenKindFound(T_NAMESPACE)
            && 1 === $tokens->countTokenKind(T_NAMESPACE)
            && $tokens->isMonolithicPhp();
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $namespaceAnalyses = $tokens->getNamespaceDeclarations();

        if (1 !== \count($namespaceAnalyses) || $namespaceAnalyses[0]->isGlobalNamespace()) {
            return;
        }

        $useDeclarations = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens);

        $newImports = [];

        if (true === $this->configuration['import_constants']) {
            $newImports['const'] = $this->importConstants($tokens, $useDeclarations);
        } elseif (false === $this->configuration['import_constants']) {
            $this->fullyQualifyConstants($tokens, $useDeclarations);
        }

        if (true === $this->configuration['import_functions']) {
            $newImports['function'] = $this->importFunctions($tokens, $useDeclarations);
        } elseif (false === $this->configuration['import_functions']) {
            $this->fullyQualifyFunctions($tokens, $useDeclarations);
        }

        if (true === $this->configuration['import_classes']) {
            $newImports['class'] = $this->importClasses($tokens, $useDeclarations);
        } elseif (false === $this->configuration['import_classes']) {
            $this->fullyQualifyClasses($tokens, $useDeclarations);
        }

        $newImports = array_filter($newImports);

        if (\count($newImports) > 0) {
            $this->insertImports($tokens, $newImports, $useDeclarations);
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('import_constants', 'Whether to import, not import or ignore global constants.'))
                ->setDefault(null)
                ->setAllowedValues([true, false, null])
                ->getOption(),
            (new FixerOptionBuilder('import_functions', 'Whether to import, not import or ignore global functions.'))
                ->setDefault(null)
                ->setAllowedValues([true, false, null])
                ->getOption(),
            (new FixerOptionBuilder('import_classes', 'Whether to import, not import or ignore global classes.'))
                ->setDefault(true)
                ->setAllowedValues([true, false, null])
                ->getOption(),
        ]);
    }

    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     *
     * @return array<string, string>
     */
    private function importConstants(Tokens $tokens, array $useDeclarations): array
    {
        [$global, $other] = $this->filterUseDeclarations($useDeclarations, static fn (NamespaceUseAnalysis $declaration): bool => $declaration->isConstant(), true);

        // find namespaced const declarations (`const FOO = 1`)
        // and add them to the not importable names (already used)
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            $token = $tokens[$index];

            if ($token->isClassy()) {
                $index = $tokens->getNextTokenOfKind($index, ['{']);
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            if (!$token->isGivenKind(T_CONST)) {
                continue;
            }

            $index = $tokens->getNextMeaningfulToken($index);
            $other[$tokens[$index]->getContent()] = true;
        }

        $analyzer = new TokensAnalyzer($tokens);
        $indices = [];

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $name = $token->getContent();

            if (isset($other[$name])) {
                continue;
            }

            if (!$analyzer->isConstantInvocation($index)) {
                continue;
            }

            $nsSeparatorIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$nsSeparatorIndex]->isGivenKind(T_NS_SEPARATOR)) {
                if (!isset($global[$name])) {
                    // found an unqualified constant invocation
                    // add it to the not importable names (already used)
                    $other[$name] = true;
                }

                continue;
            }

            $prevIndex = $tokens->getPrevMeaningfulToken($nsSeparatorIndex);
            if ($tokens[$prevIndex]->isGivenKind([CT::T_NAMESPACE_OPERATOR, T_STRING])) {
                continue;
            }

            $indices[] = $index;
        }

        return $this->prepareImports($tokens, $indices, $global, $other, true);
    }

    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     *
     * @return array<string, string>
     */
    private function importFunctions(Tokens $tokens, array $useDeclarations): array
    {
        [$global, $other] = $this->filterUseDeclarations($useDeclarations, static fn (NamespaceUseAnalysis $declaration): bool => $declaration->isFunction(), false);

        // find function declarations
        // and add them to the not importable names (already used)
        foreach ($this->findFunctionDeclarations($tokens, 0, $tokens->count() - 1) as $name) {
            $other[strtolower($name)] = true;
        }

        $analyzer = new FunctionsAnalyzer();
        $indices = [];

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $name = strtolower($token->getContent());

            if (isset($other[$name])) {
                continue;
            }

            if (!$analyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }

            $nsSeparatorIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$nsSeparatorIndex]->isGivenKind(T_NS_SEPARATOR)) {
                if (!isset($global[$name])) {
                    $other[$name] = true;
                }

                continue;
            }

            $indices[] = $index;
        }

        return $this->prepareImports($tokens, $indices, $global, $other, false);
    }

    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     *
     * @return array<string, string>
     */
    private function importClasses(Tokens $tokens, array $useDeclarations): array
    {
        [$global, $other] = $this->filterUseDeclarations($useDeclarations, static fn (NamespaceUseAnalysis $declaration): bool => $declaration->isClass(), false);

        /** @var DocBlock[] $docBlocks */
        $docBlocks = [];

        // find class declarations and class usages in docblocks
        // and add them to the not importable names (already used)
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_DOC_COMMENT)) {
                $docBlocks[$index] = new DocBlock($token->getContent());

                $this->traverseDocBlockTypes($docBlocks[$index], static function (string $type) use ($global, &$other): void {
                    if (str_contains($type, '\\')) {
                        return;
                    }

                    $name = strtolower($type);

                    if (!isset($global[$name])) {
                        $other[$name] = true;
                    }
                });
            }

            if (!$token->isClassy()) {
                continue;
            }

            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->isGivenKind(T_STRING)) {
                $other[strtolower($tokens[$index]->getContent())] = true;
            }
        }

        $analyzer = new ClassyAnalyzer();
        $indices = [];

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $name = strtolower($token->getContent());

            if (isset($other[$name])) {
                continue;
            }

            if (!$analyzer->isClassyInvocation($tokens, $index)) {
                continue;
            }

            $nsSeparatorIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$nsSeparatorIndex]->isGivenKind(T_NS_SEPARATOR)) {
                if (!isset($global[$name])) {
                    $other[$name] = true;
                }

                continue;
            }

            if ($tokens[$tokens->getPrevMeaningfulToken($nsSeparatorIndex)]->isGivenKind([CT::T_NAMESPACE_OPERATOR, T_STRING])) {
                continue;
            }

            $indices[] = $index;
        }

        $imports = [];

        foreach ($docBlocks as $index => $docBlock) {
            $changed = $this->traverseDocBlockTypes($docBlock, static function (string $type) use ($global, $other, &$imports): string {
                if ('\\' !== $type[0]) {
                    return $type;
                }

                $name = substr($type, 1);
                $checkName = strtolower($name);

                if (str_contains($checkName, '\\') || isset($other[$checkName])) {
                    return $type;
                }

                if (isset($global[$checkName])) {
                    return \is_string($global[$checkName]) ? $global[$checkName] : $name;
                }

                $imports[$checkName] = $name;

                return $name;
            });

            if ($changed) {
                $tokens[$index] = new Token([T_DOC_COMMENT, $docBlock->getContent()]);
            }
        }

        return $imports + $this->prepareImports($tokens, $indices, $global, $other, false);
    }

    /**
     * Removes the leading slash at the given indices (when the name is not already used).
     *
     * @param int[]               $indices
     * @param array<string, true> $other
     *
     * @return array<string, string> array keys contain the names that must be imported
     */
    private function prepareImports(Tokens $tokens, array $indices, array $global, array $other, bool $caseSensitive): array
    {
        $imports = [];

        foreach ($indices as $index) {
            $name = $tokens[$index]->getContent();
            $checkName = $caseSensitive ? $name : strtolower($name);

            if (isset($other[$checkName])) {
                continue;
            }

            if (!isset($global[$checkName])) {
                $imports[$checkName] = $name;
            } elseif (\is_string($global[$checkName])) {
                $tokens[$index] = new Token([T_STRING, $global[$checkName]]);
            }

            $tokens->clearAt($tokens->getPrevMeaningfulToken($index));
        }

        return $imports;
    }

    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function insertImports(Tokens $tokens, array $imports, array $useDeclarations): void
    {
        if (\count($useDeclarations) > 0) {
            $useDeclaration = end($useDeclarations);
            $index = $useDeclaration->getEndIndex() + 1;
        } else {
            $namespace = $tokens->getNamespaceDeclarations()[0];
            $index = $namespace->getEndIndex() + 1;
        }

        $lineEnding = $this->whitespacesConfig->getLineEnding();

        if (!$tokens[$index]->isWhitespace() || !str_contains($tokens[$index]->getContent(), "\n")) {
            $tokens->insertAt($index, new Token([T_WHITESPACE, $lineEnding]));
        }

        foreach ($imports as $type => $typeImports) {
            foreach ($typeImports as $name) {
                $items = [
                    new Token([T_WHITESPACE, $lineEnding]),
                    new Token([T_USE, 'use']),
                    new Token([T_WHITESPACE, ' ']),
                ];

                if ('const' === $type) {
                    $items[] = new Token([CT::T_CONST_IMPORT, 'const']);
                    $items[] = new Token([T_WHITESPACE, ' ']);
                } elseif ('function' === $type) {
                    $items[] = new Token([CT::T_FUNCTION_IMPORT, 'function']);
                    $items[] = new Token([T_WHITESPACE, ' ']);
                }

                $items[] = new Token([T_STRING, $name]);
                $items[] = new Token(';');

                $tokens->insertAt($index, $items);
            }
        }
    }

    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function fullyQualifyConstants(Tokens $tokens, array $useDeclarations): void
    {
        if (!$tokens->isTokenKindFound(CT::T_CONST_IMPORT)) {
            return;
        }

        [$global] = $this->filterUseDeclarations($useDeclarations, static fn (NamespaceUseAnalysis $declaration): bool => $declaration->isConstant() && !$declaration->isAliased(), true);

        if (!$global) {
            return;
        }

        $analyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            if (!isset($global[$token->getContent()])) {
                continue;
            }

            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            if (!$analyzer->isConstantInvocation($index)) {
                continue;
            }

            $tokens->insertAt($index, new Token([T_NS_SEPARATOR, '\\']));
        }
    }

    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function fullyQualifyFunctions(Tokens $tokens, array $useDeclarations): void
    {
        if (!$tokens->isTokenKindFound(CT::T_FUNCTION_IMPORT)) {
            return;
        }

        [$global] = $this->filterUseDeclarations($useDeclarations, static fn (NamespaceUseAnalysis $declaration): bool => $declaration->isFunction() && !$declaration->isAliased(), false);

        if (!$global) {
            return;
        }

        $analyzer = new FunctionsAnalyzer();

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            if (!isset($global[strtolower($token->getContent())])) {
                continue;
            }

            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            if (!$analyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }

            $tokens->insertAt($index, new Token([T_NS_SEPARATOR, '\\']));
        }
    }

    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function fullyQualifyClasses(Tokens $tokens, array $useDeclarations): void
    {
        if (!$tokens->isTokenKindFound(T_USE)) {
            return;
        }

        [$global] = $this->filterUseDeclarations($useDeclarations, static fn (NamespaceUseAnalysis $declaration): bool => $declaration->isClass() && !$declaration->isAliased(), false);

        if (!$global) {
            return;
        }

        $analyzer = new ClassyAnalyzer();

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_DOC_COMMENT)) {
                $doc = new DocBlock($token->getContent());

                $changed = $this->traverseDocBlockTypes($doc, static function (string $type) use ($global): string {
                    if (!isset($global[strtolower($type)])) {
                        return $type;
                    }

                    return '\\'.$type;
                });

                if ($changed) {
                    $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
                }

                continue;
            }

            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            if (!isset($global[strtolower($token->getContent())])) {
                continue;
            }

            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            if (!$analyzer->isClassyInvocation($tokens, $index)) {
                continue;
            }

            $tokens->insertAt($index, new Token([T_NS_SEPARATOR, '\\']));
        }
    }

    /**
     * @param NamespaceUseAnalysis[] $declarations
     */
    private function filterUseDeclarations(array $declarations, callable $callback, bool $caseSensitive): array
    {
        $global = [];
        $other = [];

        foreach ($declarations as $declaration) {
            if (!$callback($declaration)) {
                continue;
            }

            $fullName = ltrim($declaration->getFullName(), '\\');

            if (str_contains($fullName, '\\')) {
                $name = $caseSensitive ? $declaration->getShortName() : strtolower($declaration->getShortName());
                $other[$name] = true;

                continue;
            }

            $checkName = $caseSensitive ? $fullName : strtolower($fullName);
            $alias = $declaration->getShortName();
            $global[$checkName] = $alias === $fullName ? true : $alias;
        }

        return [$global, $other];
    }

    /**
     * @return iterable<string>
     */
    private function findFunctionDeclarations(Tokens $tokens, int $start, int $end): iterable
    {
        for ($index = $start; $index <= $end; ++$index) {
            $token = $tokens[$index];

            if ($token->isClassy()) {
                $classStart = $tokens->getNextTokenOfKind($index, ['{']);
                $classEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classStart);

                for ($index = $classStart; $index <= $classEnd; ++$index) {
                    if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                        continue;
                    }

                    $methodStart = $tokens->getNextTokenOfKind($index, ['{', ';']);

                    if ($tokens[$methodStart]->equals(';')) {
                        $index = $methodStart;

                        continue;
                    }

                    $methodEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $methodStart);

                    foreach ($this->findFunctionDeclarations($tokens, $methodStart, $methodEnd) as $function) {
                        yield $function;
                    }

                    $index = $methodEnd;
                }

                continue;
            }

            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->isGivenKind(CT::T_RETURN_REF)) {
                $index = $tokens->getNextMeaningfulToken($index);
            }

            if ($tokens[$index]->isGivenKind(T_STRING)) {
                yield $tokens[$index]->getContent();
            }
        }
    }

    private function traverseDocBlockTypes(DocBlock $doc, callable $callback): bool
    {
        $annotations = $doc->getAnnotationsOfType(Annotation::getTagsWithTypes());

        if (0 === \count($annotations)) {
            return false;
        }

        $changed = false;

        foreach ($annotations as $annotation) {
            $types = $new = $annotation->getTypes();

            foreach ($types as $i => $fullType) {
                $newFullType = $fullType;

                Preg::matchAll('/[\\\\\w]+/', $fullType, $matches, PREG_OFFSET_CAPTURE);

                foreach (array_reverse($matches[0]) as [$type, $offset]) {
                    $newType = $callback($type);

                    if (null !== $newType && $type !== $newType) {
                        $newFullType = substr_replace($newFullType, $newType, $offset, \strlen($type));
                    }
                }

                $new[$i] = $newFullType;
            }

            if ($types !== $new) {
                $annotation->setTypes($new);
                $changed = true;
            }
        }

        return $changed;
    }
}
