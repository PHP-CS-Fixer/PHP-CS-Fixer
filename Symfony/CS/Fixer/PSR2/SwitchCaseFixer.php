<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Jian Wu <jianwu1868@gmail.com>
 */
class SwitchCaseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0, $limit = count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];

            // if token is not a structure element - continue
            if (!$token->isGivenKind(T_SWITCH)) {
                continue;
            }

            $startSwitchIndex = $tokens->getNextTokenOfKind($index, array(':', '{'));
            if (null !== $tokens->getPrevTokenOfKind($startSwitchIndex - 1, array(T_CASE, T_DEFAULT))) {
                // Bug in code where switch block is missing { or :
                // Should we throw exception and exit here or just continue?
                continue;
            }

            $startSwitchToken = $tokens[$startSwitchIndex];

            if ($startSwitchToken->equals(':')) {
                $endSwitchIndex = $tokens->getNextTokenOfKind($startSwitchIndex, array(T_ENDSWITCH));
            } else {
                // Assuming there is no other open or close braces inside the switch block
                // TODO consider nested code block with open and close braces
                $endSwitchIndex = $tokens->getNextTokenOfKind($startSwitchIndex, array('}'));
            }

            $indent = Tokens::detectIndent($tokens, $index);
            $tokens->ensureWhitespaceAtIndex($endSwitchIndex - 1, 1, "\n".$indent);
            $lastSemicolonIndex = $tokens->getPrevTokenOfKind($endSwitchIndex - 1, array(';', '}'));

            for ($nestIndex = $lastSemicolonIndex; $nestIndex >= $startSwitchIndex; --$nestIndex) {
                $nestToken = $tokens[$nestIndex];

                if ($nestToken->equalsAny(array(';', ':'))) {
                    $nextNonWhitespaceNestTokenIndex = $tokens->getNextNonWhitespace($nestIndex);
                    $nextNonWhitespaceNestToken      = $tokens[$nextNonWhitespaceNestTokenIndex];

                    if (!$nextNonWhitespaceNestToken->isComment()) {
                        $whitespace                      = "\n".$indent;
                        $previousNonWhitespaceTokenIndex = $tokens->getPrevNonWhitespace($nextNonWhitespaceNestTokenIndex);
                        if ($nextNonWhitespaceNestToken->isGivenKind(array(T_CASE, T_DEFAULT))) {
                            $whitespace .= '    ';
                        } elseif ($nextNonWhitespaceNestToken->equals('}')) {
                            continue;
                        } else {
                            // T_BREAK is taken care of here.
                            if ($nestToken->equals(':')) {
                                $tokens->removeLeadingWhitespace($nestIndex);
                            }

                            $whitespace .= '        ';
                        }
                        $tokens->removeTrailingWhitespace($previousNonWhitespaceTokenIndex);
                        $tokens->ensureWhitespaceAtIndex($previousNonWhitespaceTokenIndex, 1, $whitespace);
                    }
                }
            }

            // fix indent near opening brace
            if (isset($tokens[$startSwitchIndex + 2]) && $tokens[$startSwitchIndex + 2]->equals('}')) {
                $tokens->ensureWhitespaceAtIndex($startSwitchIndex + 1, 0, "\n".$indent);
            } else {
                $nextToken              = $tokens[$startSwitchIndex + 1];
                $nextNonWhitespaceToken = $tokens[$tokens->getNextNonWhitespace($startSwitchIndex)];

                // set indent only if it is not a case, when comment is following { in same line
                if (
                    !$nextNonWhitespaceToken->isComment()
                    || !($nextToken->isWhitespace() && $nextToken->isWhitespace(array('whitespaces' => " \t")))
                ) {
                    $tokens->ensureWhitespaceAtIndex($startSwitchIndex + 1, 0, "\n".$indent.'    ');
                }
            }

            // reset loop limit due to collection change
            $limit = count($tokens);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should run after BracesFixer
        return -26;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The body of switch should be properly indented.';
    }
}
