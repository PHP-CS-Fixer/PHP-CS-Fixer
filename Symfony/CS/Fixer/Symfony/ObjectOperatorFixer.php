<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ObjectOperatorFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] there should not be space before or after T_OBJECT_OPERATOR
        $tokens = Tokens::fromCode($content);

        foreach (array_keys($tokens->findGivenKind(T_OBJECT_OPERATOR)) as $index) {
            // clear whitespace before ->
            if ($tokens[$index - 1]->isWhitespace(array('whitespaces' => " \t")) && !$tokens[$index - 2]->isComment()) {
                $tokens[$index - 1]->clear();
            }

            // clear whitespace after ->
            if ($tokens[$index + 1]->isWhitespace(array('whitespaces' => " \t")) && !$tokens[$index + 2]->isComment()) {
                $tokens[$index + 1]->clear();
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There should not be space before or after object T_OBJECT_OPERATOR.';
    }
}
