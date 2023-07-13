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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

final class NoSuperfluousPhpdocTagsFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const NO_TYPE_INFO = [
        'types' => [],
        'allows_null' => true,
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes `@param`, `@return` and `@var` tags that don\'t provide any useful information.',
            [
                new CodeSample('<?php
class Foo {
    /**
     * @param Bar $bar
     * @param mixed $baz
     *
     * @return Baz
     */
    public function doFoo(Bar $bar, $baz): Baz {}
}
'),
                new CodeSample('<?php
class Foo {
    /**
     * @param Bar $bar
     * @param mixed $baz
     */
    public function doFoo(Bar $bar, $baz) {}
}
', ['allow_mixed' => true]),
                new CodeSample('<?php
class Foo {
    /**
     * @inheritDoc
     */
    public function doFoo(Bar $bar, $baz) {}
}
', ['remove_inheritdoc' => true]),
                new CodeSample('<?php
class Foo {
    /**
     * @param Bar $bar
     * @param mixed $baz
     * @param string|int|null $qux
     */
    public function doFoo(Bar $bar, $baz /*, $qux = null */) {}
}
', ['allow_unused_params' => true]),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, PhpdocAlignFixer, VoidReturnFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, FullyQualifiedStrictTypesFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocIndentFixer, PhpdocLineSpanFixer, PhpdocReturnSelfReferenceFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocToParamTypeFixer, PhpdocToPropertyTypeFixer, PhpdocToReturnTypeFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 6;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        $namespaceUseAnalyzer = new NamespaceUsesAnalyzer();
        $shortNames = [];
        $currentSymbol = null;
        $currentSymbolEndIndex = null;

        foreach ($namespaceUseAnalyzer->getDeclarationsFromTokens($tokens) as $namespaceUseAnalysis) {
            $shortNames[strtolower($namespaceUseAnalysis->getShortName())] = '\\'.strtolower($namespaceUseAnalysis->getFullName());
        }

        $symbolKinds = [T_CLASS, T_INTERFACE];
        if (\defined('T_ENUM')) { // @TODO drop the condition when requiring PHP 8.1+
            $symbolKinds[] = T_ENUM;
        }

        foreach ($tokens as $index => $token) {
            if ($index === $currentSymbolEndIndex) {
                $currentSymbol = null;
                $currentSymbolEndIndex = null;

                continue;
            }

            if ($token->isGivenKind(T_CLASS) && $tokensAnalyzer->isAnonymousClass($index)) {
                continue;
            }

            if ($token->isGivenKind($symbolKinds)) {
                $currentSymbol = $tokens[$tokens->getNextMeaningfulToken($index)]->getContent();
                $currentSymbolEndIndex = $tokens->findBlockEnd(
                    Tokens::BLOCK_TYPE_CURLY_BRACE,
                    $tokens->getNextTokenOfKind($index, ['{']),
                );

                continue;
            }

            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $documentedElement = $this->findDocumentedElement($tokens, $index);

            if (null === $documentedElement) {
                continue;
            }

            $content = $initialContent = $token->getContent();

            if (true === $this->configuration['remove_inheritdoc']) {
                $content = $this->removeSuperfluousInheritDoc($content);
            }

            if ('function' === $documentedElement['type']) {
                $content = $this->fixFunctionDocComment($content, $tokens, $documentedElement, $currentSymbol, $shortNames);
            } elseif ('property' === $documentedElement['type']) {
                $content = $this->fixPropertyDocComment($content, $tokens, $documentedElement, $currentSymbol, $shortNames);
            } elseif ('classy' === $documentedElement['type']) {
                $content = $this->fixClassDocComment($content, $documentedElement);
            } else {
                throw new \RuntimeException('Unknown type.');
            }

            if ('' === $content) {
                $content = '/**  */';
            }

            if ($content !== $initialContent) {
                $tokens[$index] = new Token([T_DOC_COMMENT, $content]);
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('allow_mixed', 'Whether type `mixed` without description is allowed (`true`) or considered superfluous (`false`).'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('remove_inheritdoc', 'Remove `@inheritDoc` tags.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('allow_unused_params', 'Whether `param` annotation without actual signature is allowed (`true`) or considered superfluous (`false`).'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    /**
     * @return null|array{
     *       index: int,
     *       type: 'classy'|'function'|'property',
     *       modifiers: array<int, Token>,
     *       types: array<int, Token>,
     * }
     */
    private function findDocumentedElement(Tokens $tokens, int $docCommentIndex): ?array
    {
        $modifierKinds = [
            T_PRIVATE,
            T_PROTECTED,
            T_PUBLIC,
            T_ABSTRACT,
            T_FINAL,
            T_STATIC,
        ];

        $typeKinds = [
            CT::T_NULLABLE_TYPE,
            CT::T_ARRAY_TYPEHINT,
            CT::T_TYPE_ALTERNATION,
            CT::T_TYPE_INTERSECTION,
            T_STRING,
            T_NS_SEPARATOR,
        ];

        if (\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.1+ is required
            $modifierKinds[] = T_READONLY;
        }

        $element = [
            'modifiers' => [],
            'types' => [],
        ];

        $index = $tokens->getNextMeaningfulToken($docCommentIndex);

        // @TODO: drop condition when PHP 8.0+ is required
        if (null !== $index && \defined('T_ATTRIBUTE') && $tokens[$index]->isGivenKind(T_ATTRIBUTE)) {
            do {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ATTRIBUTE, $index);
                $index = $tokens->getNextMeaningfulToken($index);
            } while (null !== $index && $tokens[$index]->isGivenKind(T_ATTRIBUTE));
        }

        while (true) {
            if (null === $index) {
                break;
            }

            if ($tokens[$index]->isClassy()) {
                $element['index'] = $index;
                $element['type'] = 'classy';

                return $element;
            }

            if ($tokens[$index]->isGivenKind(T_FUNCTION)) {
                $element['index'] = $index;
                $element['type'] = 'function';

                return $element;
            }

            if ($tokens[$index]->isGivenKind(T_VARIABLE)) {
                $element['index'] = $index;
                $element['type'] = 'property';

                return $element;
            }

            if ($tokens[$index]->isGivenKind($modifierKinds)) {
                $element['modifiers'][$index] = $tokens[$index];
            } elseif ($tokens[$index]->isGivenKind($typeKinds)) {
                $element['types'][$index] = $tokens[$index];
            } else {
                break;
            }

            $index = $tokens->getNextMeaningfulToken($index);
        }

        return null;
    }

    /**
     * @param array{
     *       index: int,
     *       type: 'function',
     *       modifiers: array<int, Token>,
     *       types: array<int, Token>,
     * } $element
     * @param array<string, string> $shortNames
     */
    private function fixFunctionDocComment(
        string $content,
        Tokens $tokens,
        array $element,
        ?string $currentSymbol,
        array $shortNames
    ): string {
        $docBlock = new DocBlock($content);

        $openingParenthesisIndex = $tokens->getNextTokenOfKind($element['index'], ['(']);
        $closingParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openingParenthesisIndex);

        $argumentsInfo = $this->getArgumentsInfo(
            $tokens,
            $openingParenthesisIndex + 1,
            $closingParenthesisIndex - 1
        );

        foreach ($docBlock->getAnnotationsOfType('param') as $annotation) {
            $argumentName = $annotation->getVariableName();

            if (null === $argumentName) {
                if ($this->annotationIsSuperfluous($annotation, self::NO_TYPE_INFO, $currentSymbol, $shortNames)) {
                    $annotation->remove();
                }

                continue;
            }

            if (!isset($argumentsInfo[$argumentName]) && true === $this->configuration['allow_unused_params']) {
                continue;
            }

            if (!isset($argumentsInfo[$argumentName]) || $this->annotationIsSuperfluous($annotation, $argumentsInfo[$argumentName], $currentSymbol, $shortNames)) {
                $annotation->remove();
            }
        }

        $returnTypeInfo = $this->getReturnTypeInfo($tokens, $closingParenthesisIndex);

        foreach ($docBlock->getAnnotationsOfType('return') as $annotation) {
            if ($this->annotationIsSuperfluous($annotation, $returnTypeInfo, $currentSymbol, $shortNames)) {
                $annotation->remove();
            }
        }

        $this->removeSuperfluousModifierAnnotation($docBlock, $element);

        return $docBlock->getContent();
    }

    /**
     * @param array{
     *       index: int,
     *       type: 'property',
     *       modifiers: array<int, Token>,
     *       types: array<int, Token>,
     * } $element
     * @param array<string, string> $shortNames
     */
    private function fixPropertyDocComment(
        string $content,
        Tokens $tokens,
        array $element,
        ?string $currentSymbol,
        array $shortNames
    ): string {
        if (\count($element['types']) > 0) {
            $propertyTypeInfo = $this->parseTypeHint($tokens, array_key_first($element['types']));
        } else {
            $propertyTypeInfo = self::NO_TYPE_INFO;
        }

        $docBlock = new DocBlock($content);

        foreach ($docBlock->getAnnotationsOfType('var') as $annotation) {
            if ($this->annotationIsSuperfluous($annotation, $propertyTypeInfo, $currentSymbol, $shortNames)) {
                $annotation->remove();
            }
        }

        return $docBlock->getContent();
    }

    /**
     * @param array{
     *       index: int,
     *       type: 'classy',
     *       modifiers: array<int, Token>,
     *       types: array<int, Token>,
     * } $element
     */
    private function fixClassDocComment(string $content, array $element): string
    {
        $docBlock = new DocBlock($content);

        $this->removeSuperfluousModifierAnnotation($docBlock, $element);

        return $docBlock->getContent();
    }

    /**
     * @return array<string, array{types: list<string>, allows_null: bool}>
     */
    private function getArgumentsInfo(Tokens $tokens, int $start, int $end): array
    {
        $argumentsInfo = [];

        for ($index = $start; $index <= $end; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $beforeArgumentIndex = $tokens->getPrevTokenOfKind($index, ['(', ',']);
            $typeIndex = $tokens->getNextMeaningfulToken($beforeArgumentIndex);

            if ($typeIndex !== $index) {
                $info = $this->parseTypeHint($tokens, $typeIndex);
            } else {
                $info = self::NO_TYPE_INFO;
            }

            if (!$info['allows_null']) {
                $nextIndex = $tokens->getNextMeaningfulToken($index);
                if (
                    $tokens[$nextIndex]->equals('=')
                    && $tokens[$tokens->getNextMeaningfulToken($nextIndex)]->equals([T_STRING, 'null'], false)
                ) {
                    $info['allows_null'] = true;
                }
            }

            $argumentsInfo[$token->getContent()] = $info;
        }

        return $argumentsInfo;
    }

    /**
     * @return array{types: list<string>, allows_null: bool}
     */
    private function getReturnTypeInfo(Tokens $tokens, int $closingParenthesisIndex): array
    {
        $colonIndex = $tokens->getNextMeaningfulToken($closingParenthesisIndex);

        return $tokens[$colonIndex]->isGivenKind(CT::T_TYPE_COLON)
            ? $this->parseTypeHint($tokens, $tokens->getNextMeaningfulToken($colonIndex))
            : self::NO_TYPE_INFO;
    }

    /**
     * @param int $index The index of the first token of the type hint
     *
     * @return array{types: list<string>, allows_null: bool}
     */
    private function parseTypeHint(Tokens $tokens, int $index): array
    {
        $allowsNull = false;

        $types = [];

        while (true) {
            $type = '';

            if (\defined('T_READONLY') && $tokens[$index]->isGivenKind(T_READONLY)) { // @TODO: simplify condition when PHP 8.1+ is required
                $index = $tokens->getNextMeaningfulToken($index);
            }

            if ($tokens[$index]->isGivenKind([CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE])) {
                $index = $tokens->getNextMeaningfulToken($index);

                continue;
            }

            if ($tokens[$index]->isGivenKind(CT::T_NULLABLE_TYPE)) {
                $allowsNull = true;
                $index = $tokens->getNextMeaningfulToken($index);
            }

            while ($tokens[$index]->isGivenKind([T_NS_SEPARATOR, T_STATIC, T_STRING, CT::T_ARRAY_TYPEHINT, T_CALLABLE])) {
                $type .= $tokens[$index]->getContent();
                $index = $tokens->getNextMeaningfulToken($index);
            }

            if ('' === $type) {
                break;
            }

            $types[] = $type;

            if (!$tokens[$index]->isGivenKind([CT::T_TYPE_ALTERNATION, CT::T_TYPE_INTERSECTION])) {
                break;
            }

            $index = $tokens->getNextMeaningfulToken($index);
        }

        return [
            'types' => $types,
            'allows_null' => $allowsNull,
        ];
    }

    /**
     * @param array<string, string> $symbolShortNames
     */
    private function annotationIsSuperfluous(
        Annotation $annotation,
        array $info,
        ?string $currentSymbol,
        array $symbolShortNames
    ): bool {
        if ('param' === $annotation->getTag()->getName()) {
            $regex = '{@param(?:\s+'.TypeExpression::REGEX_TYPES.')?(?:\s+(?:\&\s*)?(?:\.{3}\s*)?\$\S+)?(?:\s+(?<description>(?!\*+\/)\S+))?}s';
        } elseif ('var' === $annotation->getTag()->getName()) {
            $regex = '{@var(?:\s+'.TypeExpression::REGEX_TYPES.')?(?:\s+\$\S+)?(?:\s+(?<description>(?!\*\/)\S+))?}s';
        } else {
            $regex = '{@return(?:\s+'.TypeExpression::REGEX_TYPES.')?(?:\s+(?<description>(?!\*\/)\S+))?}s';
        }

        if (!Preg::match($regex, $annotation->getContent(), $matches)) {
            // Unable to match the annotation, it must be malformed or has unsupported format.
            // Either way we don't want to tinker with it.
            return false;
        }

        if (isset($matches['description'])) {
            return false;
        }

        if (!isset($matches['types']) || '' === $matches['types']) {
            // If there's no type info in the annotation, further checks make no sense, exit early.
            return true;
        }

        $annotationTypes = $this->toComparableNames($annotation->getTypes(), $currentSymbol, $symbolShortNames);

        if (['null'] === $annotationTypes) {
            return false;
        }

        if (['mixed'] === $annotationTypes && [] === $info['types']) {
            return false === $this->configuration['allow_mixed'];
        }

        $actualTypes = $info['types'];

        if ($info['allows_null']) {
            $actualTypes[] = 'null';
        }

        return $annotationTypes === $this->toComparableNames($actualTypes, $currentSymbol, $symbolShortNames);
    }

    /**
     * Normalizes types to make them comparable.
     *
     * Converts given types to lowercase, replaces imports aliases with
     * their matching FQCN, and finally sorts the result.
     *
     * @param string[]              $types            The types to normalize
     * @param array<string, string> $symbolShortNames The imports aliases
     *
     * @return array The normalized types
     */
    private function toComparableNames(array $types, ?string $currentSymbol, array $symbolShortNames): array
    {
        $normalized = array_map(
            static function (string $type) use ($currentSymbol, $symbolShortNames): string {
                if ('self' === $type && null !== $currentSymbol) {
                    $type = $currentSymbol;
                }

                $type = strtolower($type);

                if (str_contains($type, '&')) {
                    $intersects = explode('&', $type);
                    sort($intersects);

                    return implode('&', $intersects);
                }

                return $symbolShortNames[$type] ?? $type;
            },
            $types
        );

        sort($normalized);

        return $normalized;
    }

    private function removeSuperfluousInheritDoc(string $docComment): string
    {
        return Preg::replace('~
            # $1: before @inheritDoc tag
            (
                # beginning of comment or a PHPDoc tag
                (?:
                    ^/\*\*
                    (?:
                        \R
                        [ \t]*(?:\*[ \t]*)?
                    )*?
                    |
                    @\N+
                )

                # empty comment lines
                (?:
                    \R
                    [ \t]*(?:\*[ \t]*?)?
                )*
            )

            # spaces before @inheritDoc tag
            [ \t]*

            # @inheritDoc tag
            (?:@inheritDocs?|\{@inheritDocs?\})

            # $2: after @inheritDoc tag
            (
                # empty comment lines
                (?:
                    \R
                    [ \t]*(?:\*[ \t]*)?
                )*

                # a PHPDoc tag or end of comment
                (?:
                    @\N+
                    |
                    (?:
                        \R
                        [ \t]*(?:\*[ \t]*)?
                    )*
                    [ \t]*\*/$
                )
            )
        ~ix', '$1$2', $docComment);
    }

    private function removeSuperfluousModifierAnnotation(DocBlock $docBlock, array $element): void
    {
        foreach (['abstract' => T_ABSTRACT, 'final' => T_FINAL] as $annotationType => $modifierToken) {
            $annotations = $docBlock->getAnnotationsOfType($annotationType);

            foreach ($element['modifiers'] as $token) {
                if ($token->isGivenKind($modifierToken)) {
                    foreach ($annotations as $annotation) {
                        $annotation->remove();
                    }
                }
            }
        }
    }
}
