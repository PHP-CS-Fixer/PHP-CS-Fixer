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

namespace PhpCsFixer\Fixer\Contrib;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class EchoToPrintFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_ECHO);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_ECHO)) {
                continue;
            }

            $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
            $endTokenIndex = $tokens->getNextTokenOfKind($index, array(';', array(T_CLOSE_TAG)));
            $canBeConverted = true;

            for ($i = $nextTokenIndex; $i < $endTokenIndex; ++$i) {
                if ($tokens[$i]->equalsAny(array('(', array(CT_ARRAY_SQUARE_BRACE_OPEN)))) {
                    $blockType = $tokens->detectBlockType($tokens[$i]);
                    $i = $tokens->findBlockEnd($blockType['type'], $i);
                }

                if ($tokens[$i]->equals(',')) {
                    $canBeConverted = false;
                    break;
                }
            }

            if (false === $canBeConverted) {
                continue;
            }

            $tokens->overrideAt($index, array(T_PRINT, 'print'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Converts echo language construct to print if possible.';
    }

    /**
     * EchoToPrintFixer should run after NoShortEchoTagFixer.
     *
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -10;
    }
}
