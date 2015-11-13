<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Mark Scherer
 * @author Lucas Manzke <lmanzke@outlook.com>
 */
final class MethodArgumentDefaultValueFixer extends AbstractFixer
{
    private $argumentBoundaryTokens = array('(', ',', ')', ';', '{', '}');
    private $variableOrTerminatorTokens = array(array(T_VARIABLE), ')', ';', '{', '}');
    private $argumentTerminatorTokens = array(',', ')', ';', '{');
    private $defaultValueTokens = array('=', ')', ';', '{');
    private $immediateDefaultValueTokens = array('=', ')', ',', ';', '{');

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
            if ($tokens[$tokens->getNextMeaningfulToken($nextRelevantTokenIndex)]->equalsAny($this->argumentTerminatorTokens)) {
                $lastNonDefaultArgumentIndex = $nextRelevantTokenIndex;
            }

            $nextRelevantTokenIndex = $tokens->getNextTokenOfKind($nextRelevantTokenIndex, $this->variableOrTerminatorTokens);
        }

        return $lastNonDefaultArgumentIndex;
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
     * @param int    $nextVariableIndex
     */
    private function removeDefaultArgument(Tokens $tokens, $nextVariableIndex)
    {
        $currentIndex = $nextVariableIndex;

        $prevMeaningfulTokenIndex = $tokens->getPrevMeaningfulToken($nextVariableIndex);
        $nextMeaningfulTokenIndex = $tokens->getNextTokenOfKind($nextVariableIndex, array(array(310, 'null'), ',', ')'));

        if (
            $tokens[$nextMeaningfulTokenIndex]->equals(array(310, 'null')) &&
            !$tokens[$prevMeaningfulTokenIndex]->equalsAny($this->argumentBoundaryTokens)
        ) {
            return; //ignore typehinted null default values
        }

        while (!$tokens[$currentIndex + 1]->equalsAny($this->argumentTerminatorTokens)) {
            $tokens[++$currentIndex]->clear();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
