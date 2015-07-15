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
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class WhitespacyLinesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$token->isWhitespace()) {
                continue;
            }

            $content = $token->getContent();
            $lines = preg_split("/([\r\n]+)/", $content);

            if (
                // fix T_WHITESPACES with at least 3 lines (eg `\n   \n`)
                count($lines) > 2
                // and T_WHITESPACES with at least 2 lines at the end of file
                || (count($lines) > 1 && !isset($tokens[$index + 1]))
            ) {
                $newContent = preg_replace('/^\h+$/m', '', $content);

                if (isset($tokens[$index + 1])) {
                    $newContent .= end($lines);
                }

                $token->setContent($newContent);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove trailing whitespace at the end of blank lines.';
    }
}
