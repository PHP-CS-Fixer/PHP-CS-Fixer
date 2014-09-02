<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ExtraEmptyLinesFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $content = '';
            $count = 0;
            $parts = explode("\n", $token->content);

            for ($i = 0, $last = count($parts) - 1; $i <= $last; ++$i) {
                if ('' === $parts[$i]) {
                    // if part is empty then we between two \n
                    ++$count;
                } else {
                    $count = 0;
                    $content .= $parts[$i];
                }

                if ($i !== $last && $count < 3) {
                    $content .= "\n";
                }
            }

            $token->content = $content;
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
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
        return 'extra_empty_lines';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes extra empty lines.';
    }
}
