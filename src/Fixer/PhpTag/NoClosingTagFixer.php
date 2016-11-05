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

namespace PhpCsFixer\Fixer\PhpTag;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoClosingTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CLOSE_TAG);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        if (!$tokens->isMonolithicPhp()) {
            return;
        }

        $closeTags = $tokens->findGivenKind(T_CLOSE_TAG);

        if (empty($closeTags)) {
            return;
        }

        list($index, $token) = each($closeTags);

        $tokens->removeLeadingWhitespace($index);
        $token->clear();

        $prevIndex = $tokens->getPrevNonWhitespace($index);
        $prevToken = $tokens[$prevIndex];

        if (!$prevToken->equalsAny(array(';', '}'))) {
            $tokens->insertAt($prevIndex + 1, new Token(';'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The closing ?> tag MUST be omitted from files containing only PHP.';
    }
}
