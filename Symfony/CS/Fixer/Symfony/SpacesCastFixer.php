<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class SpacesCastFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        static $insideCastSpaceReplaceMap = array (
            ' ' => '',
            "\t" => '',
            "\n" => '',
            "\r" => '',
            "\0" => '',
            "\x0B" => '',
        );

        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if ($token->isCast()) {
                $token->content = strtr($token->content, $insideCastSpaceReplaceMap);

                // force single whitespace after cast token:
                if ($tokens[$index + 1]->isWhitespace(array('whitespaces' => " \t"))) {
                    // - if next token is whitespaces that contains only spaces and tabs - override next token with single space
                    $tokens[$index + 1]->content = ' ';
                } elseif (!$tokens[$index + 1]->isWhitespace()) {
                    // - if next token is not whitespaces that contains spaces, tabs and new lines - append single space to current token
                    $tokens->insertAt($index + 1, new Token(' '));
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
        return 'A single space should be between cast and variable.';
    }
}
