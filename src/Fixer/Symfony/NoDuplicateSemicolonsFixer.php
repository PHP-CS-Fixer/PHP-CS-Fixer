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

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoDuplicateSemicolonsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(';');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $limit = $tokens->count();

        for ($index = 0; $index < $limit; ++$index) {
            $token = $tokens[$index];

            // skip T_FOR parenthesis to ignore duplicated `;` like `for ($i = 1; ; ++$i) {...}`
            if ($token->isGivenKind(T_FOR)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $tokens->getNextMeaningfulToken($index)) + 1;
                continue;
            }

            if (!$token->equals(';') || !$tokens[$tokens->getPrevMeaningfulToken($index)]->equals(';')) {
                continue;
            }

            $tokens->removeLeadingWhitespace($index);
            $token->clear();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove duplicated semicolons.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the BracesFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer, NoMultilineWhitespaceBeforeSemicolonsFixer and SwitchCaseSemicolonToColonFixer.
        return 10;
    }
}
