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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\AlternativeSyntaxAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class ControlStructureBracesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The body of each control structure MUST be enclosed within braces.',
            [new CodeSample("<?php\nif (foo()) echo 'Hello!';\n")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ControlStructureContinuationPositionFixer, CurlyBracesPositionFixer, NoMultipleStatementsPerLineFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $alternativeSyntaxAnalyzer = new AlternativeSyntaxAnalyzer();
        $controlTokens = $this->getControlTokens();

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind($controlTokens)) {
                continue;
            }

            if (
                $token->isGivenKind(T_ELSE)
                && $tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(T_IF)
            ) {
                continue;
            }

            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $nextAfterParenthesisEndIndex = $tokens->getNextMeaningfulToken($parenthesisEndIndex);
            $tokenAfterParenthesis = $tokens[$nextAfterParenthesisEndIndex];

            if ($tokenAfterParenthesis->equalsAny([';', '{', ':'])) {
                continue;
            }

            $statementEndIndex = null;

            if ($tokenAfterParenthesis->isGivenKind([T_IF, T_FOR, T_FOREACH, T_SWITCH, T_WHILE])) {
                $tokenAfterParenthesisBlockEnd = $tokens->findBlockEnd( // go to ')'
                    Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
                    $tokens->getNextMeaningfulToken($nextAfterParenthesisEndIndex)
                );

                if ($tokens[$tokens->getNextMeaningfulToken($tokenAfterParenthesisBlockEnd)]->equals(':')) {
                    $statementEndIndex = $alternativeSyntaxAnalyzer->findAlternativeSyntaxBlockEnd($tokens, $nextAfterParenthesisEndIndex);

                    $tokenAfterStatementEndIndex = $tokens->getNextMeaningfulToken($statementEndIndex);
                    if ($tokens[$tokenAfterStatementEndIndex]->equals(';')) {
                        $statementEndIndex = $tokenAfterStatementEndIndex;
                    }
                }
            }

            if (null === $statementEndIndex) {
                $statementEndIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);
            }

            $tokensToInsertAfterStatement = [
                new Token([T_WHITESPACE, ' ']),
                new Token('}'),
            ];

            if (!$tokens[$statementEndIndex]->equalsAny([';', '}'])) {
                array_unshift($tokensToInsertAfterStatement, new Token(';'));
            }

            $tokens->insertSlices([$statementEndIndex + 1 => $tokensToInsertAfterStatement]);

            // insert opening brace
            $tokens->insertSlices([$parenthesisEndIndex + 1 => [
                new Token([T_WHITESPACE, ' ']),
                new Token('{'),
            ]]);
        }
    }

    private function findParenthesisEnd(Tokens $tokens, int $structureTokenIndex): int
    {
        $nextIndex = $tokens->getNextMeaningfulToken($structureTokenIndex);
        $nextToken = $tokens[$nextIndex];

        if (!$nextToken->equals('(')) {
            return $structureTokenIndex;
        }

        return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
    }

    private function findStatementEnd(Tokens $tokens, int $parenthesisEndIndex): int
    {
        $nextIndex = $tokens->getNextMeaningfulToken($parenthesisEndIndex);
        $nextToken = $tokens[$nextIndex];

        if (!$nextToken) {
            return $parenthesisEndIndex;
        }

        if ($nextToken->equals('{')) {
            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $nextIndex);
        }

        if ($nextToken->isGivenKind($this->getControlTokens())) {
            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);

            $endIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);

            if ($nextToken->isGivenKind([T_IF, T_TRY, T_DO])) {
                $openingTokenKind = $nextToken->getId();

                while (true) {
                    $nextIndex = $tokens->getNextMeaningfulToken($endIndex);
                    $nextToken = isset($nextIndex) ? $tokens[$nextIndex] : null;
                    if ($nextToken && $nextToken->isGivenKind($this->getControlContinuationTokensForOpeningToken($openingTokenKind))) {
                        $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);

                        $endIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);

                        if ($nextToken->isGivenKind($this->getFinalControlContinuationTokensForOpeningToken($openingTokenKind))) {
                            return $endIndex;
                        }
                    } else {
                        break;
                    }
                }
            }

            return $endIndex;
        }

        $index = $parenthesisEndIndex;

        while (true) {
            $token = $tokens[++$index];

            // if there is some block in statement (eg lambda function) we need to skip it
            if ($token->equals('{')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            if ($token->equals(';')) {
                return $index;
            }

            if ($token->isGivenKind(T_CLOSE_TAG)) {
                return $tokens->getPrevNonWhitespace($index);
            }
        }
    }

    /**
     * @return list<int>
     */
    private function getControlTokens(): array
    {
        static $tokens = [
            T_DECLARE,
            T_DO,
            T_ELSE,
            T_ELSEIF,
            T_FINALLY,
            T_FOR,
            T_FOREACH,
            T_IF,
            T_WHILE,
            T_TRY,
            T_CATCH,
            T_SWITCH,
        ];

        return $tokens;
    }

    /**
     * @return list<int>
     */
    private function getControlContinuationTokensForOpeningToken(int $openingTokenKind): array
    {
        if (T_IF === $openingTokenKind) {
            return [
                T_ELSE,
                T_ELSEIF,
            ];
        }

        if (T_DO === $openingTokenKind) {
            return [T_WHILE];
        }

        if (T_TRY === $openingTokenKind) {
            return [
                T_CATCH,
                T_FINALLY,
            ];
        }

        return [];
    }

    /**
     * @return list<int>
     */
    private function getFinalControlContinuationTokensForOpeningToken(int $openingTokenKind): array
    {
        if (T_IF === $openingTokenKind) {
            return [T_ELSE];
        }

        if (T_TRY === $openingTokenKind) {
            return [T_FINALLY];
        }

        return [];
    }
}
