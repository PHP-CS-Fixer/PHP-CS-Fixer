<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Mark Scherer
 * @author Lucas Manzke <lmanzke@outlook.com>
 */
final class MethodArgumentDefaultValueFixer extends AbstractFixer
{
    private $argumentBoundaryTokens = array('(', ',', ';', '{', '}');
    private $variableOrTerminatorTokens = array(array(T_VARIABLE), ';', '{', '}');
    private $argumentTerminatorTokens = array(',', ')', ';', '{');
    private $defaultValueTokens = array('=', ';', '{');
    private $immediateDefaultValueTokens = array('=', ',', ';', '{');

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'In method arguments there must not be arguments with default values before non-default ones.';
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        for ($i = 0, $l = $tokens->count(); $i < $l; ++$i) {
            if ($tokens[$i]->isGivenKind(T_FUNCTION)) {
                $this->fixFunctionDefinition($tokens, $i);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixFunctionDefinition(Tokens $tokens, $index)
    {
        $examinedIndex = $tokens->getNextTokenOfKind($index, $this->argumentBoundaryTokens);
        $lastNonDefaultArgumentIndex = $this->getLastNonDefaultArgumentIndex($tokens, $index);

        while (
            $examinedIndex < $lastNonDefaultArgumentIndex &&
            $this->hasDefaultValueAfterIndex($tokens, $examinedIndex)
        ) {
            $nextRelevantIndex = $tokens->getNextTokenOfKind($examinedIndex, $this->variableOrTerminatorTokens);

            if (!$tokens[$nextRelevantIndex]->isGivenKind(T_VARIABLE)) {
                break;
            }

            if (
                $this->isDefaultArgumentAfterIndex($tokens, $nextRelevantIndex - 1) &&
                $nextRelevantIndex - 1 < $lastNonDefaultArgumentIndex
            ) {
                $this->removeDefaultArgument($tokens, $nextRelevantIndex);
            }
            $examinedIndex = $nextRelevantIndex;
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int|null
     */
    private function getLastNonDefaultArgumentIndex(Tokens $tokens, $index)
    {
        $nextRelevantTokenIndex = $tokens->getNextTokenOfKind($index, $this->variableOrTerminatorTokens);

        if (null === $nextRelevantTokenIndex) {
            return;
        }

        $lastNonDefaultArgumentIndex = null;

        while ($tokens[$nextRelevantTokenIndex]->isGivenKind(T_VARIABLE)) {
            if (!$tokens[$tokens->getNextMeaningfulToken($nextRelevantTokenIndex)]->equals('=') &&
                !$this->isEllipsis($tokens, $nextRelevantTokenIndex)
            ) {
                $lastNonDefaultArgumentIndex = $nextRelevantTokenIndex;
            }

            $nextRelevantTokenIndex = $tokens->getNextTokenOfKind($nextRelevantTokenIndex, $this->variableOrTerminatorTokens);
        }

        return $lastNonDefaultArgumentIndex;
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
     * @param int    $index
     *
     * @return bool
     */
    private function hasDefaultValueAfterIndex(Tokens $tokens, $index)
    {
        $nextTokenIndex = $tokens->getNextTokenOfKind($index, $this->defaultValueTokens);
        $nextToken = $tokens[$nextTokenIndex];

        return $nextToken->equals('=');
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function isDefaultArgumentAfterIndex(Tokens $tokens, $index)
    {
        $nextTokenIndex = $tokens->getNextTokenOfKind($index, $this->immediateDefaultValueTokens);
        $nextToken = $tokens[$nextTokenIndex];

        return $nextToken->equals('=');
    }

    /**
     * @param Tokens $tokens
     * @param int    $variableIndex
     */
    private function removeDefaultArgument(Tokens $tokens, $variableIndex)
    {
        if ($this->isTypehintedNullableVariable($tokens, $variableIndex)) {
            return;
        }

        $argumentEndIndex = $this->findArgumentEndIndex($tokens, $variableIndex);
        $currentIndex = $tokens->getNextMeaningfulToken($variableIndex);

        while ($currentIndex < $argumentEndIndex) {
            $tokens[$currentIndex]->clear();
            $this->clearWhitespacesBeforeIndex($tokens, $currentIndex);
            $currentIndex = $tokens->getNextMeaningfulToken($currentIndex);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $variableIndex
     *
     * @return bool
     */
    private function isTypehintedNullableVariable(Tokens $tokens, $variableIndex)
    {
        $typehintedTokens = array(array(T_STRING), array(CT_ARRAY_TYPEHINT), ',', '(');
        $typehintedKinds = array(T_STRING, CT_ARRAY_TYPEHINT);

        if (defined('T_CALLABLE')) {
            $typehintedTokens[] = array(T_CALLABLE);
            $typehintedKinds[] = T_CALLABLE;
        }

        $prevMeaningfulTokenIndex = $tokens->getPrevTokenOfKind($variableIndex, $typehintedTokens);

        if (!$tokens[$prevMeaningfulTokenIndex]->isGivenKind($typehintedKinds)) {
            return false;
        }

        $nextMeaningfulTokenIndex = $tokens->getNextTokenOfKind($variableIndex, array(array(T_STRING), ',', ')'));
        $lowerCasedNextContent = strtolower($tokens[$nextMeaningfulTokenIndex]->getContent());

        return 'null' === $lowerCasedNextContent;
    }

    /**
     * @param Tokens $tokens
     * @param int    $variableIndex
     *
     * @return int
     */
    private function findArgumentEndIndex(Tokens $tokens, $variableIndex)
    {
        $currentIndex = $variableIndex;
        while (!$tokens[$currentIndex]->equalsAny($this->argumentTerminatorTokens)) {
            ++$currentIndex;
            if ($tokens[$currentIndex]->equals('(')) {
                $currentIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $currentIndex) + 1;
            }

            if ($tokens[$currentIndex]->equals('[')) {
                $currentIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $currentIndex) + 1;
            }
        }

        return $currentIndex;
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

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::SYMFONY_LEVEL;
    }
}
