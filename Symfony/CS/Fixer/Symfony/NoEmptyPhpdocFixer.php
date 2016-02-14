<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class NoEmptyPhpdocFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            if (preg_match('#^/\*\*[\s\*]*\*/$#', $token->getContent())) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should not be empty PHPDoc blocks.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before ExtraEmptyLinesFixer, TrailingSpacesFixer, WhitespacyLinesFixer and
        // after PhpdocNoAccessFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer.
        return 5;
    }
}
