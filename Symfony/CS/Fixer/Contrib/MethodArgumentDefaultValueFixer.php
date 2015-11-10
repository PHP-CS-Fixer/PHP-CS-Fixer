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
    private $functionDefinitionTerminatorTokens = array(')', ';', '{', '}');
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
        while (
            $this->hasNonDefaultArgumentAfterIndex($tokens, $examinedIndex) &&
            $this->hasDefaultValueAfterIndex($tokens, $examinedIndex)
        ) {
            $nextRelevantIndex = $this->findNextVariableOrTokenOfKind($tokens, $examinedIndex, $this->functionDefinitionTerminatorTokens);

            if (!$tokens[$nextRelevantIndex]->isGivenKind(T_VARIABLE)) {
                break;
            }

            if (
                $this->isDefaultArgumentAfterIndex($tokens, $nextRelevantIndex - 1) &&
                $this->hasNonDefaultArgumentAfterIndex($tokens, $nextRelevantIndex - 1)
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
     * @return bool
     */
    private function hasNonDefaultArgumentAfterIndex(Tokens $tokens, $index)
    {
        $nextRelevantTokenIndex = $this->findNextVariableOrTokenOfKind($tokens, $index, $this->functionDefinitionTerminatorTokens);

        if (null === $nextRelevantTokenIndex) {
            return false;
        }

        while (!$tokens[$nextRelevantTokenIndex]->equalsAny($this->functionDefinitionTerminatorTokens)) {
            if ($tokens[$tokens->getNextMeaningfulToken($nextRelevantTokenIndex)]->equalsAny($this->argumentTerminatorTokens)) {
                return true;
            }

            $nextRelevantTokenIndex = $this->findNextVariableOrTokenOfKind($tokens, $nextRelevantTokenIndex, $this->functionDefinitionTerminatorTokens);
        }

        return false;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     * @param array  $relevantTokens
     *
     * @return int|null
     */
    private function findNextVariableOrTokenOfKind(Tokens $tokens, $index, array $relevantTokens)
    {
        $variableIndices = array_keys($tokens->findGivenKind(T_VARIABLE));

        $nextVariableIndex = $this->getFirstValueBiggerThan($variableIndices, $index);
        $nextRelevantTokenIndex = $tokens->getNextTokenOfKind($index, $relevantTokens);

        if (null === $nextVariableIndex) {
            return $nextRelevantTokenIndex;
        }

        if (null === $nextRelevantTokenIndex) {
            return $nextVariableIndex;
        }

        return ($nextVariableIndex < $nextRelevantTokenIndex) ? $nextVariableIndex : $nextRelevantTokenIndex;
    }

    /**
     * @param array $values
     * @param int   $minimumValue
     *
     * @return int|null
     */
    private function getFirstValueBiggerThan(array $values, $minimumValue)
    {
        foreach ($values as $value) {
            if ($value > $minimumValue) {
                return $value;
            }
        }
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

        while (!$tokens[$currentIndex + 1]->equalsAny($this->argumentTerminatorTokens)) {
            $tokens[++$currentIndex]->clear();
        }
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return -49;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }
}
