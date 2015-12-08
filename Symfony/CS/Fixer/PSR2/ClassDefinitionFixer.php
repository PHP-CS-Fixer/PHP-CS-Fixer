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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for part of the rules defined in PSR2 ¶4.1 Extends and Implements.
 *
 * @author SpacePossum
 */
final class ClassDefinitionFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isClassy()) {
                continue;
            }

            $this->fixClassDefinition($tokens, $index, $tokens->getNextTokenOfKind($index, array('{')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Whitespace around the key words of a class, trait or interfaces definition should be one space.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the TrailingSpacesFixer
        return 21;
    }

    /**
     * @param Tokens $tokens
     * @param int    $start      Class definition token start index
     * @param int    $classyOpen Class definition token end index
     */
    private function fixClassDefinition(Tokens $tokens, $start, $classyOpen)
    {
        // check if there is a `implements` part in the definition, since there are rules for it in PSR 2.
        $implementsInfo = $this->getMultiLineInfo($tokens, $start, $classyOpen);

        // 4.1 The extends and implements keywords MUST be declared on the same line as the class name.
        if ($implementsInfo['numberOfInterfaces'] > 1 && $implementsInfo['multiLine']) {
            $classyOpen += $this->ensureWhiteSpaceSeparation($tokens, $start, $implementsInfo['breakAt']);
            $this->fixMultiLineImplements($tokens, $implementsInfo['breakAt'], $classyOpen);
        } else {
            $classyOpen -= $tokens[$classyOpen - 1]->isWhitespace() ? 2 : 1;
            $this->ensureWhiteSpaceSeparation($tokens, $start, $classyOpen);
        }
    }

    /**
     * Returns an array with `implements` data.
     *
     * Returns array:
     * * int  'breakAt'            index of the Token of type T_IMPLEMENTS for the definition, or 0
     * * int  'numberOfInterfaces'
     * * bool 'multiLine'
     *
     * @param Tokens $tokens
     * @param int    $start
     * @param int    $classyOpen
     *
     * @return array
     */
    private function getMultiLineInfo(Tokens $tokens, $start, $classyOpen)
    {
        $implementsInfo = array('breakAt' => 0, 'numberOfInterfaces' => 0, 'multiLine' => false);
        $breakAtToken = $tokens->findGivenKind($tokens[$start]->isGivenKind(T_INTERFACE) ? T_EXTENDS : T_IMPLEMENTS, $start, $classyOpen);
        if (count($breakAtToken) < 1) {
            return $implementsInfo;
        }

        $implementsInfo['breakAt'] = key($breakAtToken);
        $classyOpen = $tokens->getPrevNonWhitespace($classyOpen);
        for ($j = $implementsInfo['breakAt'] + 1; $j < $classyOpen; ++$j) {
            if ($tokens[$j]->isGivenKind(T_STRING)) {
                ++$implementsInfo['numberOfInterfaces'];
                continue;
            }

            if (!$implementsInfo['multiLine'] && ($tokens[$j]->isWhitespace() || $tokens[$j]->isComment()) && false !== strpos($tokens[$j]->getContent(), "\n")) {
                $implementsInfo['multiLine'] = true;
            }
        }

        return $implementsInfo;
    }

    /**
     * Fix spacing between lines following `implements`.
     *
     * PSR2 4.1 Lists of implements MAY be split across multiple lines, where each subsequent line is indented once.
     * When doing so, the first item in the list MUST be on the next line, and there MUST be only one interface per line.
     *
     * @param Tokens $tokens
     * @param int    $breakAt
     * @param int    $classyOpen
     */
    private function fixMultiLineImplements(Tokens $tokens, $breakAt, $classyOpen)
    {
        // implements should be followed by a line break, but we allow a comments before that,
        // the lines after 'implements' are always build up as (comment|whitespace)*T_STRING{1}(comment|whitespace)*','
        // after fixing it must be (whitespace indent)(comment)*T_STRING{1}(comment)*','
        for ($index = $classyOpen - 1; $index > $breakAt - 1; --$index) {
            if ($tokens[$index]->isWhitespace()) {
                if ($tokens[$index + 1]->equals(',')) {
                    $tokens[$index]->clear();
                } elseif (
                    $tokens[$index + 1]->isComment()
                    && ' ' !== $tokens[$index]->getContent()
                    && !($tokens[$index - 1]->isComment() && "\n" === substr($tokens[$index]->getContent(), 0, 1))
                ) {
                    $tokens[$index]->setContent(' ');
                }
            }

            if ($tokens[$index]->isGivenKind(T_STRING)) {
                $index = $this->ensureOnNewLine($tokens, $index);
            }
        }
    }

    /**
     * Make sure the tokens are separated by a single space.
     *
     * @param Tokens $tokens
     * @param int    $start
     * @param int    $end
     *
     * @return int number tokens inserted by the method before the end token
     */
    private function ensureWhiteSpaceSeparation(Tokens $tokens, $start, $end)
    {
        $insertCount = 0;
        for ($i = $end; $i > $start; --$i) {
            if ($tokens[$i]->isWhitespace()) {
                $content = $tokens[$i]->getContent();
                if (
                    ' ' !== $content
                    && !($tokens[$i - 1]->isComment() && "\n" === $content[0])
                ) {
                    $tokens[$i]->setContent(' ');
                }
                continue;
            }

            if ($tokens[$i - 1]->isWhitespace() || "\n" === substr($tokens[$i - 1]->getContent(), -1)) {
                continue;
            }

            if ($tokens[$i - 1]->isComment() || $tokens[$i]->isComment()) {
                $tokens->insertAt($i, new Token(array(T_WHITESPACE, ' ')));
                ++$insertCount;
                continue;
            }
        }

        return $insertCount;
    }

    private function ensureOnNewLine(Tokens $tokens, $index)
    {
        // while not whitespace and not comment go back
        for (--$index; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(array(T_NS_SEPARATOR, T_STRING))) {
                break;
            }
        }

        if ("\n" === substr($tokens[$index]->getContent(), -1)) {
            return $index;
        }

        if (!$tokens[$index]->isWhitespace()) {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, "\n")));

            return $index;
        }

        if (false !== strpos($tokens[$index]->getContent(), "\n")) {
            return $index;
        }

        $tokens[$index]->setContent($tokens[$index]->getContent()."\n");

        return $index;
    }
}
