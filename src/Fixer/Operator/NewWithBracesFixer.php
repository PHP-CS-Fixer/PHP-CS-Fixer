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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'All instances created with new keyword must be followed by braces.',
            array(new CodeSample('<?php $x = new X;'))
        );
    }

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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
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
                array(T_CLASS),
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
                array(CT::T_ARRAY_SQUARE_BRACE_OPEN),
                array(CT::T_ARRAY_SQUARE_BRACE_CLOSE),
                array(CT::T_BRACE_CLASS_INSTANTIATION_OPEN),
                array(CT::T_BRACE_CLASS_INSTANTIATION_CLOSE),
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

            // new anonymous class definition
            if ($nextToken->isGivenKind(T_CLASS)) {
                if (!$tokens[$tokens->getNextMeaningfulToken($nextIndex)]->equals('(')) {
                    $this->insertBracesAfter($tokens, $nextIndex);
                }

                continue;
            }

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

            $this->insertBracesAfter($tokens, $tokens->getPrevMeaningfulToken($nextIndex));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function insertBracesAfter(Tokens $tokens, $index)
    {
        $tokens->insertAt(++$index, array(new Token('('), new Token(')')));
    }
}
