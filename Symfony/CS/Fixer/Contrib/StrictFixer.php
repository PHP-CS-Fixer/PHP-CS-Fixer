<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\FixerInterface;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class StrictFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $this->fixComparisons($tokens);

        return $tokens->generateCode();
    }

    private function fixComparisons(Tokens $tokens)
    {
        static $map = array(
            T_IS_EQUAL => array(
                'id' => T_IS_IDENTICAL,
                'content' => '===',
            ),
            T_IS_NOT_EQUAL => array(
                'id' => T_IS_NOT_IDENTICAL,
                'content' => '!==',
            ),
        );

        foreach ($tokens as $token) {
            if (isset($map[$token->id])) {
                $token->content = $map[$token->id]['content'];
                $token->id = $map[$token->id]['id'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'strict';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Comparison should be strict. Warning! This could change code behavior.';
    }
}
