<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\PSR2;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶5.2.
 *
 * @author SpacePossum
 */
final class SwitchCaseSemicolonToColonFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_CASE, T_DEFAULT));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(array(T_CASE, T_DEFAULT))) {
                continue;
            }

            $ternariesCount = 0;
            for ($colonIndex = $index + 1; ; ++$colonIndex) {
                // We have to skip ternary case for colons.
                if ($tokens[$colonIndex]->equals('?')) {
                    ++$ternariesCount;
                }

                if ($tokens[$colonIndex]->equalsAny(array(':', ';'))) {
                    if (0 === $ternariesCount) {
                        break;
                    }

                    --$ternariesCount;
                }
            }

            if ($tokens[$colonIndex]->equals(';')) {
                $tokens->overrideAt($colonIndex, ':');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A case should be followed by a colon and not a semicolon.';
    }
}
