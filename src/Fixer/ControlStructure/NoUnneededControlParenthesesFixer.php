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
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
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
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 */
final class NoUnneededControlParenthesesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var int[]
     */
    private const BLOCK_TYPES = [
        Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE,
        Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
        Tokens::BLOCK_TYPE_CURLY_BRACE,
        Tokens::BLOCK_TYPE_DESTRUCTURING_SQUARE_BRACE,
        Tokens::BLOCK_TYPE_DYNAMIC_PROP_BRACE,
        Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE,
        Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE,
        Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
    ];

    private const BEFORE_TYPES = [
        ';',
        '{',
        '}',
        [T_OPEN_TAG],
        [T_OPEN_TAG_WITH_ECHO],
        [T_ECHO],
        [T_PRINT],
        [T_RETURN],
        [T_THROW],
        [T_YIELD],
        [T_YIELD_FROM],
        [T_BREAK],
        [T_CONTINUE],
        // won't be fixed, but true in concept, helpful for fast check
        [T_REQUIRE],
        [T_REQUIRE_ONCE],
        [T_INCLUDE],
        [T_INCLUDE_ONCE],
    ];

    private const NOOP_TYPES = [
        '$',
        [T_CONSTANT_ENCAPSED_STRING],
        [T_DNUMBER],
        [T_DOUBLE_COLON],
        [T_LNUMBER],
        [T_NS_SEPARATOR],
        [T_OBJECT_OPERATOR],
        [T_STRING],
        [T_VARIABLE],
        // magic constants
        [T_CLASS_C],
        [T_DIR],
        [T_FILE],
        [T_FUNC_C],
        [T_LINE],
        [T_METHOD_C],
        [T_NS_C],
        [T_TRAIT_C],
    ];

    private const CONFIG_OPTIONS = [
        'break',
        'clone',
        'continue',
        'echo_print',
        'negative_instanceof',
        'others',
        'return',
        'switch_case',
        'yield',
        'yield_from',
    ];

    private const TOKEN_TYPE_CONFIG_MAP = [
        T_BREAK => 'break',
        T_CASE => 'switch_case',
        T_CONTINUE => 'continue',
        T_ECHO => 'echo_print',
        T_PRINT => 'echo_print',
        T_RETURN => 'return',
        T_YIELD => 'yield',
        T_YIELD_FROM => 'yield_from',
    ];

    // handled by the `include` rule
    private const TOKEN_TYPE_NO_CONFIG = [
        T_REQUIRE,
        T_REQUIRE_ONCE,
        T_INCLUDE,
        T_INCLUDE_ONCE,
    ];

    private TokensAnalyzer $tokensAnalyzer;

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes unneeded parentheses around control statements.',
            [
                new CodeSample(
                    '<?php
while ($x) { while ($y) { break (2); } }
clone($a);
while ($y) { continue (2); }
echo("foo");
print("foo");
return (1 + 2);
switch ($a) { case($x); }
yield(2);
'
                ),
                new CodeSample(
                    '<?php
while ($x) { while ($y) { break (2); } }

clone($a);

while ($y) { continue (2); }
',
                    ['statements' => ['break', 'continue']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ConcatSpaceFixer, NoTrailingWhitespaceFixer.
     */
    public function getPriority(): int
    {
        return 30;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(['(', CT::T_BRACE_CLASS_INSTANTIATION_OPEN]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokens as $openIndex => $token) {
            if ($token->equals('(')) {
                $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
            } elseif ($token->isGivenKind(CT::T_BRACE_CLASS_INSTANTIATION_OPEN)) {
                $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_BRACE_CLASS_INSTANTIATION, $openIndex);
            } else {
                continue;
            }

            $beforeOpenIndex = $tokens->getPrevMeaningfulToken($openIndex);
            $afterCloseIndex = $tokens->getNextMeaningfulToken($closeIndex);

            // do a cheap check for negative case: `X()`

            if ($tokens->getNextMeaningfulToken($openIndex) === $closeIndex) {
                if ($this->isExitStatement($tokens, $beforeOpenIndex)) {
                    $this->removeUselessParenthesisPair($tokens, $beforeOpenIndex, $afterCloseIndex, $openIndex, $closeIndex, 'others');
                }

                continue;
            }

            // do a cheap check for negative case: `foo(1,2)`

            if ($this->isKnownNegativePre($tokens[$beforeOpenIndex])) {
                continue;
            }

            // check for the simple useless wrapped cases

            if ($this->isUselessWrapped($tokens, $beforeOpenIndex, $afterCloseIndex)) {
                $this->removeUselessParenthesisPair($tokens, $beforeOpenIndex, $afterCloseIndex, $openIndex, $closeIndex, $this->getConfigType($tokens, $beforeOpenIndex));

                continue;
            }

            // handle `clone` statements

            if ($this->isCloneStatement($tokens, $beforeOpenIndex)) {
                if ($this->isWrappedCloneArgument($tokens, $beforeOpenIndex, $openIndex, $closeIndex, $afterCloseIndex)) {
                    $this->removeUselessParenthesisPair($tokens, $beforeOpenIndex, $afterCloseIndex, $openIndex, $closeIndex, 'clone');
                }

                continue;
            }

            // handle `instance of` statements

            $instanceOfIndex = $this->getIndexOfInstanceOfStatement($tokens, $openIndex, $closeIndex);

            if (null !== $instanceOfIndex) {
                if ($this->isWrappedInstanceOf($tokens, $instanceOfIndex, $beforeOpenIndex, $openIndex, $closeIndex, $afterCloseIndex)) {
                    $this->removeUselessParenthesisPair(
                        $tokens,
                        $beforeOpenIndex,
                        $afterCloseIndex,
                        $openIndex,
                        $closeIndex,
                        $tokens[$beforeOpenIndex]->equals('!') ? 'negative_instanceof' : 'others'
                    );
                }

                continue;
            }

            // last checks deal with operators, do not swap around

            if ($this->isWrappedPartOfOperation($tokens, $beforeOpenIndex, $openIndex, $closeIndex, $afterCloseIndex)) {
                $this->removeUselessParenthesisPair($tokens, $beforeOpenIndex, $afterCloseIndex, $openIndex, $closeIndex, $this->getConfigType($tokens, $beforeOpenIndex));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $defaults = array_filter(
            self::CONFIG_OPTIONS,
            static function (string $option): bool {
                return 'negative_instanceof' !== $option && 'others' !== $option && 'yield_from' !== $option;
            }
        );

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('statements', 'List of control statements to fix.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset(self::CONFIG_OPTIONS)])
                ->setDefault(array_values($defaults))
                ->getOption(),
        ]);
    }

    private function isUselessWrapped(Tokens $tokens, int $beforeOpenIndex, int $afterCloseIndex): bool
    {
        return
            $this->isSingleStatement($tokens, $beforeOpenIndex, $afterCloseIndex)
            || $this->isWrappedFnBody($tokens, $beforeOpenIndex, $afterCloseIndex)
            || $this->isWrappedForElement($tokens, $beforeOpenIndex, $afterCloseIndex)
            || $this->isWrappedLanguageConstructArgument($tokens, $beforeOpenIndex, $afterCloseIndex)
            || $this->isWrappedSequenceElement($tokens, $beforeOpenIndex, $afterCloseIndex)
        ;
    }

    private function isExitStatement(Tokens $tokens, int $beforeOpenIndex): bool
    {
        return $tokens[$beforeOpenIndex]->isGivenKind(T_EXIT);
    }

    private function isCloneStatement(Tokens $tokens, int $beforeOpenIndex): bool
    {
        return $tokens[$beforeOpenIndex]->isGivenKind(T_CLONE);
    }

    private function isWrappedCloneArgument(Tokens $tokens, int $beforeOpenIndex, int $openIndex, int $closeIndex, int $afterCloseIndex): bool
    {
        $beforeOpenIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);

        if (
            !(
                $tokens[$beforeOpenIndex]->equals('?') // For BC reasons
                || $this->isSimpleAssignment($tokens, $beforeOpenIndex, $afterCloseIndex)
                || $this->isSingleStatement($tokens, $beforeOpenIndex, $afterCloseIndex)
                || $this->isWrappedFnBody($tokens, $beforeOpenIndex, $afterCloseIndex)
                || $this->isWrappedForElement($tokens, $beforeOpenIndex, $afterCloseIndex)
                || $this->isWrappedSequenceElement($tokens, $beforeOpenIndex, $afterCloseIndex)
            )
        ) {
            return false;
        }

        $newCandidateIndex = $tokens->getNextMeaningfulToken($openIndex);

        if ($tokens[$newCandidateIndex]->isGivenKind(T_NEW)) {
            $openIndex = $newCandidateIndex; // `clone (new X)`, `clone (new X())`, clone (new X(Y))`
        }

        return !$this->containsOperation($tokens, $openIndex, $closeIndex);
    }

    private function getIndexOfInstanceOfStatement(Tokens $tokens, int $openIndex, int $closeIndex): ?int
    {
        $instanceOfIndex = $tokens->findGivenKind(T_INSTANCEOF, $openIndex, $closeIndex);

        return 1 === \count($instanceOfIndex) ? array_key_first($instanceOfIndex) : null;
    }

    private function isWrappedInstanceOf(Tokens $tokens, int $instanceOfIndex, int $beforeOpenIndex, int $openIndex, int $closeIndex, int $afterCloseIndex): bool
    {
        if (
            $this->containsOperation($tokens, $openIndex, $instanceOfIndex)
            || $this->containsOperation($tokens, $instanceOfIndex, $closeIndex)
        ) {
            return false;
        }

        if ($tokens[$beforeOpenIndex]->equals('!')) {
            $beforeOpenIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);
        }

        return
            $this->isSimpleAssignment($tokens, $beforeOpenIndex, $afterCloseIndex)
            || $this->isSingleStatement($tokens, $beforeOpenIndex, $afterCloseIndex)
            || $this->isWrappedFnBody($tokens, $beforeOpenIndex, $afterCloseIndex)
            || $this->isWrappedForElement($tokens, $beforeOpenIndex, $afterCloseIndex)
            || $this->isWrappedSequenceElement($tokens, $beforeOpenIndex, $afterCloseIndex)
        ;
    }

    private function isWrappedPartOfOperation(Tokens $tokens, int $beforeOpenIndex, int $openIndex, int $closeIndex, int $afterCloseIndex): bool
    {
        if ($this->containsOperation($tokens, $openIndex, $closeIndex)) {
            return false;
        }

        $boundariesMoved = false;

        if ($this->isPreUnaryOperation($tokens, $beforeOpenIndex)) {
            $beforeOpenIndex = $this->getBeforePreUnaryOperation($tokens, $beforeOpenIndex);
            $boundariesMoved = true;
        }

        if ($this->isAccess($tokens, $afterCloseIndex)) {
            $afterCloseIndex = $this->getAfterAccess($tokens, $afterCloseIndex);
            $boundariesMoved = true;

            if ($this->tokensAnalyzer->isUnarySuccessorOperator($afterCloseIndex)) { // post unary operation are only valid here
                $afterCloseIndex = $tokens->getNextMeaningfulToken($afterCloseIndex);
            }
        }

        if ($boundariesMoved) {
            if ($this->isKnownNegativePre($tokens[$beforeOpenIndex])) {
                return false;
            }

            if ($this->isUselessWrapped($tokens, $beforeOpenIndex, $afterCloseIndex)) {
                return true;
            }
        }

        // check if part of some operation sequence

        $beforeIsBinaryOperation = $this->tokensAnalyzer->isBinaryOperator($beforeOpenIndex);
        $afterIsBinaryOperation = $this->tokensAnalyzer->isBinaryOperator($afterCloseIndex);

        if ($beforeIsBinaryOperation && $afterIsBinaryOperation) {
            return true; // `+ (x) +`
        }

        $beforeToken = $tokens[$beforeOpenIndex];
        $afterToken = $tokens[$afterCloseIndex];

        $beforeIsBlockOpenOrComma = $beforeToken->equals(',') || null !== $this->getBlock($tokens, $beforeOpenIndex, true);
        $afterIsBlockEndOrComma = $afterToken->equals(',') || null !== $this->getBlock($tokens, $afterCloseIndex, false);

        if (($beforeIsBlockOpenOrComma && $afterIsBinaryOperation) || ($beforeIsBinaryOperation && $afterIsBlockEndOrComma)) {
            // $beforeIsBlockOpenOrComma && $afterIsBlockEndOrComma is covered by `isWrappedSequenceElement`
            // `[ (x) +` or `+ (X) ]` or `, (X) +` or `+ (X) ,`

            return true;
        }

        $beforeIsStatementOpen = $beforeToken->equalsAny(self::BEFORE_TYPES) || $beforeToken->isGivenKind(T_CASE);
        $afterIsStatementEnd = $afterToken->equalsAny([';', [T_CLOSE_TAG]]);

        return
            ($beforeIsStatementOpen && $afterIsBinaryOperation) // `<?php (X) +`
            || ($beforeIsBinaryOperation && $afterIsStatementEnd) // `+ (X);`
        ;
    }

    // bounded `print|yield|yield from|require|require_once|include|include_once (X)`
    private function isWrappedLanguageConstructArgument(Tokens $tokens, int $beforeOpenIndex, int $afterCloseIndex): bool
    {
        if (!$tokens[$beforeOpenIndex]->isGivenKind([T_PRINT, T_YIELD, T_YIELD_FROM, T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE])) {
            return false;
        }

        $beforeOpenIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);

        return $this->isWrappedSequenceElement($tokens, $beforeOpenIndex, $afterCloseIndex);
    }

    // any of `<?php|<?|<?=|;|throw|return|... (X) ;|T_CLOSE`
    private function isSingleStatement(Tokens $tokens, int $beforeOpenIndex, int $afterCloseIndex): bool
    {
        if ($tokens[$beforeOpenIndex]->isGivenKind(T_CASE)) {
            return $tokens[$afterCloseIndex]->equalsAny([':', ';']); // `switch case`
        }

        return $tokens[$afterCloseIndex]->equalsAny([';', [T_CLOSE_TAG]]) && $tokens[$beforeOpenIndex]->equalsAny(self::BEFORE_TYPES);
    }

    private function isSimpleAssignment(Tokens $tokens, int $beforeOpenIndex, int $afterCloseIndex): bool
    {
        return $tokens[$beforeOpenIndex]->equals('=') && $tokens[$afterCloseIndex]->equalsAny([';', [T_CLOSE_TAG]]); // `= (X) ;`
    }

    private function isWrappedSequenceElement(Tokens $tokens, int $startIndex, int $endIndex): bool
    {
        $startIsComma = $tokens[$startIndex]->equals(',');
        $endIsComma = $tokens[$endIndex]->equals(',');

        if ($startIsComma && $endIsComma) {
            return true; // `,(X),`
        }

        $blockTypeStart = $this->getBlock($tokens, $startIndex, true);
        $blockTypeEnd = $this->getBlock($tokens, $endIndex, false);

        return
            ($startIsComma && null !== $blockTypeEnd) // `,(X)]`
            || ($endIsComma && null !== $blockTypeStart) // `[(X),`
            || (null !== $blockTypeEnd && null !== $blockTypeStart) // any type of `{(X)}`, `[(X)]` and `((X))`
        ;
    }

    // any of `for( (X); ;(X)) ;` note that the middle element is covered as 'single statement' as it is `; (X) ;`
    private function isWrappedForElement(Tokens $tokens, int $beforeOpenIndex, int $afterCloseIndex): bool
    {
        $forCandidateIndex = null;

        if ($tokens[$beforeOpenIndex]->equals('(') && $tokens[$afterCloseIndex]->equals(';')) {
            $forCandidateIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);
        } elseif ($tokens[$afterCloseIndex]->equals(')') && $tokens[$beforeOpenIndex]->equals(';')) {
            $forCandidateIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $afterCloseIndex);
            $forCandidateIndex = $tokens->getPrevMeaningfulToken($forCandidateIndex);
        }

        return null !== $forCandidateIndex && $tokens[$forCandidateIndex]->isGivenKind(T_FOR);
    }

    // `fn() => (X);`
    private function isWrappedFnBody(Tokens $tokens, int $beforeOpenIndex, int $afterCloseIndex): bool
    {
        if (!$tokens[$beforeOpenIndex]->isGivenKind(T_DOUBLE_ARROW)) {
            return false;
        }

        $beforeOpenIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);

        if ($tokens[$beforeOpenIndex]->isGivenKind(T_STRING)) {
            while (true) {
                $beforeOpenIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);

                if (!$tokens[$beforeOpenIndex]->isGivenKind([T_STRING, CT::T_TYPE_INTERSECTION, CT::T_TYPE_ALTERNATION])) {
                    break;
                }
            }

            if (!$tokens[$beforeOpenIndex]->isGivenKind(CT::T_TYPE_COLON)) {
                return false;
            }

            $beforeOpenIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);
        }

        if (!$tokens[$beforeOpenIndex]->equals(')')) {
            return false;
        }

        $beforeOpenIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $beforeOpenIndex);
        $beforeOpenIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);

        if ($tokens[$beforeOpenIndex]->isGivenKind(CT::T_RETURN_REF)) {
            $beforeOpenIndex = $tokens->getPrevMeaningfulToken($beforeOpenIndex);
        }

        if (!$tokens[$beforeOpenIndex]->isGivenKind(T_FN)) {
            return false;
        }

        return $tokens[$afterCloseIndex]->equalsAny([';', ',', [T_CLOSE_TAG]]);
    }

    private function isPreUnaryOperation(Tokens $tokens, int $index): bool
    {
        return $this->tokensAnalyzer->isUnaryPredecessorOperator($index) || $tokens[$index]->isCast();
    }

    private function getBeforePreUnaryOperation(Tokens $tokens, $index): int
    {
        do {
            $index = $tokens->getPrevMeaningfulToken($index);
        } while ($this->isPreUnaryOperation($tokens, $index));

        return $index;
    }

    // array access `(X)[` or `(X){` or object access `(X)->` or `(X)?->`
    private function isAccess(Tokens $tokens, int $index): bool
    {
        $token = $tokens[$index];

        return $token->isObjectOperator() || $token->equals('[') || $token->isGivenKind([CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN]);
    }

    private function getAfterAccess(Tokens $tokens, $index): int
    {
        while (true) {
            $block = $this->getBlock($tokens, $index, true);

            if (null !== $block) {
                $index = $tokens->findBlockEnd($block['type'], $index);
                $index = $tokens->getNextMeaningfulToken($index);

                continue;
            }

            if (
                $tokens[$index]->isObjectOperator()
                || $tokens[$index]->equalsAny(['$', [T_PAAMAYIM_NEKUDOTAYIM], [T_STRING], [T_VARIABLE]])
            ) {
                $index = $tokens->getNextMeaningfulToken($index);

                continue;
            }

            break;
        }

        return $index;
    }

    private function getBlock(Tokens $tokens, int $index, bool $isStart): ?array
    {
        $block = Tokens::detectBlockType($tokens[$index]);

        return null !== $block && $isStart === $block['isStart'] && \in_array($block['type'], self::BLOCK_TYPES, true) ? $block : null;
    }

    // cheap check on a tokens type before `(` of which we know the `(` will never be superfluous
    private function isKnownNegativePre(Token $token): bool
    {
        static $knownNegativeTypes;

        if (null === $knownNegativeTypes) {
            $knownNegativeTypes = [
                [CT::T_CLASS_CONSTANT],
                [CT::T_DYNAMIC_VAR_BRACE_CLOSE],
                [CT::T_RETURN_REF],
                [CT::T_USE_LAMBDA],
                [T_ARRAY],
                [T_CATCH],
                [T_CLASS],
                [T_DECLARE],
                [T_ELSEIF],
                [T_EMPTY],
                [T_EXIT],
                [T_EVAL],
                [T_FN],
                [T_FOREACH],
                [T_FOR],
                [T_FUNCTION],
                [T_HALT_COMPILER],
                [T_IF],
                [T_ISSET],
                [T_LIST],
                [T_STRING],
                [T_SWITCH],
                [T_STATIC],
                [T_UNSET],
                [T_VARIABLE],
                [T_WHILE],
                // handled by the `include` rule
                [T_REQUIRE],
                [T_REQUIRE_ONCE],
                [T_INCLUDE],
                [T_INCLUDE_ONCE],
            ];

            if (\defined('T_MATCH')) { // @TODO: drop condition and add directly in `$knownNegativeTypes` above when PHP 8.0+ is required
                $knownNegativeTypes[] = T_MATCH;
            }
        }

        return $token->equalsAny($knownNegativeTypes);
    }

    private function containsOperation(Tokens $tokens, int $startIndex, int $endIndex): bool
    {
        while (true) {
            $startIndex = $tokens->getNextMeaningfulToken($startIndex);

            if ($startIndex === $endIndex) {
                break;
            }

            $block = Tokens::detectBlockType($tokens[$startIndex]);

            if (null !== $block && $block['isStart']) {
                $startIndex = $tokens->findBlockEnd($block['type'], $startIndex);

                continue;
            }

            if (!$tokens[$startIndex]->equalsAny(self::NOOP_TYPES)) {
                return true;
            }
        }

        return false;
    }

    private function getConfigType(Tokens $tokens, int $beforeOpenIndex): ?string
    {
        if ($tokens[$beforeOpenIndex]->isGivenKind(self::TOKEN_TYPE_NO_CONFIG)) {
            return null;
        }

        foreach (self::TOKEN_TYPE_CONFIG_MAP as $type => $configItem) {
            if ($tokens[$beforeOpenIndex]->isGivenKind($type)) {
                return $configItem;
            }
        }

        return 'others';
    }

    private function removeUselessParenthesisPair(
        Tokens $tokens,
        int $beforeOpenIndex,
        int $afterCloseIndex,
        int $openIndex,
        int $closeIndex,
        ?string $configType
    ): void {
        $statements = $this->configuration['statements'];

        if (null === $configType || !\in_array($configType, $statements, true)) {
            return;
        }

        $needsSpaceAfter =
            !$this->isAccess($tokens, $afterCloseIndex)
            && !$tokens[$afterCloseIndex]->equalsAny([';', ',', [T_CLOSE_TAG]])
            && null === $this->getBlock($tokens, $afterCloseIndex, false)
            && !($tokens[$afterCloseIndex]->equalsAny([':', ';']) && $tokens[$beforeOpenIndex]->isGivenKind(T_CASE))
        ;

        $needsSpaceBefore =
            !$this->isPreUnaryOperation($tokens, $beforeOpenIndex)
            && !$tokens[$beforeOpenIndex]->equalsAny(['}', [T_EXIT], [T_OPEN_TAG]])
            && null === $this->getBlock($tokens, $beforeOpenIndex, true)
        ;

        $this->removeBrace($tokens, $closeIndex, $needsSpaceAfter);
        $this->removeBrace($tokens, $openIndex, $needsSpaceBefore);
    }

    private function removeBrace(Tokens $tokens, int $index, bool $needsSpace): void
    {
        if ($needsSpace) {
            foreach ([-1, 1] as $direction) {
                $siblingIndex = $tokens->getNonEmptySibling($index, $direction);

                if ($tokens[$siblingIndex]->isWhitespace() || $tokens[$siblingIndex]->isComment()) {
                    $needsSpace = false;

                    break;
                }
            }
        }

        if ($needsSpace) {
            $tokens[$index] = new Token([T_WHITESPACE, ' ']);
        } else {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }
    }
}
