<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class StructureBracesFixer implements FixerInterface
{
    static private $structures = array(
        T_DO        => T_DO,
        T_ELSE      => T_ELSE,
        T_ELSEIF    => T_ELSEIF,
        T_FOR       => T_FOR,
        T_FOREACH   => T_FOREACH,
        T_IF        => T_IF,
        T_WHILE     => T_WHILE,
    );

    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $this->fixMissingBraces($tokens);
        $tokens->clearEmptyTokens();
        $this->fixIndents($tokens);

        return $tokens->generateCode();
    }

    private function fixIndents(Tokens $tokens)
    {
        for ($index = 0, $limit = count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];

            // if token is not a structure element - continue
            if (!$token->isGivenKind(self::$structures)) {
                continue;
            }

/* debug
echo "-----\n";
echo "Content: " . $token->content . " | " . $index . "\n";
$indent = $this->detectIndent($tokens, $index);
echo "Indent: > " . $indent . "<\n";
echo "=====\n\n\n";
*/

            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $startBraceIndex = null;
            $startBraceToken = $tokens->getNextNonWhitespace($parenthesisEndIndex, array(), $startBraceIndex);

            // structure without block - nothing to do, e.g. do { } while (true);
            if ('{' !== $startBraceToken->content) {
                continue;
            }

            $endBraceIndex = $tokens->findBracesBlockEnd($startBraceIndex);

            $indent = $this->detectIndent($tokens, $index);

            // fix indent near closing brace
            $this->ensureWhitespaceAtIndex($tokens, $endBraceIndex - 1, 1, "\n".$indent);

            // fix indent between braces
            $lastCommaIndex = null;
            $prevToken = $tokens->getPrevTokenOfKind($endBraceIndex - 1, array(';', '}'), $lastCommaIndex);

            $nestLevel = 1;
            for ($nestIndex = $lastCommaIndex - 1; $nestIndex >= $startBraceIndex; --$nestIndex) {
                if (1 === $nestLevel && in_array($tokens[$nestIndex]->content, array(';', '}'), true)) {
                    if ($tokens->getNextNonWhitespace($nestIndex)->isGivenKind(array(T_ELSE, T_ELSEIF))) {
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

                        $whitespace = $nextWhitespace."\n".$indent.'    ';
                    }

                    $this->ensureWhitespaceAtIndex($tokens, $nestIndex + 1, 0, $whitespace);
                }

                if ('}' === $tokens[$nestIndex]->content) {
                    ++$nestLevel;
                    continue;
                }

                if ('{' === $tokens[$nestIndex]->content) {
                    --$nestLevel;
                    continue;
                }
            }

            // fix indent near opening brace
            $this->ensureWhitespaceAtIndex($tokens, $startBraceIndex + 1, 0, "\n".$indent.'    ');
            $this->ensureWhitespaceAtIndex($tokens, $startBraceIndex - 1, 1, ' ');

            // reset loop due to collection change
            $limit = count($tokens);
        }
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

    private function fixMissingBraces(Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(self::$structures)) {
                continue;
            }

            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $tokenAfterParenthesis = $tokens->getNextNonWhitespace($parenthesisEndIndex);

            // structure without block or with block with braces - nothing to do
            if (in_array($tokenAfterParenthesis->content, array(';', '{'), true)) {
                continue;
            }

            $statementEndIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);

            // insert closing brace
            $tokens->insertAt($statementEndIndex + 1, array(new Token(array(T_WHITESPACE, ' ')), new Token('}')));

            // insert opening brace
            $tokens->removeTrailingWhitespace($parenthesisEndIndex);
            $tokens->insertAt($parenthesisEndIndex + 1, array(new Token(array(T_WHITESPACE, ' ')), new Token('{'), new Token(array(T_WHITESPACE, ' '))));
        }
    }

    private function detectIndent(Tokens $tokens, $index)
    {
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

    private function findStatementEnd(Tokens $tokens, $parenthesisEndIndex)
    {
        $nextIndex = null;
        $nextToken = $tokens->getNextNonWhitespace($parenthesisEndIndex, array(), $nextIndex);

        if (!$nextToken) {
            return $parenthesisEndIndex;
        }

        if ('{' === $nextToken->content) {
            return $tokens->findBracesBlockEnd($nextIndex);
        }

        if ($nextToken->isGivenKind(self::$structures)) {
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

    private function findParenthesisEnd(Tokens $tokens, $structureTokenIndex)
    {
        $nextToken = $tokens->getNextNonWhitespace($structureTokenIndex);

        // return if next token is not opening parenthesis
        if ('(' !== $nextToken->content) {
            return $structureTokenIndex;
        }

        $parenthesisLevel = 0;
        $index = $structureTokenIndex;

        while (true) {
            $token = $tokens[++$index];

            if ('(' === $token->content) {
                ++$parenthesisLevel;
                continue;
            }

            if (')' === $token->content) {
                --$parenthesisLevel;

                if (0 === $parenthesisLevel) {
                    break;
                }

                continue;
            }
        }

        return $index;
    }

    public function getLevel()
    {
        // defined in PSR2 ¶5
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'structure_braces';
    }

    public function getDescription()
    {
        return 'The body of each structure MUST be enclosed by braces. Braces should be properly placed.';
    }
}
