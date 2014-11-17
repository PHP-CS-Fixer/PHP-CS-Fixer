<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ExtraEmptyLinesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_WHITESPACE) as $token) {
            $content = '';
            $count = 0;
            $parts = explode("\n", $token->getContent());

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

            $token->setContent($content);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes extra empty lines.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the UnusedUseFixer
        return -20;
    }
}
