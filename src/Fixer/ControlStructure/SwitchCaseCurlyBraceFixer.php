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
 * @author SpacePossum
 */
final class SwitchCaseCurlyBraceFixer extends AbstractFixer
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
            if ($token->isGivenKind(array(T_CASE, T_DEFAULT))) {
                continue;
            }

            $next = $tokens->getNextTokenOfKind($index, array(':', ';'));
            while (true) {
                $next = $tokens->getNextMeaningfulToken($next);
                if (!$tokens[$next]->equals('{')) {
                    break;
                }

                $tokens->clearTokenAndMergeSurroundingWhitespace($tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $next));
                $tokens->clearTokenAndMergeSurroundingWhitespace($next);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes unneeded curly braces after case- and default statements.';
    }
}
