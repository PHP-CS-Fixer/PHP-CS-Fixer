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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'A case should be followed by a colon and not a semicolon.',
            array(
                new CodeSample(
'<?php
    switch ($a) {
        case 1;
            break;
        default;
            break;
    }
'
                ),
            )
        );
    }

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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
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
}
