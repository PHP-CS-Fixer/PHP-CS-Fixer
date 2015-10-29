<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Loïck Piera <pyrech@gmail.com>
 */
class LineBetweenMethodsFixer extends AbstractFixer
{
    static private $methodModifiers = array(
        'public',
        'protected',
        'private',
        'final',
        'abstract',
        'static',
    );

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_FUNCTION)) {
                $closingBracketIndex = $tokens->getPrevTokenOfKind($index, array('}'));

                if ($closingBracketIndex === null) {
                    continue;
                }

                $closingBracket = $tokens[$closingBracketIndex];

                if (!isset($tokens[$closingBracketIndex + 1])) {
                    continue;
                }

                $nextTokenIndex = $closingBracketIndex + 1;
                $nextToken = $tokens[$nextTokenIndex];

                while($nextToken->isWhitespace() || $nextToken->isComment() || in_array(strtolower($nextToken->getContent()), self::$methodModifiers)) {
                    $nextTokenIndex++;
                    $nextToken = $tokens[$nextTokenIndex];
                }

                if ($nextTokenIndex !== $index) {
                    continue;
                }

                $nextTokenAfterBracket = $tokens[$closingBracketIndex + 1];

                if (!$nextTokenAfterBracket->isWhitespace()) {
                    $tabulation = '';
                    if (isset($tokens[$closingBracketIndex - 1])) {
                        $closingBracketPreviousToken = $tokens[$closingBracketIndex - 1];
                        if ($closingBracketPreviousToken->isWhitespace()) {
                            $tabulation = ltrim($closingBracketPreviousToken->getContent(), "\n\r");
                        }
                    }
                    $tokens->insertAt($closingBracketIndex + 1, new Token(array(T_WHITESPACE, "\n\n".$tabulation)));
                } else {
                    $nextTokenAfterBracket->setContent("\n\n".ltrim($nextTokenAfterBracket->getContent(), "\n\r"));
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should be a blank lines between methods.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
