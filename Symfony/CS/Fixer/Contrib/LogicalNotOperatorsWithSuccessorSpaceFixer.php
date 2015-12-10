<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class LogicalNotOperatorsWithSuccessorSpaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens->isUnaryPredecessorOperator($index) && $tokens[$index]->equals('!') && !$tokens->isIndented($index + 2)) {
                $tokens->ensureSingleWithSpaceAt($index + 1);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Logical NOT operators (!) should have one trailing whitespace.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the UnaryOperatorsSpacesFixer
        return -10;
    }
}
