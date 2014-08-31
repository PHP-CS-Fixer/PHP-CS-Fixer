<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ObjectOperatorFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] there should not be space before or after T_OBJECT_OPERATOR
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            // skip if $token is not ->
            if (!$token->isGivenKind(T_OBJECT_OPERATOR)) {
                continue;
            }

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

    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return true;
    }

    public function getName()
    {
        return 'object_operator';
    }

    public function getDescription()
    {
        return 'There should not be space before or after object T_OBJECT_OPERATOR.';
    }
}
