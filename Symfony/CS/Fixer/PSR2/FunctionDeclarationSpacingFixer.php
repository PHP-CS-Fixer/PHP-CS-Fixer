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

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class FunctionDeclarationSpacingFixer implements FixerInterface
{
    private $singleLineWhitespaceOptions = array('whitespaces' => " \t");

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $startParenthesisIndex = null;
            $tokens->getNextTokenOfKind($index, array('('), $startParenthesisIndex);
            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
            $startBraceIndex = null;
            $startBraceToken = $tokens->getNextTokenOfKind($endParenthesisIndex, array(';', '{'), $startBraceIndex);

            if ('{' === $startBraceToken->content) {
                // fix single-line whitespace before {
                // eg: `function foo(){}` => `function foo() {}`
                // eg: `function foo()   {}` => `function foo() {}`
                if (
                    !$tokens[$startBraceIndex - 1]->isWhitespace() ||
                    $tokens[$startBraceIndex - 1]->isWhitespace($this->singleLineWhitespaceOptions)
                ) {
                    $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
                }
            }

            $afterParenthesisIndex = null;
            $afterParenthesisToken = $tokens->getNextNonWhitespace($endParenthesisIndex, array(), $afterParenthesisIndex);

            if ($afterParenthesisToken->isGivenKind(T_USE)) {
                $useStartParenthesisIndex = null;
                $tokens->getNextTokenOfKind($afterParenthesisIndex, array('('), $useStartParenthesisIndex);
                $useEndParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $useStartParenthesisIndex);

                // fix whitespace after T_USE
                $tokens->ensureWhitespaceAtIndex($afterParenthesisIndex + 1, 0, ' ');

                // remove single-line edge whitespaces inside use parentheses
                $this->fixParenthesisInnerEdge($tokens, $useStartParenthesisIndex, $useEndParenthesisIndex);

                // fix whitespace before T_USE
                $tokens->ensureWhitespaceAtIndex($afterParenthesisIndex - 1, 1, ' ');
            }

            // remove single-line edge whitespaces inside parameters list parentheses
            $this->fixParenthesisInnerEdge($tokens, $startParenthesisIndex, $endParenthesisIndex);

            // remove whitespace before (
            // eg: `function foo () {}` => `function foo() {}`
            if ($tokens[$startParenthesisIndex - 1]->isWhitespace()) {
                $tokens[$startParenthesisIndex - 1]->clear();
            }

            // fix whitespace after T_FUNCTION
            // eg: `function     foo() {}` => `function foo() {}`
            $tokens->ensureWhitespaceAtIndex($index + 1, 0, ' ');
        }

        return $tokens->generateCode();
    }

    private function fixParenthesisInnerEdge(Tokens $tokens, $start, $end)
    {
        // remove single-line whitespace before )
        if ($tokens[$end - 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens[$end - 1]->clear();
        }

        // remove single-line whitespace after (
        if ($tokens[$start + 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens[$start + 1]->clear();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        // defined in PSR2 generally (¶1 and ¶6)
        return FixerInterface::PSR2_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'function_declaration';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Spaces should be properly placed in a function declaration.';
    }
}
