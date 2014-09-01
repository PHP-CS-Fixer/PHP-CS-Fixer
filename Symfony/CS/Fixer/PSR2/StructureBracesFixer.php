<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\FixerInterface;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class StructureBracesFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $this->fixMissingControlBraces($tokens);
        $this->fixIndents($tokens);
        $this->fixDoWhile($tokens);

        return $tokens->generateCode();
    }

    private function fixDoWhile(Tokens $tokens)
    {
        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_DO)) {
                continue;
            }

            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $startBraceIndex = null;
            $startBraceToken = $tokens->getNextNonWhitespace($parenthesisEndIndex, array(), $startBraceIndex);
            $endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);
            $nextNonWhitespaceIndex = null;
            $nextNonWhitespaceToken = $tokens->getNextNonWhitespace($endBraceIndex, array(), $nextNonWhitespaceIndex);

            if (!$nextNonWhitespaceToken->isGivenKind(T_WHILE)) {
                continue;
            }

            $beforeWhileToken = $tokens[$nextNonWhitespaceIndex - 1];
            $this->ensureWhitespaceAtIndex($tokens, $nextNonWhitespaceIndex - 1, 1, ' ');
        }
    }

    private function fixIndents(Tokens $tokens)
    {
        $classyTokens = $this->getClassyTokens();
        $controlTokens = $this->getControlTokens();
        $classyAndControlTokens = array_merge($classyTokens, $controlTokens);

        for ($index = 0, $limit = count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];

            // if token is not a structure element - continue
            if (!$token->isGivenKind($classyAndControlTokens)) {
                continue;
            }

            if ($token->isGivenKind($classyTokens)) {
                $startBraceIndex = null;
                $startBraceToken = $tokens->getNextTokenOfKind($index, array('{'), $startBraceIndex);
            } else {
                $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
                $startBraceIndex = null;
                $startBraceToken = $tokens->getNextNonWhitespace($parenthesisEndIndex, array(), $startBraceIndex);
            }

            // structure without braces block - nothing to do, e.g. do { } while (true);
            if ('{' !== $startBraceToken->content) {
                continue;
            }

            $endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);

            $indent = $this->detectIndent($tokens, $index);

            // fix indent near closing brace
            $this->ensureWhitespaceAtIndex($tokens, $endBraceIndex - 1, 1, "\n".$indent);

            // fix indent between braces
            $lastCommaIndex = null;
            $prevToken = $tokens->getPrevTokenOfKind($endBraceIndex - 1, array(';', '}'), $lastCommaIndex);

            $nestLevel = 1;
            for ($nestIndex = $lastCommaIndex; $nestIndex >= $startBraceIndex; --$nestIndex) {
                $nestToken = $tokens[$nestIndex];

                if (')' === $nestToken->content) {
                    $nestIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nestIndex, false);
                    continue;
                }

                if (1 === $nestLevel && in_array($nestToken->content, array(';', '}'), true)) {
                    $nextNonWhitespaceNestToken = $tokens->getNextNonWhitespace($nestIndex);

                    if ($nextNonWhitespaceNestToken->isGivenKind(array(T_ELSE, T_ELSEIF))) {
                        $whitespace = ' ';
                    } else {
                        $nextToken = $tokens[$nestIndex + 1];
                        $nextWhitespace = '';

                        if ($nextToken->isWhitespace()) {
                            $nextWhitespace = rtrim($nextToken->content, " \t");

                            if (strlen($nextWhitespace) && "\n" === $nextWhitespace[strlen($nextWhitespace) - 1]) {
                                $nextWhitespace = substr($nextWhitespace, 0, -1);
                            }
                        }

                        $whitespace = $nextWhitespace."\n".$indent;

                        if ('}' !== $nextNonWhitespaceNestToken->content) {
                            $whitespace .= '    ';
                        }
                    }

                    $this->ensureWhitespaceAtIndex($tokens, $nestIndex + 1, 0, $whitespace);
                }

                if ('}' === $nestToken->content) {
                    ++$nestLevel;
                    continue;
                }

                if ('{' === $nestToken->content) {
                    --$nestLevel;
                    continue;
                }
            }

            // fix indent near opening brace
            if (isset($tokens[$startBraceIndex + 2]) && '}' === $tokens[$startBraceIndex + 2]->content) {
                $this->ensureWhitespaceAtIndex($tokens, $startBraceIndex + 1, 0, "\n".$indent);
            } else {
                $this->ensureWhitespaceAtIndex($tokens, $startBraceIndex + 1, 0, "\n".$indent.'    ');
            }

            if ($token->isGivenKind($classyTokens)) {
                $this->ensureWhitespaceAtIndex($tokens, $startBraceIndex - 1, 1, "\n".$indent);
            } else {
                $this->ensureWhitespaceAtIndex($tokens, $startBraceIndex - 1, 1, ' ');
            }

            // reset loop due to collection change
            $limit = count($tokens);
        }
    }

    private function fixMissingControlBraces(Tokens $tokens)
    {
        $controlTokens = $this->getControlTokens();

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind($controlTokens)) {
                continue;
            }

            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $tokenAfterParenthesis = $tokens->getNextNonWhitespace($parenthesisEndIndex);

            // do not add braces for cases:
            // - structure without block, e.g. while ($iter->next());
            // - structure with block, e.g. while ($i) {...}, while ($i) : {...} endwhile;
            if (in_array($tokenAfterParenthesis->content, array(';', '{', ':'), true)) {
                continue;
            }

            $statementEndIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);

            // insert closing brace
            $tokens->insertAt($statementEndIndex + 1, array(new Token(array(T_WHITESPACE, ' ')), new Token('}')));

            // insert opening brace
            $tokens->removeTrailingWhitespace($parenthesisEndIndex);
            $tokens->insertAt($parenthesisEndIndex + 1, new Token('{'));
            $this->ensureWhitespaceAtIndex($tokens, $parenthesisEndIndex + 1, 0, ' ');
        }
    }

    private function detectIndent(Tokens $tokens, $index)
    {
        if ($tokens[$index]->isGivenKind($this->getClassyTokens())) {
            $prevIndex = null;
            $prevToken = $tokens->getPrevNonWhitespace($index, array(), $prevIndex);

            if ($prevToken->isGivenKind(array(T_ABSTRACT, T_FINAL))) {
                $index = $prevIndex;
            }
        }

        $prevIndex = $index - 1;
        $prevToken = $tokens[$prevIndex];

        if ('}' === $prevToken->content) {
            return $this->detectIndent($tokens, $prevIndex);
        }

        // if can not detect indent:
        if (!$prevToken->isWhitespace()) {
            return '';
        }

        $explodedContent = explode("\n", $prevToken->content);

        // proper decect indent for code: `    } else {`
        if (1 === count($explodedContent)) {
            if ('}' === $tokens[$index - 2]->content) {
                return $this->detectIndent($tokens, $index - 2);
            }
        }

        return end($explodedContent);
    }

    private function ensureWhitespaceAtIndex(Tokens $tokens, $index, $indexOffset, $whitespace)
    {
        $removeLastCommentLine = function ($token, $indexOffset) {
            // becouse comments tokens are greedy and may consume single \n if we are putting whitespace after it let trim that \n
            if (1 === $indexOffset && $token->isGivenKind(array(T_COMMENT, T_DOC_COMMENT)) && "\n" === $token->content[strlen($token->content) - 1]) {
                $token->content = substr($token->content, 0, -1);
            }
        };

        $token = $tokens[$index];

        if ($token->isWhitespace()) {
            $removeLastCommentLine($tokens[$index - 1], $indexOffset);
            $token->content = $whitespace;

            return;
        }

        $removeLastCommentLine($token, $indexOffset);

        $tokens->insertAt(
            $index + $indexOffset,
            array(
                new Token(array(T_WHITESPACE, $whitespace)),
            )
        );
    }

    private function findParenthesisEnd(Tokens $tokens, $structureTokenIndex)
    {
        $nextIndex = null;
        $nextToken = $tokens->getNextNonWhitespace($structureTokenIndex, array(), $nextIndex);

        // return if next token is not opening parenthesis
        if ('(' !== $nextToken->content) {
            return $structureTokenIndex;
        }

        return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
    }

    private function findStatementEnd(Tokens $tokens, $parenthesisEndIndex)
    {
        $nextIndex = null;
        $nextToken = $tokens->getNextNonWhitespace($parenthesisEndIndex, array(), $nextIndex);

        if (!$nextToken) {
            return $parenthesisEndIndex;
        }

        if ('{' === $nextToken->content) {
            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $nextIndex);
        }

        if ($nextToken->isGivenKind($this->getControlTokens())) {
            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);

            $endIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);

            if ($nextToken->isGivenKind(T_IF)) {
                $nextIndex = null;
                $nextToken = $tokens->getNextNonWhitespace($endIndex, array(), $nextIndex);

                if ($nextToken && $nextToken->isGivenKind(array(T_ELSE, T_ELSEIF))) {
                    $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);

                    return $this->findStatementEnd($tokens, $parenthesisEndIndex);
                }
            }

            return $endIndex;
        }

        $index = $parenthesisEndIndex;

        while (true) {
            $token = $tokens[++$index];

            if (';' === $token->content) {
                break;
            }
        }

        return $index;
    }

    private function getClassyTokens()
    {
        static $tokens = null;

        if ($tokens === null) {
            $tokens = array(T_CLASS, T_INTERFACE);

            if (defined('T_TRAIT')) {
                $tokens[] = T_TRAIT;
            }
        }

        return $tokens;
    }

    private function getControlTokens()
    {
        static $tokens = array(
            T_DECLARE,
            T_DO,
            T_ELSE,
            T_ELSEIF,
            T_FOR,
            T_FOREACH,
            T_IF,
            T_WHILE,
        );

        return $tokens;
    }

    public function getLevel()
    {
        // defined in PSR2 ¶4.1, ¶5
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        // should be run after the CurlyBracketsNewlineFixer and ElseIfFixer
        return -25;
    }

    public function supports(\SplFileInfo $file)
    {
        return true;
    }

    public function getName()
    {
        return 'structure_braces';
    }

    public function getDescription()
    {
        return 'The body of each structure MUST be enclosed by braces. Braces should be properly placed. Fix indent of body as well.';
    }
}
