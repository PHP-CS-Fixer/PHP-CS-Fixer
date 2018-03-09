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
 * @author Eddilbert Macharia <edd.cowan@gmail.com>
 */
final class NoAlternativeSyntaxFixer extends AbstractFixer
{
    const SINGLE_COLON = ':';

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Replace control structure alternative syntax to use braces.',
            [
                new CodeSample(
                    "<?php\nif(true):echo 't';else:echo 'f';endif;\n"
                ),
                new CodeSample(
                    "<?php\nwhile(true):echo 'red';endwhile;\n"
                ),
                new CodeSample(
                    "<?php\nforeach(array('a') as \$item):echo 'xc';endforeach;\n"
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(
            [
                T_IF,
                T_ENDIF,
                T_ELSE,
                T_ELSEIF,
                T_WHILE,
                T_ENDWHILE,
                T_FOREACH,
                T_ENDFOREACH,
            ]
        );
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
     * Handle the elsif(): cases.
     *
     * @param Tokens $tokens
     *
     * @author Eddilbert Macharia <edd.cowan@gmail.com>
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
                // stop, and rescan the tokens again
                // taking into account the new tokens added
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
     * Handle both extremes of the control structures.
     * e.g. if(): or endif;.
     *
     * @param Tokens $tokens
     *
     * @author Eddilbert Macharia <edd.cowan@gmail.com>
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
     * Handle the else:.
     *
     * @param Tokens $tokens
     *
     * @author Eddilbert Macharia <edd.cowan@gmail.com>
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

            $tokenAfterParenthesisIndex = $tokens->getNextMeaningfulToken($index);
            $tokenAfterParenthesis = $tokens[$tokenAfterParenthesisIndex];
            if ($tokenAfterParenthesis->equals(self::SINGLE_COLON)) {
                // insert closing brace
                $tokens[$tokenAfterParenthesisIndex] = new Token('{');
            }
        }
    }
}
