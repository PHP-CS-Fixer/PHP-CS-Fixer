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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Mark Scherer
 * @author Lucas Manzke <lmanzke@outlook.com>
 * @author Gregor Harlan <gharlan@web.de>
 */
final class NoUnreachableDefaultArgumentValueFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($i = 0, $l = $tokens->count(); $i < $l; ++$i) {
            if (!$tokens[$i]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $startIndex = $tokens->getNextTokenOfKind($i, array('('));
            $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);

            $this->fixFunctionDefinition($tokens, $startIndex, $i);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'In function arguments there must not be arguments with default values before non-default ones.',
            array(
                new CodeSample(
                    '<?php
function example($foo = "two words", $bar) {}
'
                ),
            ),
            null,
            'Modifies the signature of functions; therefore risky when using systems (such as some Symfony components) that rely on those (for example through reflection).'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function fixFunctionDefinition(Tokens $tokens, $startIndex, $endIndex)
    {
        $lastArgumentIndex = $this->getLastNonDefaultArgumentIndex($tokens, $startIndex, $endIndex);

        if (!$lastArgumentIndex) {
            return;
        }

        for ($i = $lastArgumentIndex; $i > $startIndex; --$i) {
            $token = $tokens[$i];

            if ($token->isGivenKind(T_VARIABLE)) {
                $lastArgumentIndex = $i;
                continue;
            }

            if (!$token->equals('=') || $this->isTypehintedNullableVariable($tokens, $i)) {
                continue;
            }

            $endIndex = $tokens->getPrevTokenOfKind($lastArgumentIndex, array(','));
            $endIndex = $tokens->getPrevMeaningfulToken($endIndex);
            $this->removeDefaultArgument($tokens, $i, $endIndex);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     *
     * @return int|null
     */
    private function getLastNonDefaultArgumentIndex(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $token = $tokens[$i];

            if ($token->equals('=')) {
                $i = $tokens->getPrevMeaningfulToken($i);
                continue;
            }

            if ($token->isGivenKind(T_VARIABLE) && !$this->isEllipsis($tokens, $i)) {
                return $i;
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $variableIndex
     *
     * @return bool
     */
    private function isEllipsis(Tokens $tokens, $variableIndex)
    {
        if (!defined('T_ELLIPSIS')) {
            return $tokens[$tokens->getPrevMeaningfulToken($variableIndex)]->equals('.');
        }

        return $tokens[$tokens->getPrevMeaningfulToken($variableIndex)]->isGivenKind(T_ELLIPSIS);
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function removeDefaultArgument(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($i = $startIndex; $i <= $endIndex;) {
            $tokens[$i]->clear();
            $this->clearWhitespacesBeforeIndex($tokens, $i);
            $i = $tokens->getNextMeaningfulToken($i);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  Index of "="
     *
     * @return bool
     */
    private function isTypehintedNullableVariable(Tokens $tokens, $index)
    {
        $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];

        if (!$nextToken->equals(array(T_STRING, 'null'), false)) {
            return false;
        }

        $variableIndex = $tokens->getPrevMeaningfulToken($index);

        $searchTokens = array(',', '(', array(T_STRING), array(CT::T_ARRAY_TYPEHINT));
        $typehintKinds = array(T_STRING, CT::T_ARRAY_TYPEHINT);

        if (defined('T_CALLABLE')) {
            $searchTokens[] = array(T_CALLABLE);
            $typehintKinds[] = T_CALLABLE;
        }

        $prevIndex = $tokens->getPrevTokenOfKind($variableIndex, $searchTokens);

        return $tokens[$prevIndex]->isGivenKind($typehintKinds);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function clearWhitespacesBeforeIndex(Tokens $tokens, $index)
    {
        $token = $tokens[$index - 1];

        if ($token->isGivenKind(T_WHITESPACE)) {
            $token->clear();
        }
    }
}
