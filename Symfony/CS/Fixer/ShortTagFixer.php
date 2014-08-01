<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ShortTagFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // replace all <? with <?php to replace all short open tags even without short_open_tag option enabled
        $newContent = preg_replace('/<\?(\s|$)/', '<?php$1', $content);

        /* the following code is magic to revert previous replacements which should NOT be replaced, for example incorrectly replacing
         * > echo '<? ';
         * with
         * > echo '<?php ';
        */
        $tokens = Tokens::fromCode($newContent);
        $tokensOldContent = '';
        $tokensOldContentLength = 0;

        foreach ($tokens as $token) {
            if ($token->isGivenKind(T_OPEN_TAG)) {
                $tokenContent = $token->content;

                if ('<?php' !== substr($content, $tokensOldContentLength, 5)) {
                    $tokenContent = '<? ';
                }

                $tokensOldContent .= $tokenContent;
                $tokensOldContentLength += strlen($tokenContent);
                continue;
            }

            if ($token->isGivenKind(array(T_COMMENT, T_DOC_COMMENT, T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE, T_STRING, ))) {
                $tokenContent = '';
                $tokenContentLength = 0;
                $parts = explode('<?php ', $token->content);
                $iLast = count($parts) - 1;

                foreach ($parts as $i => $part) {
                    $tokenContent .= $part;
                    $tokenContentLength += strlen($part);

                    if ($i !== $iLast) {
                        if ('<?php' === substr($content, $tokensOldContentLength + $tokenContentLength, 5)) {
                            $tokenContent .= '<?php ';
                            $tokenContentLength += 5;
                        } else {
                            $tokenContent .= '<? ';
                            $tokenContentLength += 3;
                        }
                    }
                }

                $token->content = $tokenContent;
            }

            $tokensOldContent .= $token->content;
            $tokensOldContentLength += strlen($token->content);
        }

        return $tokens->generateCode();
    }

    public function getLevel()
    {
        // defined in PSR1 ¶2.1
        return FixerInterface::PSR1_LEVEL;
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
        return 'short_tag';
    }

    public function getDescription()
    {
        return 'PHP code must use the long <?php ?> tags or the short-echo <?= ?> tags; it must not use the other tag variations.';
    }
}
