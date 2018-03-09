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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Class NoAlternativeSyntaxFixer.
 *
 * @author Eddilbert Macharia <edd.cowan@gmail.com>(eddmash.com)
 */
class NoAlternativeSyntaxFixer extends AbstractFixer
{
    const SINGLE_COLON = ':';

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Replace alternative syntax on if():endif;,foreach():endforeach; and while()endwhile()'.
            ' to use braces.', // Trailing dot is important. We thrive to use English grammar properly.
            [
                new CodeSample(
                    '<?php if(){}else{}'
                ),
                new CodeSample(
                    '<?php while(){}'
                ),
                new CodeSample(
                    '<?php foreach(){}'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_IF, T_ENDIF, T_ELSE, T_ELSEIF, T_WHILE, T_ENDWHILE,
            T_FOREACH, T_ENDFOREACH]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->fixElseif($tokens);
        $this->fixElse($tokens);
        $this->fixOpenCloseControls($tokens);
    }

    private function findParenthesisEnd(Tokens $tokens, $structureTokenIndex)
    {
        $nextIndex = $tokens->getNextMeaningfulToken($structureTokenIndex);
        $nextToken = $tokens[$nextIndex];

        // return if next token is not opening parenthesis
        if (!$nextToken->equals('(')) {
            return $structureTokenIndex;
        }

        return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
    }

    /**
     * @param Tokens $tokens
     *
     * @author Eddilbert Macharia <edd.cowan@gmail.com>(eddmash.com)
     */
    private function fixElseif(Tokens $tokens)
    {
        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(T_ELSEIF)) {
                continue;
            }

            $prevIndex = $tokens->getPrevNonWhitespace($index);
            $prevToken = $tokens[$prevIndex];
            if (!$prevToken->equals('}')) {
                // insert closing brace
                $tokens->insertAt($prevIndex + 1, [new Token([T_WHITESPACE, ' ']), new Token('}')]);
                $this->fixElseif($tokens);

                break;
            }

            $parenthesisEndIndex = $this->findParenthesisEnd($tokens, $index);
            $tokenAfterParenthesis = $tokens[$tokens->getNextMeaningfulToken($parenthesisEndIndex)];
            if ($tokenAfterParenthesis->equals(self::SINGLE_COLON)) {
                // insert closing brace
                $tokens[$tokens->getNextMeaningfulToken($parenthesisEndIndex)] = new Token('{');
            }
        }
    }

    /**
     * @param Tokens $tokens
     *
     * @author Eddilbert Macharia <edd.cowan@gmail.com>(eddmash.com)
     */
    private function fixOpenCloseControls(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind([T_IF, T_FOREACH, T_WHILE])) {
                $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
                $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
                $afterParenthesisIndex = $tokens->getNextNonWhitespace($closeIndex);
                $afterParenthesis = $tokens[$afterParenthesisIndex];

                if ($afterParenthesis->equals(self::SINGLE_COLON)) {
                    $tokens[$afterParenthesisIndex] = new Token('{');
                } else {
                    continue;
                }
            }

            if ($token->isGivenKind([T_ENDIF, T_ENDFOREACH, T_ENDWHILE])) {
                $tokens[$index] = new Token('}');
            } else {
                continue;
            }
        }
    }

    /**
     * @param Tokens $tokens
     *
     * @author Eddilbert Macharia <edd.cowan@gmail.com>(eddmash.com)
     */
    private function fixElse(Tokens $tokens)
    {
        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(T_ELSE)) {
                continue;
            }

            $prevIndex = $tokens->getPrevNonWhitespace($index);
            $prevToken = $tokens[$prevIndex];
            if (!$prevToken->equals('}')) {
                // insert closing brace
                $tokens->insertAt($prevIndex + 1, [new Token([T_WHITESPACE, ' ']), new Token('}')]);
                $this->fixElse($tokens);

                break;
            }

            $tokenAfterParenthesis = $tokens[$tokens->getNextMeaningfulToken($index)];
            if ($tokenAfterParenthesis->equals(self::SINGLE_COLON)) {
                // insert closing brace
                $tokens[$tokens->getNextMeaningfulToken($index)] = new Token('{');
            }
        }
    }
}
