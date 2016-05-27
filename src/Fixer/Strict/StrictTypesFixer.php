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

namespace PhpCsFixer\Fixer\Strict;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
final class StrictTypesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        if (!$tokens->isMonolithicPhp()) {
            return;
        }

        $declareTokens = array(
            new Token(array(T_OPEN_TAG, "<?php ")),
            new Token(array(T_DECLARE, 'declare')),
            new Token("("),
            new Token(array(T_STRING, "strict_types")),
            new Token("="),
            new Token(array(T_LNUMBER, 1)),
            new Token(")"),
            new Token(";"),
            new Token(array(T_WHITESPACE, "\n\n")),
        );

        // check for valid construct, break on mismatch
        for ($i = 0;; $i++) {
            if (!$tokens[$i]->equals($declareTokens[$i], true)) {
                break;
            }
            if ($i === count($declareTokens) - 1) {
                return;
            }
        }

        $index = $tokens->getNextNonWhitespace(0);

        // empty file, just add the tokens
        if (null === $index) {
            $tokens->overrideRange(0, 0, $declareTokens);

            return;
        }

        // find the end of the declare if there is one already so we can replace it
        if ($tokens[$index]->isGivenKind(T_DECLARE)) {
            $isOpen = false;
            $isStrictTypes = false;
            $originalIndex = $index;

            while ($index = $tokens->getNextNonWhitespace($index)) {
                if ($tokens[$index]->getContent() === '(') {
                    $isOpen = true;
                }
                if ($isOpen && $tokens[$index]->getContent() === 'strict_types') {
                    $isStrictTypes = true;
                }
                if ($tokens[$index]->getContent() === ';') {
                    $index = $tokens->getNextNonWhitespace($index);
                    break;
                }
            }

            if (!$isStrictTypes) {
                $index = $originalIndex;
            }
        }

        $tokens->overrideRange(0, $index - 1, $declareTokens);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Force strict types declaration in all files';
    }
}
