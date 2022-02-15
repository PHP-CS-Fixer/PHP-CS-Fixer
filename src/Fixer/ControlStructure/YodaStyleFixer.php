<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Bram Gotink <bram@gotink.me>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class YodaStyleFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var array<int|string, Token>
     */
    private $candidatesMap;

    /**
     * @var array<int|string, null|bool>
     */
    private $candidateTypesConfiguration;

    /**
     * @var array<int|string>
     */
    private $candidateTypes;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->resolveConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Write conditions in Yoda style (`true`), non-Yoda style (`[\'equal\' => false, \'identical\' => false, \'less_and_greater\' => false]`) or ignore those conditions (`null`) based on configuration.',
            [
                new CodeSample(
                    '<?php
    if ($a === null) {
        echo "null";
    }
'
                ),
                new CodeSample(
                    '<?php
    $b = $c != 1;  // equal
    $a = 1 === $b; // identical
    $c = $c > 3;   // less than
',
                    [
                        'equal' => true,
                        'identical' => false,
                        'less_and_greater' => null,
                    ]
                ),
                new CodeSample(
                    '<?php
return $foo === count($bar);
',
                    [
                        'always_move_variable' => true,
                    ]
                ),
                new CodeSample(
                    '<?php
    // Enforce non-Yoda style.
    if (null === $a) {
        echo "null";
    }
',
                    [
                        'equal' => false,
                        'identical' => false,
                        'less_and_greater' => false,
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after IsNullFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound($this->candidateTypes);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->fixTokens($tokens);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('equal', 'Style for equal (`==`, `!=`) statements.'))
                ->setAllowedTypes(['bool', 'null'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('identical', 'Style for identical (`===`, `!==`) statements.'))
                ->setAllowedTypes(['bool', 'null'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('less_and_greater', 'Style for less and greater than (`<`, `<=`, `>`, `>=`) statements.'))
                ->setAllowedTypes(['bool', 'null'])
                ->setDefault(null)
                ->getOption(),
            (new FixerOptionBuilder('always_move_variable', 'Whether variables should always be on non assignable side when applying Yoda style.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    /**
     * Finds the end of the right-hand side of the comparison at the given
     * index.
     *
     * The right-hand side ends when an operator with a lower precedence is
     * encountered or when the block level for `()`, `{}` or `[]` goes below
     * zero.
     *
     * @param Tokens $tokens The token list
     * @param int    $index  The index of the comparison
     *
     * @return int The last index of the right-hand side of the comparison
     */
    private function findComparisonEnd(Tokens $tokens, int $index): int
    {
        ++$index;
        $count = \count($tokens);

        while ($index < $count) {
            $token = $tokens[$index];

            if ($token->isGivenKind([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                ++$index;

                continue;
            }

            if ($this->isOfLowerPrecedence($token)) {
                break;
            }

            $block = Tokens::detectBlockType($token);

            if (null === $block) {
                ++$index;

                continue;
            }

            if (!$block['isStart']) {
                break;
            }

            $index = $tokens->findBlockEnd($block['type'], $index) + 1;
        }

        $prev = $tokens->getPrevMeaningfulToken($index);

        return $tokens[$prev]->isGivenKind(T_CLOSE_TAG) ? $tokens->getPrevMeaningfulToken($prev) : $prev;
    }

    /**
     * Finds the start of the left-hand side of the comparison at the given
     * index.
     *
     * The left-hand side ends when an operator with a lower precedence is
     * encountered or when the block level for `()`, `{}` or `[]` goes below
     * zero.
     *
     * @param Tokens $tokens The token list
     * @param int    $index  The index of the comparison
     *
     * @return int The first index of the left-hand side of the comparison
     */
    private function findComparisonStart(Tokens $tokens, int $index): int
    {
        --$index;
        $nonBlockFound = false;

        while (0 <= $index) {
            $token = $tokens[$index];

            if ($token->isGivenKind([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                --$index;

                continue;
            }

            if ($token->isGivenKind([CT::T_NAMED_ARGUMENT_COLON])) {
                break;
            }

            if ($this->isOfLowerPrecedence($token)) {
                break;
            }

            $block = Tokens::detectBlockType($token);

            if (null === $block) {
                --$index;
                $nonBlockFound = true;

                continue;
            }

            if (
                $block['isStart']
                || ($nonBlockFound && Tokens::BLOCK_TYPE_CURLY_BRACE === $block['type']) // closing of structure not related to the comparison
            ) {
                break;
            }

            $index = $tokens->findBlockStart($block['type'], $index) - 1;
        }

        return $tokens->getNextMeaningfulToken($index);
    }

    private function fixTokens(Tokens $tokens): Tokens
    {
        for ($i = \count($tokens) - 1; $i > 1; --$i) {
            if ($tokens[$i]->isGivenKind($this->candidateTypes)) {
                $yoda = $this->candidateTypesConfiguration[$tokens[$i]->getId()];
            } elseif (
                ($tokens[$i]->equals('<') && \in_array('<', $this->candidateTypes, true))
                || ($tokens[$i]->equals('>') && \in_array('>', $this->candidateTypes, true))
            ) {
                $yoda = $this->candidateTypesConfiguration[$tokens[$i]->getContent()];
            } else {
                continue;
            }

            $fixableCompareInfo = $this->getCompareFixableInfo($tokens, $i, $yoda);

            if (null === $fixableCompareInfo) {
                continue;
            }

            $i = $this->fixTokensCompare(
                $tokens,
                $fixableCompareInfo['left']['start'],
                $fixableCompareInfo['left']['end'],
                $i,
                $fixableCompareInfo['right']['start'],
                $fixableCompareInfo['right']['end']
            );
        }

        return $tokens;
    }

    /**
     * Fixes the comparison at the given index.
     *
     * A comparison is considered fixed when
     * - both sides are a variable (e.g. $a === $b)
     * - neither side is a variable (e.g. self::CONST === 3)
     * - only the right-hand side is a variable (e.g. 3 === self::$var)
     *
     * If the left-hand side and right-hand side of the given comparison are
     * swapped, this function runs recursively on the previous left-hand-side.
     *
     * @return int an upper bound for all non-fixed comparisons
     */
    private function fixTokensCompare(
        Tokens $tokens,
        int $startLeft,
        int $endLeft,
        int $compareOperatorIndex,
        int $startRight,
        int $endRight
    ): int {
        $type = $tokens[$compareOperatorIndex]->getId();
        $content = $tokens[$compareOperatorIndex]->getContent();

        if (\array_key_exists($type, $this->candidatesMap)) {
            $tokens[$compareOperatorIndex] = clone $this->candidatesMap[$type];
        } elseif (\array_key_exists($content, $this->candidatesMap)) {
            $tokens[$compareOperatorIndex] = clone $this->candidatesMap[$content];
        }

        $right = $this->fixTokensComparePart($tokens, $startRight, $endRight);
        $left = $this->fixTokensComparePart($tokens, $startLeft, $endLeft);

        for ($i = $startRight; $i <= $endRight; ++$i) {
            $tokens->clearAt($i);
        }

        for ($i = $startLeft; $i <= $endLeft; ++$i) {
            $tokens->clearAt($i);
        }

        $tokens->insertAt($startRight, $left);
        $tokens->insertAt($startLeft, $right);

        return $startLeft;
    }

    private function fixTokensComparePart(Tokens $tokens, int $start, int $end): Tokens
    {
        $newTokens = $tokens->generatePartialCode($start, $end);
        $newTokens = $this->fixTokens(Tokens::fromCode(sprintf('<?php %s;', $newTokens)));
        $newTokens->clearAt(\count($newTokens) - 1);
        $newTokens->clearAt(0);
        $newTokens->clearEmptyTokens();

        return $newTokens;
    }

    private function getCompareFixableInfo(Tokens $tokens, int $index, bool $yoda): ?array
    {
        $left = $this->getLeftSideCompareFixableInfo($tokens, $index);
        $right = $this->getRightSideCompareFixableInfo($tokens, $index);

        if (!$yoda && $this->isOfLowerPrecedenceAssignment($tokens[$tokens->getNextMeaningfulToken($right['end'])])) {
            return null;
        }

        if ($this->isListStatement($tokens, $left['start'], $left['end']) || $this->isListStatement($tokens, $right['start'], $right['end'])) {
            return null; // do not fix lists assignment inside statements
        }

        /** @var bool $strict */
        $strict = $this->configuration['always_move_variable'];
        $leftSideIsVariable = $this->isVariable($tokens, $left['start'], $left['end'], $strict);
        $rightSideIsVariable = $this->isVariable($tokens, $right['start'], $right['end'], $strict);

        if (!($leftSideIsVariable ^ $rightSideIsVariable)) {
            return null; // both are (not) variables, do not touch
        }

        if (!$strict) { // special handling for braces with not "always_move_variable"
            $leftSideIsVariable = $leftSideIsVariable && !$tokens[$left['start']]->equals('(');
            $rightSideIsVariable = $rightSideIsVariable && !$tokens[$right['start']]->equals('(');
        }

        return ($yoda && !$leftSideIsVariable) || (!$yoda && !$rightSideIsVariable)
            ? null
            : ['left' => $left, 'right' => $right]
        ;
    }

    private function getLeftSideCompareFixableInfo(Tokens $tokens, int $index): array
    {
        return [
            'start' => $this->findComparisonStart($tokens, $index),
            'end' => $tokens->getPrevMeaningfulToken($index),
        ];
    }

    private function getRightSideCompareFixableInfo(Tokens $tokens, int $index): array
    {
        return [
            'start' => $tokens->getNextMeaningfulToken($index),
            'end' => $this->findComparisonEnd($tokens, $index),
        ];
    }

    private function isListStatement(Tokens $tokens, int $index, int $end): bool
    {
        for ($i = $index; $i <= $end; ++$i) {
            if ($tokens[$i]->isGivenKind([T_LIST, CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN, CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether the given token has a lower precedence than `T_IS_EQUAL`
     * or `T_IS_IDENTICAL`.
     *
     * @param Token $token The token to check
     *
     * @return bool Whether the token has a lower precedence
     */
    private function isOfLowerPrecedence(Token $token): bool
    {
        static $tokens;

        if (null === $tokens) {
            $tokens = [
                T_BOOLEAN_AND,  // &&
                T_BOOLEAN_OR,   // ||
                T_CASE,         // case
                T_DOUBLE_ARROW, // =>
                T_ECHO,         // echo
                T_GOTO,         // goto
                T_LOGICAL_AND,  // and
                T_LOGICAL_OR,   // or
                T_LOGICAL_XOR,  // xor
                T_OPEN_TAG,     // <?php
                T_OPEN_TAG_WITH_ECHO,
                T_PRINT,        // print
                T_RETURN,       // return
                T_THROW,        // throw
                T_COALESCE,
                T_YIELD,        // yield
            ];
        }

        static $otherTokens = [
            // bitwise and, or, xor
            '&', '|', '^',
            // ternary operators
            '?', ':',
            // end of PHP statement
            ',', ';',
        ];

        return $this->isOfLowerPrecedenceAssignment($token) || $token->isGivenKind($tokens) || $token->equalsAny($otherTokens);
    }

    /**
     * Checks whether the given assignment token has a lower precedence than `T_IS_EQUAL`
     * or `T_IS_IDENTICAL`.
     */
    private function isOfLowerPrecedenceAssignment(Token $token): bool
    {
        static $tokens;

        if (null === $tokens) {
            $tokens = [
                T_AND_EQUAL,      // &=
                T_CONCAT_EQUAL,   // .=
                T_DIV_EQUAL,      // /=
                T_MINUS_EQUAL,    // -=
                T_MOD_EQUAL,      // %=
                T_MUL_EQUAL,      // *=
                T_OR_EQUAL,       // |=
                T_PLUS_EQUAL,     // +=
                T_POW_EQUAL,      // **=
                T_SL_EQUAL,       // <<=
                T_SR_EQUAL,       // >>=
                T_XOR_EQUAL,      // ^=
                T_COALESCE_EQUAL, // ??=
            ];
        }

        return $token->equals('=') || $token->isGivenKind($tokens);
    }

    /**
     * Checks whether the tokens between the given start and end describe a
     * variable.
     *
     * @param Tokens $tokens The token list
     * @param int    $start  The first index of the possible variable
     * @param int    $end    The last index of the possible variable
     * @param bool   $strict Enable strict variable detection
     *
     * @return bool Whether the tokens describe a variable
     */
    private function isVariable(Tokens $tokens, int $start, int $end, bool $strict): bool
    {
        $tokenAnalyzer = new TokensAnalyzer($tokens);

        if ($start === $end) {
            return $tokens[$start]->isGivenKind(T_VARIABLE);
        }

        if ($tokens[$start]->equals('(')) {
            return true;
        }

        if ($strict) {
            for ($index = $start; $index <= $end; ++$index) {
                if (
                    $tokens[$index]->isCast()
                    || $tokens[$index]->isGivenKind(T_INSTANCEOF)
                    || $tokens[$index]->equals('!')
                    || $tokenAnalyzer->isBinaryOperator($index)
                ) {
                    return false;
                }
            }
        }

        $index = $start;

        // handle multiple braces around statement ((($a === 1)))
        while (
            $tokens[$index]->equals('(')
            && $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index) === $end
        ) {
            $index = $tokens->getNextMeaningfulToken($index);
            $end = $tokens->getPrevMeaningfulToken($end);
        }

        $expectString = false;

        while ($index <= $end) {
            $current = $tokens[$index];
            if ($current->isComment() || $current->isWhitespace() || $tokens->isEmptyAt($index)) {
                ++$index;

                continue;
            }

            // check if this is the last token
            if ($index === $end) {
                return $current->isGivenKind($expectString ? T_STRING : T_VARIABLE);
            }

            if ($current->isGivenKind([T_LIST, CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN, CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE])) {
                return false;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($index);
            $next = $tokens[$nextIndex];

            // self:: or ClassName::
            if ($current->isGivenKind(T_STRING) && $next->isGivenKind(T_DOUBLE_COLON)) {
                $index = $tokens->getNextMeaningfulToken($nextIndex);

                continue;
            }

            // \ClassName
            if ($current->isGivenKind(T_NS_SEPARATOR) && $next->isGivenKind(T_STRING)) {
                $index = $nextIndex;

                continue;
            }

            // ClassName\
            if ($current->isGivenKind(T_STRING) && $next->isGivenKind(T_NS_SEPARATOR)) {
                $index = $nextIndex;

                continue;
            }

            // $a-> or a-> (as in $b->a->c)
            if ($current->isGivenKind([T_STRING, T_VARIABLE]) && $next->isObjectOperator()) {
                $index = $tokens->getNextMeaningfulToken($nextIndex);
                $expectString = true;

                continue;
            }

            // $a[...], a[...] (as in $c->a[$b]), $a{...} or a{...} (as in $c->a{$b})
            if (
                $current->isGivenKind($expectString ? T_STRING : T_VARIABLE)
                && $next->equalsAny(['[', [CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN, '{']])
            ) {
                $index = $tokens->findBlockEnd(
                    $next->equals('[') ? Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE : Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE,
                    $nextIndex
                );

                if ($index === $end) {
                    return true;
                }

                $index = $tokens->getNextMeaningfulToken($index);

                if (!$tokens[$index]->equalsAny(['[', [CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN, '{']]) && !$tokens[$index]->isObjectOperator()) {
                    return false;
                }

                $index = $tokens->getNextMeaningfulToken($index);
                $expectString = true;

                continue;
            }

            // $a(...) or $a->b(...)
            if ($strict && $current->isGivenKind([T_STRING, T_VARIABLE]) && $next->equals('(')) {
                return false;
            }

            // {...} (as in $a->{$b})
            if ($expectString && $current->isGivenKind(CT::T_DYNAMIC_PROP_BRACE_OPEN)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_DYNAMIC_PROP_BRACE, $index);
                if ($index === $end) {
                    return true;
                }

                $index = $tokens->getNextMeaningfulToken($index);

                if (!$tokens[$index]->isObjectOperator()) {
                    return false;
                }

                $index = $tokens->getNextMeaningfulToken($index);
                $expectString = true;

                continue;
            }

            break;
        }

        return !$this->isConstant($tokens, $start, $end);
    }

    private function isConstant(Tokens $tokens, int $index, int $end): bool
    {
        $expectArrayOnly = false;
        $expectNumberOnly = false;
        $expectNothing = false;

        for (; $index <= $end; ++$index) {
            $token = $tokens[$index];

            if ($token->isComment() || $token->isWhitespace()) {
                continue;
            }

            if ($expectNothing) {
                return false;
            }

            if ($expectArrayOnly) {
                if ($token->equalsAny(['(', ')', [CT::T_ARRAY_SQUARE_BRACE_CLOSE]])) {
                    continue;
                }

                return false;
            }

            if ($token->isGivenKind([T_ARRAY,  CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
                $expectArrayOnly = true;

                continue;
            }

            if ($expectNumberOnly && !$token->isGivenKind([T_LNUMBER, T_DNUMBER])) {
                return false;
            }

            if ($token->equals('-')) {
                $expectNumberOnly = true;

                continue;
            }

            if (
                $token->isGivenKind([T_LNUMBER, T_DNUMBER, T_CONSTANT_ENCAPSED_STRING])
                || $token->equalsAny([[T_STRING, 'true'], [T_STRING, 'false'], [T_STRING, 'null']])
            ) {
                $expectNothing = true;

                continue;
            }

            return false;
        }

        return true;
    }

    private function resolveConfiguration(): void
    {
        $candidateTypes = [];
        $this->candidatesMap = [];

        if (null !== $this->configuration['equal']) {
            // `==`, `!=` and `<>`
            $candidateTypes[T_IS_EQUAL] = $this->configuration['equal'];
            $candidateTypes[T_IS_NOT_EQUAL] = $this->configuration['equal'];
        }

        if (null !== $this->configuration['identical']) {
            // `===` and `!==`
            $candidateTypes[T_IS_IDENTICAL] = $this->configuration['identical'];
            $candidateTypes[T_IS_NOT_IDENTICAL] = $this->configuration['identical'];
        }

        if (null !== $this->configuration['less_and_greater']) {
            // `<`, `<=`, `>` and `>=`
            $candidateTypes[T_IS_SMALLER_OR_EQUAL] = $this->configuration['less_and_greater'];
            $this->candidatesMap[T_IS_SMALLER_OR_EQUAL] = new Token([T_IS_GREATER_OR_EQUAL, '>=']);

            $candidateTypes[T_IS_GREATER_OR_EQUAL] = $this->configuration['less_and_greater'];
            $this->candidatesMap[T_IS_GREATER_OR_EQUAL] = new Token([T_IS_SMALLER_OR_EQUAL, '<=']);

            $candidateTypes['<'] = $this->configuration['less_and_greater'];
            $this->candidatesMap['<'] = new Token('>');

            $candidateTypes['>'] = $this->configuration['less_and_greater'];
            $this->candidatesMap['>'] = new Token('<');
        }

        $this->candidateTypesConfiguration = $candidateTypes;
        $this->candidateTypes = array_keys($candidateTypes);
    }
}
