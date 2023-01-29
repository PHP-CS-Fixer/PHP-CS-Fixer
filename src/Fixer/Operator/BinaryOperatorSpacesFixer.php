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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BinaryOperatorSpacesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    public const SINGLE_SPACE = 'single_space';

    /**
     * @internal
     */
    public const NO_SPACE = 'no_space';

    /**
     * @internal
     */
    public const ALIGN = 'align';

    /**
     * @internal
     */
    public const ALIGN_BY_SCOPE = 'align_by_scope';

    /**
     * @internal
     */
    public const ALIGN_SINGLE_SPACE = 'align_single_space';

    /**
     * @internal
     */
    public const ALIGN_SINGLE_SPACE_BY_SCOPE = 'align_single_space_by_scope';

    /**
     * @internal
     */
    public const ALIGN_SINGLE_SPACE_MINIMAL = 'align_single_space_minimal';

    /**
     * @internal
     */
    public const ALIGN_SINGLE_SPACE_MINIMAL_BY_SCOPE = 'align_single_space_minimal_by_scope';

    /**
     * @internal
     *
     * @const Placeholder used as anchor for right alignment.
     */
    public const ALIGN_PLACEHOLDER = "\x2 ALIGNABLE%d \x3";

    /**
     * @var string[]
     */
    private const SUPPORTED_OPERATORS = [
        '=',
        '*',
        '/',
        '%',
        '<',
        '>',
        '|',
        '^',
        '+',
        '-',
        '&',
        '&=',
        '&&',
        '||',
        '.=',
        '/=',
        '=>',
        '==',
        '>=',
        '===',
        '!=',
        '<>',
        '!==',
        '<=',
        'and',
        'or',
        'xor',
        '-=',
        '%=',
        '*=',
        '|=',
        '+=',
        '<<',
        '<<=',
        '>>',
        '>>=',
        '^=',
        '**',
        '**=',
        '<=>',
        '??',
        '??=',
    ];

    /**
     * Keep track of the deepest level ever achieved while
     * parsing the code. Used later to replace alignment
     * placeholders with spaces.
     */
    private int $deepestLevel;

    /**
     * Level counter of the current nest level.
     * So one level alignments are not mixed with
     * other level ones.
     */
    private int $currentLevel;

    /**
     * @var array<null|string>
     */
    private static array $allowedValues = [
        self::ALIGN,
        self::ALIGN_BY_SCOPE,
        self::ALIGN_SINGLE_SPACE,
        self::ALIGN_SINGLE_SPACE_MINIMAL,
        self::ALIGN_SINGLE_SPACE_BY_SCOPE,
        self::ALIGN_SINGLE_SPACE_MINIMAL_BY_SCOPE,
        self::SINGLE_SPACE,
        self::NO_SPACE,
        null,
    ];

    private TokensAnalyzer $tokensAnalyzer;

    /**
     * @var array<string, string>
     */
    private array $alignOperatorTokens = [];

    /**
     * @var array<string, string>
     */
    private array $operators = [];

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->operators = $this->resolveOperatorsFromConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Binary operators should be surrounded by space as configured.',
            [
                new CodeSample(
                    "<?php\n\$a= 1  + \$b^ \$d !==  \$e or   \$f;\n"
                ),
                new CodeSample(
                    '<?php
$aa=  1;
$b=2;

$c = $d    xor    $e;
$f    -=  1;
',
                    ['operators' => ['=' => 'align', 'xor' => null]]
                ),
                new CodeSample(
                    '<?php
$a = $b +=$c;
$d = $ee+=$f;

$g = $b     +=$c;
$h = $ee+=$f;
',
                    ['operators' => ['+=' => 'align_single_space']]
                ),
                new CodeSample(
                    '<?php
$a = $b===$c;
$d = $f   ===  $g;
$h = $i===  $j;
',
                    ['operators' => ['===' => 'align_single_space_minimal']]
                ),
                new CodeSample(
                    '<?php
$foo = \json_encode($bar, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
',
                    ['operators' => ['|' => 'no_space']]
                ),
                new CodeSample(
                    '<?php
$array = [
    "foo"            =>   1,
    "baaaaaaaaaaar"  =>  11,
];
',
                    ['operators' => ['=>' => 'single_space']]
                ),
                new CodeSample(
                    '<?php
$array = [
    "foo" => 12,
    "baaaaaaaaaaar"  => 13,

    "baz" => 1,
];
',
                    ['operators' => ['=>' => 'align']]
                ),
                new CodeSample(
                    '<?php
$array = [
    "foo" => 12,
    "baaaaaaaaaaar"  => 13,

    "baz" => 1,
];
',
                    ['operators' => ['=>' => 'align_by_scope']]
                ),
                new CodeSample(
                    '<?php
$array = [
    "foo" => 12,
    "baaaaaaaaaaar"  => 13,

    "baz" => 1,
];
',
                    ['operators' => ['=>' => 'align_single_space']]
                ),
                new CodeSample(
                    '<?php
$array = [
    "foo" => 12,
    "baaaaaaaaaaar"  => 13,

    "baz" => 1,
];
',
                    ['operators' => ['=>' => 'align_single_space_by_scope']]
                ),
                new CodeSample(
                    '<?php
$array = [
    "foo" => 12,
    "baaaaaaaaaaar"  => 13,

    "baz" => 1,
];
',
                    ['operators' => ['=>' => 'align_single_space_minimal']]
                ),
                new CodeSample(
                    '<?php
$array = [
    "foo" => 12,
    "baaaaaaaaaaar"  => 13,

    "baz" => 1,
];
',
                    ['operators' => ['=>' => 'align_single_space_minimal_by_scope']]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after ArrayIndentationFixer, ArraySyntaxFixer, AssignNullCoalescingToCoalesceEqualFixer, ListSyntaxFixer, ModernizeStrposFixer, NoMultilineWhitespaceAroundDoubleArrowFixer, NoUnsetCastFixer, PowToExponentiationFixer, StandardizeNotEqualsFixer, StrictComparisonFixer.
     */
    public function getPriority(): int
    {
        return -32;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->tokensAnalyzer = new TokensAnalyzer($tokens);

        // last and first tokens cannot be an operator
        for ($index = $tokens->count() - 2; $index > 0; --$index) {
            if (!$this->tokensAnalyzer->isBinaryOperator($index)) {
                continue;
            }

            if ('=' === $tokens[$index]->getContent()) {
                $isDeclare = $this->isEqualPartOfDeclareStatement($tokens, $index);
                if (false === $isDeclare) {
                    $this->fixWhiteSpaceAroundOperator($tokens, $index);
                } else {
                    $index = $isDeclare; // skip `declare(foo ==bar)`, see `declare_equal_normalize`
                }
            } else {
                $this->fixWhiteSpaceAroundOperator($tokens, $index);
            }

            // previous of binary operator is now never an operator / previous of declare statement cannot be an operator
            --$index;
        }

        if (\count($this->alignOperatorTokens) > 0) {
            $this->fixAlignment($tokens, $this->alignOperatorTokens);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('default', 'Default fix strategy.'))
                ->setDefault(self::SINGLE_SPACE)
                ->setAllowedValues(self::$allowedValues)
                ->getOption(),
            (new FixerOptionBuilder('operators', 'Dictionary of `binary operator` => `fix strategy` values that differ from the default strategy. Supported are: `'.implode('`, `', self::SUPPORTED_OPERATORS).'`'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([static function (array $option): bool {
                    foreach ($option as $operator => $value) {
                        if (!\in_array($operator, self::SUPPORTED_OPERATORS, true)) {
                            throw new InvalidOptionsException(
                                sprintf(
                                    'Unexpected "operators" key, expected any of "%s", got "%s".',
                                    implode('", "', self::SUPPORTED_OPERATORS),
                                    \gettype($operator).'#'.$operator
                                )
                            );
                        }

                        if (!\in_array($value, self::$allowedValues, true)) {
                            throw new InvalidOptionsException(
                                sprintf(
                                    'Unexpected value for operator "%s", expected any of "%s", got "%s".',
                                    $operator,
                                    implode('", "', self::$allowedValues),
                                    \is_object($value) ? \get_class($value) : (null === $value ? 'null' : \gettype($value).'#'.$value)
                                )
                            );
                        }
                    }

                    return true;
                }])
                ->setDefault([])
                ->getOption(),
        ]);
    }

    private function fixWhiteSpaceAroundOperator(Tokens $tokens, int $index): void
    {
        $tokenContent = strtolower($tokens[$index]->getContent());

        if (!\array_key_exists($tokenContent, $this->operators)) {
            return; // not configured to be changed
        }

        if (self::SINGLE_SPACE === $this->operators[$tokenContent]) {
            $this->fixWhiteSpaceAroundOperatorToSingleSpace($tokens, $index);

            return;
        }

        if (self::NO_SPACE === $this->operators[$tokenContent]) {
            $this->fixWhiteSpaceAroundOperatorToNoSpace($tokens, $index);

            return;
        }

        // schedule for alignment
        $this->alignOperatorTokens[$tokenContent] = $this->operators[$tokenContent];

        if (
            self::ALIGN === $this->operators[$tokenContent]
            || self::ALIGN_BY_SCOPE === $this->operators[$tokenContent]
        ) {
            return;
        }

        // fix white space after operator
        if ($tokens[$index + 1]->isWhitespace()) {
            if (
                self::ALIGN_SINGLE_SPACE_MINIMAL === $this->operators[$tokenContent]
                || self::ALIGN_SINGLE_SPACE_MINIMAL_BY_SCOPE === $this->operators[$tokenContent]
            ) {
                $tokens[$index + 1] = new Token([T_WHITESPACE, ' ']);
            }

            return;
        }

        $tokens->insertAt($index + 1, new Token([T_WHITESPACE, ' ']));
    }

    private function fixWhiteSpaceAroundOperatorToSingleSpace(Tokens $tokens, int $index): void
    {
        // fix white space after operator
        if ($tokens[$index + 1]->isWhitespace()) {
            $content = $tokens[$index + 1]->getContent();
            if (' ' !== $content && !str_contains($content, "\n") && !$tokens[$tokens->getNextNonWhitespace($index + 1)]->isComment()) {
                $tokens[$index + 1] = new Token([T_WHITESPACE, ' ']);
            }
        } else {
            $tokens->insertAt($index + 1, new Token([T_WHITESPACE, ' ']));
        }

        // fix white space before operator
        if ($tokens[$index - 1]->isWhitespace()) {
            $content = $tokens[$index - 1]->getContent();
            if (' ' !== $content && !str_contains($content, "\n") && !$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
                $tokens[$index - 1] = new Token([T_WHITESPACE, ' ']);
            }
        } else {
            $tokens->insertAt($index, new Token([T_WHITESPACE, ' ']));
        }
    }

    private function fixWhiteSpaceAroundOperatorToNoSpace(Tokens $tokens, int $index): void
    {
        // fix white space after operator
        if ($tokens[$index + 1]->isWhitespace()) {
            $content = $tokens[$index + 1]->getContent();
            if (!str_contains($content, "\n") && !$tokens[$tokens->getNextNonWhitespace($index + 1)]->isComment()) {
                $tokens->clearAt($index + 1);
            }
        }

        // fix white space before operator
        if ($tokens[$index - 1]->isWhitespace()) {
            $content = $tokens[$index - 1]->getContent();
            if (!str_contains($content, "\n") && !$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
                $tokens->clearAt($index - 1);
            }
        }
    }

    /**
     * @return false|int index of T_DECLARE where the `=` belongs to or `false`
     */
    private function isEqualPartOfDeclareStatement(Tokens $tokens, int $index)
    {
        $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_STRING)) {
            $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
            if ($tokens[$prevMeaningfulIndex]->equals('(')) {
                $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
                if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_DECLARE)) {
                    return $prevMeaningfulIndex;
                }
            }
        }

        return false;
    }

    /**
     * @return array<string, string>
     */
    private function resolveOperatorsFromConfig(): array
    {
        $operators = [];

        if (null !== $this->configuration['default']) {
            foreach (self::SUPPORTED_OPERATORS as $operator) {
                $operators[$operator] = $this->configuration['default'];
            }
        }

        foreach ($this->configuration['operators'] as $operator => $value) {
            if (null === $value) {
                unset($operators[$operator]);
            } else {
                $operators[$operator] = $value;
            }
        }

        return $operators;
    }

    // Alignment logic related methods

    /**
     * @param array<string, string> $toAlign
     */
    private function fixAlignment(Tokens $tokens, array $toAlign): void
    {
        $this->deepestLevel = 0;
        $this->currentLevel = 0;

        foreach ($toAlign as $tokenContent => $alignStrategy) {
            // This fixer works partially on Tokens and partially on string representation of code.
            // During the process of fixing internal state of single Token may be affected by injecting ALIGN_PLACEHOLDER to its content.
            // The placeholder will be resolved by `replacePlaceholders` method by removing placeholder or changing it into spaces.
            // That way of fixing the code causes disturbances in marking Token as changed - if code is perfectly valid then placeholder
            // still be injected and removed, which will cause the `changed` flag to be set.
            // To handle that unwanted behavior we work on clone of Tokens collection and then override original collection with fixed collection.
            $tokensClone = clone $tokens;

            if ('=>' === $tokenContent) {
                $this->injectAlignmentPlaceholdersForArrow($tokensClone, 0, \count($tokens));
            } else {
                $this->injectAlignmentPlaceholdersDefault($tokensClone, 0, \count($tokens), $tokenContent);
            }

            // for all tokens that should be aligned but do not have anything to align with, fix spacing if needed
            if (
                self::ALIGN_SINGLE_SPACE === $alignStrategy
                || self::ALIGN_SINGLE_SPACE_MINIMAL === $alignStrategy
                || self::ALIGN_SINGLE_SPACE_BY_SCOPE === $alignStrategy
                || self::ALIGN_SINGLE_SPACE_MINIMAL_BY_SCOPE === $alignStrategy
            ) {
                if ('=>' === $tokenContent) {
                    for ($index = $tokens->count() - 2; $index > 0; --$index) {
                        if ($tokens[$index]->isGivenKind(T_DOUBLE_ARROW)) { // always binary operator, never part of declare statement
                            $this->fixWhiteSpaceBeforeOperator($tokensClone, $index, $alignStrategy);
                        }
                    }
                } elseif ('=' === $tokenContent) {
                    for ($index = $tokens->count() - 2; $index > 0; --$index) {
                        if ('=' === $tokens[$index]->getContent() && !$this->isEqualPartOfDeclareStatement($tokens, $index) && $this->tokensAnalyzer->isBinaryOperator($index)) {
                            $this->fixWhiteSpaceBeforeOperator($tokensClone, $index, $alignStrategy);
                        }
                    }
                } else {
                    for ($index = $tokens->count() - 2; $index > 0; --$index) {
                        $content = $tokens[$index]->getContent();
                        if (strtolower($content) === $tokenContent && $this->tokensAnalyzer->isBinaryOperator($index)) { // never part of declare statement
                            $this->fixWhiteSpaceBeforeOperator($tokensClone, $index, $alignStrategy);
                        }
                    }
                }
            }

            $tokens->setCode($this->replacePlaceholders($tokensClone, $alignStrategy, $tokenContent));
        }
    }

    private function injectAlignmentPlaceholdersDefault(Tokens $tokens, int $startAt, int $endAt, string $tokenContent): void
    {
        $newLineFoundSinceLastPlaceholder = true;

        for ($index = $startAt; $index < $endAt; ++$index) {
            $token = $tokens[$index];
            $content = $token->getContent();

            if (str_contains($content, "\n")) {
                $newLineFoundSinceLastPlaceholder = true;
            }

            if (
                strtolower($content) === $tokenContent
                && $this->tokensAnalyzer->isBinaryOperator($index)
                && ('=' !== $content || !$this->isEqualPartOfDeclareStatement($tokens, $index))
                && $newLineFoundSinceLastPlaceholder
            ) {
                $tokens[$index] = new Token(sprintf(self::ALIGN_PLACEHOLDER, $this->currentLevel).$content);
                $newLineFoundSinceLastPlaceholder = false;

                continue;
            }

            if ($token->isGivenKind(T_FN)) {
                $from = $tokens->getNextMeaningfulToken($index);
                $until = $this->getLastTokenIndexOfFn($tokens, $index);
                $this->injectAlignmentPlaceholders($tokens, $from + 1, $until - 1, $tokenContent);
                $index = $until;

                continue;
            }

            if ($token->isGivenKind([T_FUNCTION, T_CLASS])) {
                $index = $tokens->getNextTokenOfKind($index, ['{', ';', '(']);
                // We don't align `=` on multi-line definition of function parameters with default values
                if ($tokens[$index]->equals('(')) {
                    $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                    continue;
                }

                if ($tokens[$index]->equals(';')) {
                    continue;
                }

                // Update the token to the `{` one in order to apply the following logic
                $token = $tokens[$index];
            }

            if ($token->equals('{')) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                $this->injectAlignmentPlaceholders($tokens, $index + 1, $until - 1, $tokenContent);
                $index = $until;

                continue;
            }

            if ($token->equals('(')) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $this->injectAlignmentPlaceholders($tokens, $index + 1, $until - 1, $tokenContent);
                $index = $until;

                continue;
            }

            if ($token->equals('[')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);

                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                $this->injectAlignmentPlaceholders($tokens, $index + 1, $until - 1, $tokenContent);
                $index = $until;

                continue;
            }
        }
    }

    private function injectAlignmentPlaceholders(Tokens $tokens, int $from, int $until, string $tokenContent): void
    {
        // Only inject placeholders for multi-line code
        if ($tokens->isPartialCodeMultiline($from, $until)) {
            ++$this->deepestLevel;
            $currentLevel = $this->currentLevel;
            $this->currentLevel = $this->deepestLevel;
            $this->injectAlignmentPlaceholdersDefault($tokens, $from, $until, $tokenContent);
            $this->currentLevel = $currentLevel;
        }
    }

    private function injectAlignmentPlaceholdersForArrow(Tokens $tokens, int $startAt, int $endAt): void
    {
        $newLineFoundSinceLastPlaceholder = true;
        $yieldFoundSinceLastPlaceholder = false;

        for ($index = $startAt; $index < $endAt; ++$index) {
            /** @var Token $token */
            $token = $tokens[$index];
            $content = $token->getContent();

            if (str_contains($content, "\n")) {
                $newLineFoundSinceLastPlaceholder = true;
            }

            if ($token->isGivenKind(T_YIELD)) {
                $yieldFoundSinceLastPlaceholder = true;
            }

            if ($token->isGivenKind(T_FN)) {
                $yieldFoundSinceLastPlaceholder = false;
                $from = $tokens->getNextMeaningfulToken($index);
                $until = $this->getLastTokenIndexOfFn($tokens, $index);
                $this->injectArrayAlignmentPlaceholders($tokens, $from + 1, $until - 1);
                $index = $until;

                continue;
            }

            if ($token->isGivenKind(T_ARRAY)) { // don't use "$tokens->isArray()" here, short arrays are handled in the next case
                $yieldFoundSinceLastPlaceholder = false;
                $from = $tokens->getNextMeaningfulToken($index);
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $from);
                $index = $until;

                $this->injectArrayAlignmentPlaceholders($tokens, $from + 1, $until - 1);

                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $yieldFoundSinceLastPlaceholder = false;
                $from = $index;
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $from);
                $index = $until;

                $this->injectArrayAlignmentPlaceholders($tokens, $from + 1, $until - 1);

                continue;
            }

            // no need to analyze for `isBinaryOperator` (always true), nor if part of declare statement (not valid PHP)
            // there is also no need to analyse the second arrow of a line
            if ($token->isGivenKind(T_DOUBLE_ARROW) && $newLineFoundSinceLastPlaceholder) {
                if ($yieldFoundSinceLastPlaceholder) {
                    ++$this->deepestLevel;
                    ++$this->currentLevel;
                }
                $tokenContent = sprintf(self::ALIGN_PLACEHOLDER, $this->currentLevel).$token->getContent();

                $nextToken = $tokens[$index + 1];
                if (!$nextToken->isWhitespace()) {
                    $tokenContent .= ' ';
                } elseif ($nextToken->isWhitespace(" \t")) {
                    $tokens[$index + 1] = new Token([T_WHITESPACE, ' ']);
                }

                $tokens[$index] = new Token([T_DOUBLE_ARROW, $tokenContent]);
                $newLineFoundSinceLastPlaceholder = false;
                $yieldFoundSinceLastPlaceholder = false;

                continue;
            }

            if ($token->equals(';')) {
                ++$this->deepestLevel;
                ++$this->currentLevel;

                continue;
            }

            if ($token->equals(',')) {
                for ($i = $index; $i < $endAt - 1; ++$i) {
                    if (str_contains($tokens[$i - 1]->getContent(), "\n")) {
                        $newLineFoundSinceLastPlaceholder = true;

                        break;
                    }

                    if ($tokens[$i + 1]->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
                        $arrayStartIndex = $tokens[$i + 1]->isGivenKind(T_ARRAY)
                            ? $tokens->getNextMeaningfulToken($i + 1)
                            : $i + 1
                        ;
                        $blockType = Tokens::detectBlockType($tokens[$arrayStartIndex]);
                        $arrayEndIndex = $tokens->findBlockEnd($blockType['type'], $arrayStartIndex);

                        if ($tokens->isPartialCodeMultiline($arrayStartIndex, $arrayEndIndex)) {
                            break;
                        }
                    }

                    ++$index;
                }
            }

            if ($token->equals('{')) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                $this->injectArrayAlignmentPlaceholders($tokens, $index + 1, $until - 1);
                $index = $until;

                continue;
            }

            if ($token->equals('(')) {
                $until = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                $this->injectArrayAlignmentPlaceholders($tokens, $index + 1, $until - 1);
                $index = $until;

                continue;
            }
        }
    }

    private function injectArrayAlignmentPlaceholders(Tokens $tokens, int $from, int $until): void
    {
        // Only inject placeholders for multi-line arrays
        if ($tokens->isPartialCodeMultiline($from, $until)) {
            ++$this->deepestLevel;
            $currentLevel = $this->currentLevel;
            $this->currentLevel = $this->deepestLevel;
            $this->injectAlignmentPlaceholdersForArrow($tokens, $from, $until);
            $this->currentLevel = $currentLevel;
        }
    }

    private function fixWhiteSpaceBeforeOperator(Tokens $tokens, int $index, string $alignStrategy): void
    {
        // fix white space after operator is not needed as BinaryOperatorSpacesFixer took care of this (if strategy is _not_ ALIGN)
        if (!$tokens[$index - 1]->isWhitespace()) {
            $tokens->insertAt($index, new Token([T_WHITESPACE, ' ']));

            return;
        }

        if (
            self::ALIGN_SINGLE_SPACE_MINIMAL !== $alignStrategy && self::ALIGN_SINGLE_SPACE_MINIMAL_BY_SCOPE !== $alignStrategy
            || $tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()
        ) {
            return;
        }

        $content = $tokens[$index - 1]->getContent();
        if (' ' !== $content && !str_contains($content, "\n")) {
            $tokens[$index - 1] = new Token([T_WHITESPACE, ' ']);
        }
    }

    /**
     * Look for group of placeholders and provide vertical alignment.
     */
    private function replacePlaceholders(Tokens $tokens, string $alignStrategy, string $tokenContent): string
    {
        $tmpCode = $tokens->generateCode();

        for ($j = 0; $j <= $this->deepestLevel; ++$j) {
            $placeholder = sprintf(self::ALIGN_PLACEHOLDER, $j);

            if (!str_contains($tmpCode, $placeholder)) {
                continue;
            }

            $lines = explode("\n", $tmpCode);
            $groups = [];
            $groupIndex = 0;
            $groups[$groupIndex] = [];

            foreach ($lines as $index => $line) {
                if (substr_count($line, $placeholder) > 0) {
                    $groups[$groupIndex][] = $index;
                } elseif (
                    self::ALIGN_BY_SCOPE !== $alignStrategy
                    && self::ALIGN_SINGLE_SPACE_BY_SCOPE !== $alignStrategy
                    && self::ALIGN_SINGLE_SPACE_MINIMAL_BY_SCOPE !== $alignStrategy
                ) {
                    ++$groupIndex;
                    $groups[$groupIndex] = [];
                }
            }

            foreach ($groups as $group) {
                if (\count($group) < 1) {
                    continue;
                }

                if (self::ALIGN !== $alignStrategy) {
                    // move placeholders to match strategy
                    foreach ($group as $index) {
                        $currentPosition = strpos($lines[$index], $placeholder);
                        $before = substr($lines[$index], 0, $currentPosition);

                        if (
                            self::ALIGN_SINGLE_SPACE === $alignStrategy
                            || self::ALIGN_SINGLE_SPACE_BY_SCOPE === $alignStrategy
                        ) {
                            if (!str_ends_with($before, ' ')) { // if last char of before-content is not ' '; add it
                                $before .= ' ';
                            }
                        } elseif (
                            self::ALIGN_SINGLE_SPACE_MINIMAL === $alignStrategy
                            || self::ALIGN_SINGLE_SPACE_MINIMAL_BY_SCOPE === $alignStrategy
                        ) {
                            if (1 !== Preg::match('/^\h+$/', $before)) { // if indent; do not move, leave to other fixer
                                $before = rtrim($before).' ';
                            }
                        }

                        $lines[$index] = $before.substr($lines[$index], $currentPosition);
                    }
                }

                $rightmostSymbol = 0;
                foreach ($group as $index) {
                    $rightmostSymbol = max($rightmostSymbol, mb_strpos($lines[$index], $placeholder));
                }

                foreach ($group as $index) {
                    $line = $lines[$index];
                    $currentSymbol = mb_strpos($line, $placeholder);
                    $delta = abs($rightmostSymbol - $currentSymbol);

                    if ($delta > 0) {
                        $line = str_replace($placeholder, str_repeat(' ', $delta).$placeholder, $line);
                        $lines[$index] = $line;
                    }
                }
            }

            $tmpCode = str_replace($placeholder, '', implode("\n", $lines));
        }

        return $tmpCode;
    }

    private function getLastTokenIndexOfFn(Tokens $tokens, int $index): int
    {
        $index = $tokens->getNextTokenOfKind($index, [[T_DOUBLE_ARROW]]);

        while (true) {
            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->equalsAny([';', ',', [T_CLOSE_TAG]])) {
                break;
            }

            $blockType = Tokens::detectBlockType($tokens[$index]);

            if (null === $blockType) {
                continue;
            }

            if ($blockType['isStart']) {
                $index = $tokens->findBlockEnd($blockType['type'], $index);

                continue;
            }

            break;
        }

        return $index;
    }
}
