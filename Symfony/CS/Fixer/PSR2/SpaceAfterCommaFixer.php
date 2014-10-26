<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\Token;

/**
 * Fixer for rules defined in PSR2 ¶4.4. Method Arguments
 *
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 */
class SpaceAfterCommaFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$tokens[$index]->equals(",")) {
                continue;
            }

            // remove space before each comma if exist
            if ($tokens[$index-1]->isWhitespace()) {
                $tokens[$index-1]->clear();
            }

            if ($tokens[$index+1]->isWhitespace()) {
                continue;
            }

            // add space after each comma if not exist
            $tokens->insertAt($index+1, new Token(array(T_WHITESPACE, ' ')));
            continue;
        }

        return $tokens->generateCode();
    }

    public function getName()
    {
        return 'space_after_comma';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST NOT be a space before each comma, and there MUST be one space after each comma.';
    }
}
