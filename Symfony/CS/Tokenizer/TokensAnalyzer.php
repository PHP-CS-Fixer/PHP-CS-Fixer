<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer;

/**
 * Analyzer of Tokens collection.
 *
 * Its role is to provide the ability to analyze collection.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class TokensAnalyzer
{
    /**
     * Tokens collection instance.
     *
     * @var Tokens
     */
    private $tokens;

    public function __construct(Tokens $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Get indexes of methods and properties in classy code (classes, interfaces and traits).
     *
     * @return array
     */
    public function getClassyElements()
    {
        $tokens = $this->tokens;

        $tokens->rewind();

        $elements = array();
        $inClass = false;
        $curlyBracesLevel = 0;
        $bracesLevel = 0;

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                continue;
            }

            if (!$inClass) {
                $inClass = $token->isClassy();
                continue;
            }

            if ($token->equals('(')) {
                ++$bracesLevel;
                continue;
            }

            if ($token->equals(')')) {
                --$bracesLevel;
                continue;
            }

            if ($token->equals('{')) {
                ++$curlyBracesLevel;
                continue;
            }

            if ($token->equals('}')) {
                --$curlyBracesLevel;

                if (0 === $curlyBracesLevel) {
                    $inClass = false;
                }

                continue;
            }

            if (1 !== $curlyBracesLevel || !$token->isArray()) {
                continue;
            }

            if (T_VARIABLE === $token->getId() && 0 === $bracesLevel) {
                $elements[$index] = array('token' => $token, 'type' => 'property');
                continue;
            }

            if (T_FUNCTION === $token->getId()) {
                $elements[$index] = array('token' => $token, 'type' => 'method');
            }
        }

        return $elements;
    }

    /**
     * Get indexes of namespace uses.
     *
     * @param bool $perNamespace Return namespace uses per namespace
     *
     * @return array|array[]
     */
    public function getImportUseIndexes($perNamespace = false)
    {
        $tokens = $this->tokens;

        $tokens->rewind();

        $uses = array();
        $namespaceIndex = 0;

        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_NAMESPACE)) {
                $nextTokenIndex = $tokens->getNextTokenOfKind($index, array(';', '{'));
                $nextToken = $tokens[$nextTokenIndex];

                if ($nextToken->equals('{')) {
                    $index = $nextTokenIndex;
                }

                if ($perNamespace) {
                    ++$namespaceIndex;
                }

                continue;
            }

            if ($token->isGivenKind(T_USE)) {
                $uses[$namespaceIndex][] = $index;
            }
        }

        if (!$perNamespace && isset($uses[$namespaceIndex])) {
            return $uses[$namespaceIndex];
        }

        return $uses;
    }

    /**
     * Check if there is an array at given index.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isArray($index)
    {
        return $this->tokens[$index]->isGivenKind(array(T_ARRAY, CT_ARRAY_SQUARE_BRACE_OPEN));
    }

    /**
     * Check if the array at index is multiline.
     *
     * This only checks the root-level of the array.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isArrayMultiLine($index)
    {
        $tokens = $this->tokens;

        // Skip only when its an array, for short arrays we need the brace for correct
        // level counting
        if ($tokens[$index]->isGivenKind(T_ARRAY)) {
            $index = $tokens->getNextMeaningfulToken($index);
        }

        $endIndex = $tokens[$index]->equals('(')
            ? $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index)
            : $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index)
        ;

        for (++$index; $index < $endIndex; ++$index) {
            $token      = $tokens[$index];
            $blockType  = Tokens::detectBlockType($token);

            if ($blockType && $blockType['isStart']) {
                $index = $tokens->findBlockEnd($blockType['type'], $index);
                continue;
            }

            if ($token->isGivenKind(T_WHITESPACE) && false !== strpos($token->getContent(), "\n")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if there is a lambda function under given index.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isLambda($index)
    {
        $tokens = $this->tokens;
        $token  = $tokens[$index];

        if (!$token->isGivenKind(T_FUNCTION)) {
            throw new \LogicException('No T_FUNCTION at given index');
        }

        $nextIndex = $tokens->getNextNonWhitespace($index);
        $nextToken = $tokens[$nextIndex];

        if (!$nextToken->equals('(')) {
            return false;
        }

        $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);

        $nextIndex = $tokens->getNextNonWhitespace($endParenthesisIndex);
        $nextToken = $tokens[$nextIndex];

        if (!$nextToken->equalsAny(array('{', array(CT_USE_LAMBDA)))) {
            return false;
        }

        return true;
    }
}
