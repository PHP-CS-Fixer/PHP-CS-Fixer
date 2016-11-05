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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶5.2.
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class SwitchCaseSpaceFixer extends AbstractFixer
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

            $valueIndex = $tokens->getPrevNonWhitespace($colonIndex);
            if (2 + $valueIndex === $colonIndex) {
                $tokens[$valueIndex + 1]->clear();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes extra spaces between colon and case value.';
    }
}
