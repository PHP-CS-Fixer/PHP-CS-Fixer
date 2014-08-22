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
            T_ELSE      => true,
            T_ELSEIF    => true,
            T_FOR       => true,
            T_FOREACH   => true,
            T_IF        => true,
            T_WHILE     => true,
        );

    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $this->fixMissingBraces($tokens);
        $this->fixIndents($tokens);

        return $tokens->generateCode();
    }

    private function fixIndents(Tokens $tokens)
    {
        $structureFixedCollection = new \SplObjectStorage();

        for ($index = 0, $limit = count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];

            // if token is not a structure element - continue
            if (!$token->isArray() || !isset(self::$structures[$token->id])) {
                continue;
            }

            // if token was already fixed - continue
            if ($structureFixedCollection->contains($token)) {
                continue;
            }

            // set info that token was fixed
            $structureFixedCollection->attach($token);

            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $startBraceIndex = null;
            $startBraceToken = $tokens->getNextNonWhitespace($parenthesisEndIndex, array(), $startBraceIndex);

            // structure without block - nothing to do
            if ('{' !== $startBraceToken->content) {
                continue;
            }

            $endBraceIndex = $this->findBracesBlockEnd($tokens, $startBraceIndex);
            $endBraceToken = $tokens[$endBraceIndex];
        }
    }

    private function fixMissingBraces(Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isArray() || !isset(self::$structures[$token->id])) {
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

    private function detectIndent(Tokens $tokens, $structureTokenIndex)
    {
        $token = $tokens[$structureTokenIndex - 1];
/// co z wcieciami dla:
/// } else { ... } ???
        // if can not detect indent:
        if (!$token->isWhitespace()) {
            return '';
        }

        $explodedContent = explode("\n", $token->content);

        return end($explodedContent);
    }

    private function findBracesBlockEnd(Tokens $tokens, $startBraceIndex)
    {
        $bracesLevel = 0;

        for ($index = $startBraceIndex; ; ++$index) {
            $token = $tokens[$index];

            if ('{' === $token->content) {
                ++$bracesLevel;

                continue;
            }

            if ('}' === $token->content) {
                --$bracesLevel;

                if (0 === $bracesLevel) {
                    break;
                }

                continue;
            }
        }

        return $index;
    }

    private function findStatementEnd(Tokens $tokens, $parenthesisEndIndex)
    {
        $nextIndex = null;
        $nextToken = $tokens->getNextNonWhitespace($parenthesisEndIndex, array(), $nextIndex);

        if (!$nextToken) {
            return $parenthesisEndIndex;
        }

        if ('{' === $nextToken->content) {
            return $this->findBracesBlockEnd($tokens, $nextIndex);
        }

        if ($nextToken->isArray() && isset(self::$structures[$nextToken->id])) {
            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $nextIndex);

            $endIndex = $this->findStatementEnd($tokens, $parenthesisEndIndex);

            if (T_IF === $nextToken->id) {
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
