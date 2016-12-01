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

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Nicola Pietroluongo <nik.longstone@gmail.com>
 */
class NoYodaConditionFixer extends AbstractFixer
{
    private $conditions = array(
        array(T_IS_EQUAL),
        array(T_IS_NOT_EQUAL),
        array(T_IS_IDENTICAL),
        array(T_IS_NOT_IDENTICAL),
    );

    private $yodaToken = array(
        array(T_LNUMBER),
        array(T_STRING),
    );

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if ($token->equalsAny($this->conditions)) {
                $previousTokenNonWhiteIndex = $tokens->getPrevNonWhitespace($index);
                $previousToken = $tokens[$previousTokenNonWhiteIndex];
                if ($previousToken->equalsAny($this->yodaToken)) {
                    $nextNonWhiteIndex = $tokens->getNextNonWhitespace($index);
                    $nextToken = $tokens[$nextNonWhiteIndex];
                    if (!$nextToken->equalsAny($this->yodaToken)) {
                        $tokens[$previousTokenNonWhiteIndex] = $nextToken;
                        $tokens[$nextNonWhiteIndex] = $previousToken;
                    }
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'No Yoda condition for ==, !=, ===, and !==. Warning! This could change code behavior.';
    }
}
