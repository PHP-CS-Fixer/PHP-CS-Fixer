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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class YieldFromArrayToYieldsFixer extends AbstractFixer
{
    /**
     * @var array<int, Token>
     */
    private array $inserts;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Yield from array should be unpacked to series of yields.',
            [new CodeSample('<?php function generate() {
    yield from [
        1,
        2,
        3,
    ];
}
')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_YIELD_FROM);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BlankLineBeforeStatementFixer, NoExtraBlankLinesFixer, NoMultipleStatementsPerLineFixer, NoWhitespaceInBlankLineFixer, StatementIndentationFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->inserts = [];

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_YIELD_FROM)) {
                continue;
            }

            $arrayStartIndex = $tokens->getNextMeaningfulToken($index);

            if (!$tokens[$arrayStartIndex]->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
                continue;
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($index);

            if ($tokens[$arrayStartIndex]->isGivenKind(T_ARRAY)) {
                $startIndex = $tokens->getNextTokenOfKind($arrayStartIndex, ['(']);
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);

                $tokens->clearTokenAndMergeSurroundingWhitespace($arrayStartIndex); // clear `array` from `array(`
            } else {
                $startIndex = $arrayStartIndex;
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($startIndex);
            $tokens->clearTokenAndMergeSurroundingWhitespace($endIndex);
            $tokens->clearTokenAndMergeSurroundingWhitespace($tokens->getNextMeaningfulToken($endIndex));

            $this->unpackYieldFrom(
                $tokens,
                $tokens->getNextMeaningfulToken($startIndex),
                $tokens->getPrevMeaningfulToken($endIndex),
            );
        }

        $tokens->insertSlices($this->inserts);
    }

    private function unpackYieldFrom(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $this->inserts[$startIndex] = [new Token([T_YIELD, 'yield']), new Token([T_WHITESPACE, ' '])];

        for ($index = $startIndex; $index <= $endIndex; ++$index) {
            $token = $tokens[$index];

            // skip nested (), [], {} constructs
            $blockDefinitionProbe = Tokens::detectBlockType($token);

            if (null !== $blockDefinitionProbe && true === $blockDefinitionProbe['isStart']) {
                $index = $tokens->findBlockEnd($blockDefinitionProbe['type'], $index);

                continue;
            }

            if (!$tokens[$index]->equals(',')) {
                continue;
            }

            $yieldInsertIndex = $tokens->getNextMeaningfulToken($index);

            $tokens[$index] = new Token(';');

            if (null === $yieldInsertIndex || $yieldInsertIndex > $endIndex) {
                return;
            }

            $this->inserts[$yieldInsertIndex] = [new Token([T_YIELD, 'yield']), new Token([T_WHITESPACE, ' '])];
        }

        $this->inserts[$endIndex + 1] = new Token(';');
    }
}
