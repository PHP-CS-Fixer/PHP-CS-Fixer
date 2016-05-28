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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NewWithBracesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_NEW);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        static $nextTokenKinds = null;

        if (null === $nextTokenKinds) {
            $nextTokenKinds = array(
                '?',
                ';',
                ',',
                '(',
                ')',
                '[',
                ']',
                ':',
                '<',
                '>',
                '+',
                '-',
                '*',
                '/',
                '%',
                '&',
                '^',
                '|',
                array(T_IS_SMALLER_OR_EQUAL),
                array(T_IS_GREATER_OR_EQUAL),
                array(T_IS_EQUAL),
                array(T_IS_NOT_EQUAL),
                array(T_IS_IDENTICAL),
                array(T_IS_NOT_IDENTICAL),
                array(T_CLOSE_TAG),
                array(T_LOGICAL_AND),
                array(T_LOGICAL_OR),
                array(T_LOGICAL_XOR),
                array(T_BOOLEAN_AND),
                array(T_BOOLEAN_OR),
                array(T_SL),
                array(T_SR),
                array(T_INSTANCEOF),
                array(T_AS),
                array(T_DOUBLE_ARROW),
                array(CT_ARRAY_SQUARE_BRACE_OPEN),
                array(CT_ARRAY_SQUARE_BRACE_CLOSE),
                array(CT_BRACE_CLASS_INSTANTIATION_OPEN),
                array(CT_BRACE_CLASS_INSTANTIATION_CLOSE),
            );
            if (defined('T_POW')) {
                $nextTokenKinds[] = array(T_POW);
            }

            if (defined('T_SPACESHIP')) {
                $nextTokenKinds[] = array(T_SPACESHIP);
            }
        }

        for ($index = $tokens->count() - 3; $index > 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_NEW)) {
                continue;
            }

            $nextIndex = $tokens->getNextTokenOfKind($index, $nextTokenKinds);
            $nextToken = $tokens[$nextIndex];

            // entrance into array index syntax - need to look for exit
            while ($nextToken->equals('[')) {
                $nextIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $nextIndex) + 1;
                $nextToken = $tokens[$nextIndex];
            }

            // new statement has a gap in it - advance to the next token
            if ($nextToken->isWhitespace()) {
                $nextIndex = $tokens->getNextNonWhitespace($nextIndex);
                $nextToken = $tokens[$nextIndex];
            }

            // new statement with () - nothing to do
            if ($nextToken->equals('(')) {
                continue;
            }

            $meaningBeforeNextIndex = $tokens->getPrevMeaningfulToken($nextIndex);

            $tokens->insertAt($meaningBeforeNextIndex + 1, array(new Token('('), new Token(')')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'All instances created with new keyword must be followed by braces.';
    }
}
