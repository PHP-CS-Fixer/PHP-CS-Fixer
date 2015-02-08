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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class StrictFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        static $map = array(
            T_IS_EQUAL => array(
                'id'      => T_IS_IDENTICAL,
                'content' => '===',
            ),
            T_IS_NOT_EQUAL => array(
                'id'      => T_IS_NOT_IDENTICAL,
                'content' => '!==',
            ),
        );

        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            $tokenId = $token->getId();

            if (isset($map[$tokenId])) {
                $token->override(array($map[$tokenId]['id'], $map[$tokenId]['content'], $token->getLine()));
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Comparison should be strict. Warning! This could change code behavior.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the StandardizeNotEqualFixer
        return -10;
    }
}
