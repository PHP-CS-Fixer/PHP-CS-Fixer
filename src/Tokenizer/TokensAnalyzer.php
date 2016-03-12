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

namespace PhpCsFixer\Tokenizer;

/**
 * Analyzer of Tokens collection.
 *
 * Its role is to provide the ability to analyze collection.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class TokensAnalyzer
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
     * @return array[]
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

            if (0 === $bracesLevel && $token->isGivenKind(T_VARIABLE)) {
                $elements[$index] = array('token' => $token, 'type' => 'property');
                continue;
            }

            if ($token->isGivenKind(T_FUNCTION)) {
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
        if (!$this->isArray($index)) {
            throw new \InvalidArgumentException('Not an array at given index');
        }

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
            $token = $tokens[$index];
            $blockType = Tokens::detectBlockType($token);

            if ($blockType && $blockType['isStart']) {
                $index = $tokens->findBlockEnd($blockType['type'], $index);
                continue;
            }

            if (
                $token->isWhitespace() &&
                !$tokens[$index - 1]->isGivenKind(T_END_HEREDOC) &&
                false !== strpos($token->getContent(), "\n")
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the attributes of the method under the given index.
     *
     * The array has the following items:
     * 'visibility' int|null  T_PRIVATE, T_PROTECTED or T_PUBLIC
     * 'static'     bool
     * 'abstract'   bool
     * 'final'      bool
     *
     * @param int $index Token index of the method (T_FUNCTION)
     *
     * @return array
     */
    public function getMethodAttributes($index)
    {
        $tokens = $this->tokens;
        $token = $tokens[$index];

        if (!$token->isGivenKind(T_FUNCTION)) {
            throw new \LogicException(sprintf('No T_FUNCTION at given index %d, got %s', $index, $token->getName()));
        }

        $attributes = array(
            'visibility' => null,
            'static' => false,
            'abstract' => false,
            'final' => false,
        );

        for ($i = $index; $i >= 0; --$i) {
            $tokenIndex = $tokens->getPrevMeaningfulToken($i);
            if (null === $tokenIndex) {
                break;
            }

            $i = $tokenIndex;
            $token = $tokens[$tokenIndex];

            if ($token->isGivenKind(array(T_STATIC))) {
                $attributes['static'] = true;
                continue;
            }

            if ($token->isGivenKind(array(T_FINAL))) {
                $attributes['final'] = true;
                continue;
            }

            if ($token->isGivenKind(array(T_ABSTRACT))) {
                $attributes['abstract'] = true;
                continue;
            }

            // visibility

            if ($token->isGivenKind(array(T_PRIVATE))) {
                $attributes['visibility'] = T_PRIVATE;
                continue;
            }

            if ($token->isGivenKind(array(T_PROTECTED))) {
                $attributes['visibility'] = T_PROTECTED;
                continue;
            }

            if ($token->isGivenKind(array(T_PUBLIC))) {
                $attributes['visibility'] = T_PUBLIC;
                continue;
            }

            // found a meaningful token that is not part of
            // the function signature; stop looking
            break;
        }

        return $attributes;
    }

    /**
     * Check if the function under given index is a lambda.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isLambda($index)
    {
        $tokens = $this->tokens;
        $token = $tokens[$index];

        if (!$token->isGivenKind(T_FUNCTION)) {
            throw new \LogicException(sprintf('No T_FUNCTION at given index %d, got %s', $index, $token->getName()));
        }

        $startParenthesisIndex = $tokens->getNextMeaningfulToken($index);
        $startParenthesisToken = $tokens[$startParenthesisIndex];

        // skip & for `function & () {}` syntax
        if ($startParenthesisToken->equals('&')) {
            $startParenthesisIndex = $tokens->getNextMeaningfulToken($startParenthesisIndex);
            $startParenthesisToken = $tokens[$startParenthesisIndex];
        }

        return $startParenthesisToken->equals('(');
    }

    /**
     * Checks if there is an unary successor operator under given index.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isUnarySuccessorOperator($index)
    {
        static $allowedPrevToken = array(
            ']',
            array(T_STRING),
            array(T_VARIABLE),
            array(CT_DYNAMIC_PROP_BRACE_CLOSE),
            array(CT_DYNAMIC_VAR_BRACE_CLOSE),
        );

        $tokens = $this->tokens;
        $token = $tokens[$index];

        if (!$token->isGivenKind(array(T_INC, T_DEC))) {
            return false;
        }

        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

        return $prevToken->equalsAny($allowedPrevToken);
    }

    /**
     * Checks if there is an unary predecessor operator under given index.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isUnaryPredecessorOperator($index)
    {
        static $potentialSuccessorOperator = array(T_INC, T_DEC);

        static $potentialBinaryOperator = array('+', '-', '&');

        static $otherOperators;
        if (null === $otherOperators) {
            $otherOperators = array('!', '~', '@');
            if (defined('T_ELLIPSIS')) {
                $otherOperators[] = array(T_ELLIPSIS);
            }
        }

        static $disallowedPrevTokens;
        if (null === $disallowedPrevTokens) {
            $disallowedPrevTokens = array(
                ']',
                '}',
                ')',
                '"',
                '`',
                array(CT_ARRAY_SQUARE_BRACE_CLOSE),
                array(CT_DYNAMIC_PROP_BRACE_CLOSE),
                array(CT_DYNAMIC_VAR_BRACE_CLOSE),
                array(T_CLASS_C),
                array(T_CONSTANT_ENCAPSED_STRING),
                array(T_DEC),
                array(T_DIR),
                array(T_DNUMBER),
                array(T_FILE),
                array(T_FUNC_C),
                array(T_INC),
                array(T_LINE),
                array(T_LNUMBER),
                array(T_METHOD_C),
                array(T_NS_C),
                array(T_STRING),
                array(T_VARIABLE),
            );
            if (defined('T_TRAIT_C')) {
                $disallowedPrevTokens[] = array(T_TRAIT_C);
            }
        }

        $tokens = $this->tokens;
        $token = $tokens[$index];

        if ($token->isGivenKind($potentialSuccessorOperator)) {
            return !$this->isUnarySuccessorOperator($index);
        }

        if ($token->equalsAny($otherOperators)) {
            return true;
        }

        if (!$token->equalsAny($potentialBinaryOperator)) {
            return false;
        }

        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

        if (!$prevToken->equalsAny($disallowedPrevTokens)) {
            return true;
        }

        if (!$token->equals('&') || !$prevToken->isGivenKind(T_STRING)) {
            return false;
        }

        static $searchTokens = array(
            ';',
            '{',
            '}',
            array(T_FUNCTION),
            array(T_OPEN_TAG),
            array(T_OPEN_TAG_WITH_ECHO),
        );
        $prevToken = $tokens[$tokens->getPrevTokenOfKind($index, $searchTokens)];

        return $prevToken->isGivenKind(T_FUNCTION);
    }

    /**
     * Checks if there is a binary operator under given index.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isBinaryOperator($index)
    {
        static $nonArrayOperators = array(
            '=' => true,
            '*' => true,
            '/' => true,
            '%' => true,
            '<' => true,
            '>' => true,
            '|' => true,
            '^' => true,
        );

        static $potentialUnaryNonArrayOperators = array(
            '+' => true,
            '-' => true,
            '&' => true,
        );

        static $arrayOperators;
        if (null === $arrayOperators) {
            $arrayOperators = array(
                T_AND_EQUAL => true,            // &=
                T_BOOLEAN_AND => true,          // &&
                T_BOOLEAN_OR => true,           // ||
                T_CONCAT_EQUAL => true,         // .=
                T_DIV_EQUAL => true,            // /=
                T_DOUBLE_ARROW => true,         // =>
                T_IS_EQUAL => true,             // ==
                T_IS_GREATER_OR_EQUAL => true,  // >=
                T_IS_IDENTICAL => true,         // ===
                T_IS_NOT_EQUAL => true,         // !=, <>
                T_IS_NOT_IDENTICAL => true,     // !==
                T_IS_SMALLER_OR_EQUAL => true,  // <=
                T_LOGICAL_AND => true,          // and
                T_LOGICAL_OR => true,           // or
                T_LOGICAL_XOR => true,          // xor
                T_MINUS_EQUAL => true,          // -=
                T_MOD_EQUAL => true,            // %=
                T_MUL_EQUAL => true,            // *=
                T_OR_EQUAL => true,             // |=
                T_PLUS_EQUAL => true,           // +=
                T_SL => true,                   // <<
                T_SL_EQUAL => true,             // <<=
                T_SR => true,                   // >>
                T_SR_EQUAL => true,             // >>=
                T_XOR_EQUAL => true,            // ^=
            );

            if (defined('T_POW')) {
                $arrayOperators[T_POW] = true;       // **
                $arrayOperators[T_POW_EQUAL] = true; // **=
            }

            if (defined('T_SPACESHIP')) {
                $arrayOperators[T_SPACESHIP] = true; // <=>
            }

            if (defined('T_COALESCE')) {
                $arrayOperators[T_COALESCE] = true;  // ??
            }
        }

        $tokens = $this->tokens;
        $token = $tokens[$index];

        if ($token->isArray()) {
            return isset($arrayOperators[$token->getId()]);
        }

        if (isset($nonArrayOperators[$token->getContent()])) {
            return true;
        }

        if (isset($potentialUnaryNonArrayOperators[$token->getContent()])) {
            return !$this->isUnaryPredecessorOperator($index);
        }

        return false;
    }

    /**
     * Check if `T_WHILE` token at given index is `do { ... } while ();` syntax
     * and not `while () { ...}`.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isWhilePartOfDoWhile($index)
    {
        $tokens = $this->tokens;
        $token = $tokens[$index];

        if (!$token->isGivenKind(T_WHILE)) {
            throw new \LogicException(sprintf('No T_WHILE at given index %d, got %s', $index, $token->getName()));
        }

        $startParenthesisIndex = $tokens->getNextMeaningfulToken($index);
        $startParenthesisToken = $tokens[$startParenthesisIndex];

        if (!$startParenthesisToken->equals('(')) {
            return false;
        }

        $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);

        $nextIndex = $tokens->getNextMeaningfulToken($endParenthesisIndex);
        $nextToken = $tokens[$nextIndex];

        if ($nextToken->equals(';')) {
            return true;
        }

        return false;
    }
}
