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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractPhpdocToTypeDeclarationFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jan Gantzert <jan@familie-gantzert.de>
 */
final class PhpdocToParamTypeFixer extends AbstractPhpdocToTypeDeclarationFixer
{
    /**
     * @var array{int, string}[]
     */
    private const EXCLUDE_FUNC_NAMES = [
        [T_STRING, '__clone'],
        [T_STRING, '__destruct'],
    ];

    /**
     * @var array<string, true>
     */
    private const SKIPPED_TYPES = [
        'mixed' => true,
        'resource' => true,
        'static' => true,
        'void' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'EXPERIMENTAL: Takes `@param` annotations of non-mixed types and adjusts accordingly the function signature. Requires PHP >= 7.0.',
            [
                new CodeSample(
                    '<?php

/**
 * @param string $foo
 * @param string|null $bar
 */
function f($foo, $bar)
{}
'
                ),
                new CodeSample(
                    '<?php

/** @param Foo $foo */
function foo($foo) {}
/** @param string $foo */
function bar($foo) {}
',
                    ['scalar_types' => false]
                ),
            ],
            null,
            'This rule is EXPERIMENTAL and [1] is not covered with backward compatibility promise. [2] `@param` annotation is mandatory for the fixer to make changes, signatures of methods without it (no docblock, inheritdocs) will not be fixed. [3] Manual actions are required if inherited signatures are not properly documented.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoSuperfluousPhpdocTagsFixer, PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 8;
    }

    protected function isSkippedType(string $type): bool
    {
        return isset(self::SKIPPED_TYPES[$type]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; 0 < $index; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $funcName = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$funcName]->equalsAny(self::EXCLUDE_FUNC_NAMES, false)) {
                continue;
            }

            $docCommentIndex = $this->findFunctionDocComment($tokens, $index);

            if (null === $docCommentIndex) {
                continue;
            }

            foreach ($this->getAnnotationsFromDocComment('param', $tokens, $docCommentIndex) as $paramTypeAnnotation) {
                $typeInfo = $this->getCommonTypeFromAnnotation($paramTypeAnnotation, false);

                if (null === $typeInfo) {
                    continue;
                }

                [$paramType, $isNullable] = $typeInfo;

                $startIndex = $tokens->getNextTokenOfKind($index, ['(']);
                $variableIndex = $this->findCorrectVariable($tokens, $startIndex, $paramTypeAnnotation);

                if (null === $variableIndex) {
                    continue;
                }

                $byRefIndex = $tokens->getPrevMeaningfulToken($variableIndex);

                if ($tokens[$byRefIndex]->equals('&')) {
                    $variableIndex = $byRefIndex;
                }

                if ($this->hasParamTypeHint($tokens, $variableIndex)) {
                    continue;
                }

                if (!$this->isValidSyntax(sprintf('<?php function f(%s $x) {}', $paramType))) {
                    continue;
                }

                $tokens->insertAt($variableIndex, array_merge(
                    $this->createTypeDeclarationTokens($paramType, $isNullable),
                    [new Token([T_WHITESPACE, ' '])]
                ));
            }
        }
    }

    private function findCorrectVariable(Tokens $tokens, int $startIndex, Annotation $paramTypeAnnotation): ?int
    {
        $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);

        for ($index = $startIndex + 1; $index < $endIndex; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $variableName = $tokens[$index]->getContent();

            if ($paramTypeAnnotation->getVariableName() === $variableName) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Determine whether the function already has a param type hint.
     *
     * @param int $index The index of the end of the function definition line, EG at { or ;
     */
    private function hasParamTypeHint(Tokens $tokens, int $index): bool
    {
        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        return !$tokens[$prevIndex]->equalsAny([',', '(']);
    }
}
