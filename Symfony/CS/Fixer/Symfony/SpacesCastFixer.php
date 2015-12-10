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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

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
        static $insideCastSpaceReplaceMap = array(
            ' ' => '',
            "\t" => '',
            "\n" => '',
            "\r" => '',
            "\0" => '',
            "\x0B" => '',
        );

        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$token->isCast()) {
                continue;
            }

            $token->setContent(strtr($token->getContent(), $insideCastSpaceReplaceMap));
            // force single whitespace after cast token if not after line break
            if ($tokens->isIndented($index + 2)) {
                continue;
            }

            $tokens->ensureSingleWithSpaceAt($index + 1);
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

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be ran after the ShortBoolCastFixer
        return -10;
    }
}
