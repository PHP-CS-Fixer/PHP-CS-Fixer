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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use SplFileInfo;

/**
 * Make sure there is one blank line above and below a method.
 *
 * The exception is when a method is the first or last item in a 'classy'.
 *
 * @author SpacePossum
 */
final class MethodSeparationFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
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
    public function fix(SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isClassy()) {
                continue;
            }

            // figure out where the classy starts
            $classStart = $tokens->getNextTokenOfKind($index, array('{'));

            // figure out where the classy ends
            $classEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classStart);

            if ($tokens[$index]->isGivenKind(T_INTERFACE)) {
                $this->fixInterface($tokens, $classStart, $classEnd);
            } else {
                // classes and traits can be fixed the same way
                $this->fixClass($tokens, $tokensAnalyzer, $classStart, $classEnd);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Methods must be separated with one blank line.',
            array(
                new CodeSample(
                    '<?php
final class Sample
{
    protected function foo()
    {
    }
    protected function bar()
    {
    }
}
'
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // Must run before BracesFixer and IndentationTypeFixer fixers because this fixer
        // might add line breaks to the code without indenting.
        return 55;
    }

    /**
     * @param Tokens         $tokens
     * @param TokensAnalyzer $tokensAnalyzer
     * @param int            $classStart
     * @param int            $classEnd
     */
    private function fixClass(Tokens $tokens, TokensAnalyzer $tokensAnalyzer, $classStart, $classEnd)
    {
        for ($index = $classEnd; $index > $classStart; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION) || $tokensAnalyzer->isLambda($index)) {
                continue;
            }

            $attributes = $tokensAnalyzer->getMethodAttributes($index);
            if (true === $attributes['abstract']) {
                $methodEnd = $tokens->getNextTokenOfKind($index, array(';'));
            } else {
                $methodStart = $tokens->getNextTokenOfKind($index, array('{'));
                $methodEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $methodStart, true);
            }

            $this->fixSpaceBelowMethod($tokens, $classEnd, $methodEnd);
            $this->fixSpaceAboveMethod($tokens, $classStart, $index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $classStart
     * @param int    $classEnd
     */
    private function fixInterface(Tokens $tokens, $classStart, $classEnd)
    {
        for ($index = $classEnd; $index > $classStart; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $methodEnd = $tokens->getNextTokenOfKind($index, array(';'));

            $this->fixSpaceBelowMethod($tokens, $classEnd, $methodEnd);
            $this->fixSpaceAboveMethod($tokens, $classStart, $index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $classEnd
     * @param int    $methodEnd
     */
    private function fixSpaceBelowMethod(Tokens $tokens, $classEnd, $methodEnd)
    {
        $nextNotWhite = $tokens->getNextNonWhitespace($methodEnd);
        $this->correctLineBreaks($tokens, $methodEnd, $nextNotWhite, $nextNotWhite === $classEnd ? 1 : 2);
    }

    /**
     * Fix spacing above a method signature.
     *
     * Deals with comments, PHPDocs and spaces above the method with respect to the position of the method in the class.
     *
     * @param Tokens $tokens
     * @param int    $classStart  index of the class Token the method is in
     * @param int    $methodIndex index of the method to fix
     */
    private function fixSpaceAboveMethod(Tokens $tokens, $classStart, $methodIndex)
    {
        static $methodAttr = array(T_PRIVATE, T_PROTECTED, T_PUBLIC, T_ABSTRACT, T_FINAL, T_STATIC);

        // find out where the method signature starts
        $firstMethodAttrIndex = $methodIndex;
        for ($i = $methodIndex; $i > $classStart; --$i) {
            $nonWhiteAbove = $tokens->getNonWhitespaceSibling($i, -1);
            if (null !== $nonWhiteAbove && $tokens[$nonWhiteAbove]->isGivenKind($methodAttr)) {
                $firstMethodAttrIndex = $nonWhiteAbove;
            } else {
                break;
            }
        }

        // deal with comments above a method
        if ($tokens[$nonWhiteAbove]->isGivenKind(T_COMMENT)) {
            if (1 === $firstMethodAttrIndex - $nonWhiteAbove) {
                // no white space found between comment and method start
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstMethodAttrIndex, 1);

                return;
            }

            // $tokens[$nonWhiteAbove+1] is always a white space token here
            if (substr_count($tokens[$nonWhiteAbove + 1]->getContent(), "\n") > 1) {
                // more than one line break, always bring it back to 2 line breaks between the method start and what is above it
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstMethodAttrIndex, 2);

                return;
            }

            // there are 2 cases:
            if ($tokens[$nonWhiteAbove - 1]->isWhitespace() && substr_count($tokens[$nonWhiteAbove - 1]->getContent(), "\n") > 0) {
                // 1. The comment is meant for the method (although not a PHPDoc),
                //    make sure there is one line break between the method and the comment...
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstMethodAttrIndex, 1);
                //    ... and make sure there is blank line above the comment (with the exception when it is directly after a class opening)
                $nonWhiteAbove = $this->findCommentBlockStart($tokens, $nonWhiteAbove);
                $nonWhiteAboveComment = $tokens->getNonWhitespaceSibling($nonWhiteAbove, -1);

                $this->correctLineBreaks($tokens, $nonWhiteAboveComment, $nonWhiteAbove, $nonWhiteAboveComment === $classStart ? 1 : 2);
            } else {
                // 2. The comment belongs to the code above the method,
                //    make sure there is a blank line above the method (i.e. 2 line breaks)
                $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstMethodAttrIndex, 2);
            }

            return;
        }

        // deal with method without a PHPDoc above it
        if (false === $tokens[$nonWhiteAbove]->isGivenKind(T_DOC_COMMENT)) {
            $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstMethodAttrIndex, $nonWhiteAbove === $classStart ? 1 : 2);

            return;
        }

        // there should be one linebreak between the method signature and the PHPDoc above it
        $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstMethodAttrIndex, 1);

        // there should be one blank line between the PHPDoc and whatever is above (with the exception when it is directly after a class opening)
        $nonWhiteAbovePHPDoc = $tokens->getNonWhitespaceSibling($nonWhiteAbove, -1);
        $this->correctLineBreaks($tokens, $nonWhiteAbovePHPDoc, $nonWhiteAbove, $nonWhiteAbovePHPDoc === $classStart ? 1 : 2);
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     * @param int    $reqLineCount
     */
    private function correctLineBreaks(Tokens $tokens, $startIndex, $endIndex, $reqLineCount = 2)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        ++$startIndex;
        $numbOfWhiteTokens = $endIndex - $startIndex;
        if (0 === $numbOfWhiteTokens) {
            $tokens->insertAt($startIndex, new Token(array(T_WHITESPACE, str_repeat($lineEnding, $reqLineCount))));

            return;
        }

        $lineBreakCount = $this->getLineBreakCount($tokens, $startIndex, $endIndex);
        if ($reqLineCount === $lineBreakCount) {
            return;
        }

        if ($lineBreakCount < $reqLineCount) {
            $tokens[$startIndex]->setContent(str_repeat($lineEnding, $reqLineCount - $lineBreakCount).$tokens[$startIndex]->getContent());

            return;
        }

        // $lineCount = > $reqLineCount : check the one Token case first since this one will be true most of the time
        if (1 === $numbOfWhiteTokens) {
            $tokens[$startIndex]->setContent(preg_replace('/\r\n|\n/', '', $tokens[$startIndex]->getContent(), $lineBreakCount - $reqLineCount));

            return;
        }

        // $numbOfWhiteTokens = > 1
        $toReplaceCount = $lineBreakCount - $reqLineCount;
        for ($i = $startIndex; $i < $endIndex && $toReplaceCount > 0; ++$i) {
            $tokenLineCount = substr_count($tokens[$i]->getContent(), "\n");
            if ($tokenLineCount > 0) {
                $tokens[$i]->setContent(preg_replace('/\r\n|\n/', '', $tokens[$i]->getContent(), min($toReplaceCount, $tokenLineCount)));
                $toReplaceCount -= $tokenLineCount;
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $whiteStart
     * @param int    $whiteEnd
     *
     * @return int
     */
    private function getLineBreakCount(Tokens $tokens, $whiteStart, $whiteEnd)
    {
        $lineCount = 0;
        for ($i = $whiteStart; $i < $whiteEnd; ++$i) {
            $lineCount += substr_count($tokens[$i]->getContent(), "\n");
        }

        return $lineCount;
    }

    /**
     * @param Tokens $tokens
     * @param int    $commentIndex
     *
     * @return int
     */
    private function findCommentBlockStart(Tokens $tokens, $commentIndex)
    {
        $start = $commentIndex;
        for ($i = $commentIndex - 1; $i > 0; --$i) {
            if ($tokens[$i]->isComment()) {
                $start = $i;
                continue;
            }

            if (!$tokens[$i]->isWhitespace() || $this->getLineBreakCount($tokens, $i, $i + 1) > 1) {
                break;
            }
        }

        return $start;
    }
}
