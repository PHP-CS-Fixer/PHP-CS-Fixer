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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Utils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 */
final class UnaryOperatorSpacesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const OPTION_LEADING_AND_TRAILING_SPACES = 'leading_and_trailing_spaces';
    private const OPTION_LEADING_SPACE = 'leading_space';
    private const OPTION_TRAILING_SPACE = 'trailing_space';
    private const OPTION_NO_SPACES = 'no_spaces';

    private const ALLOWED_SPACE_OPTIONS = [
        self::OPTION_LEADING_AND_TRAILING_SPACES,
        self::OPTION_LEADING_SPACE,
        self::OPTION_TRAILING_SPACE,
        self::OPTION_NO_SPACES,
        null,
    ];

    private const SUPPORTED_OPERATORS = [
        '++',
        '--',
        '+',
        '-',
        '&',
        '!',
        '~',
        '@',
        '...',
    ];

    /**
     * @var array<string, string>
     *
     * @phpstan-var array<value-of<self::SUPPORTED_OPERATORS>, self::OPTION_*>
     */
    private array $operators = [];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Unary operators should be surrounded by space as configured.',
            [
                new CodeSample(
                    "<?php\n\$sample ++;\n-- \$sample;\n\$sample = ! ! \$a;\n\$sample = ~  \$c;\nfunction & foo() {}\n"
                ),
                new CodeSample(
                    '<?php
if (!$isBar()) {
    $sample++;

    for (; $sample <= 0;--$sample) {
        $a =& foo();
        $b =~$c;
    }
}
',
                    ['default' => self::OPTION_LEADING_SPACE, 'operators' => ['&' => self::OPTION_NO_SPACES]]
                ),
                new CodeSample(
                    "<?php\n++\$sample;\n--\$sample;\n\$sample = !foo();\n\$sample = ~\$b;\nfunction &foo() {}\n",
                    ['default' => self::OPTION_TRAILING_SPACE, 'operators' => ['!' => self::OPTION_LEADING_SPACE]]
                ),
                new CodeSample(
                    "<?php\nif (!\$bar) {\n    echo \"Help!\";\n\n    return !\$a;\n}\n",
                    ['operators' => ['!' => self::OPTION_LEADING_AND_TRAILING_SPACES]]
                ),
            ],
            'When using the `no_spaces` option, the leading whitespace in unary successor operators will only be removed. Likewise, the trailing whitespace in unary predecessor operators will only be removed. '
            .'The `leading_space` option will force a leading whitespace but the opposite side of the operator will be unchanged. Conversely, the `trailing_space` option will force a trailing whitespace but '
            .'the opposite side of the operator will be unchanged. Use the `leading_and_trailing_spaces` option to force whitespaces on both sides of the unary operator.'
        );
    }

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->operators = $this->resolveOperators();
    }

    /**
     * {@inheritdoc}
     *
     * Must run after ModernizeStrposFixer.
     */
    public function getPriority(): int
    {
        return -10;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        $unaryTokenKinds = ['+', '-', '!', '~', '@', '&', T_DEC, T_ELLIPSIS, T_INC, CT::T_RETURN_REF];

        if (\defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG')) {
            $unaryTokenKinds[] = T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG; // @TODO drop condition when PHP 8.0+ is required
        }

        return $tokens->isAnyTokenKindsFound($unaryTokenKinds);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('default', 'Default fix strategy.'))
                ->setAllowedValues(self::ALLOWED_SPACE_OPTIONS)
                ->setDefault(self::OPTION_NO_SPACES)
                ->getOption(),
            (new FixerOptionBuilder('operators', 'Dictionary of `unary operator` => `fix strategy` values that differ from the default strategy. Supported operators are '.Utils::naturalLanguageJoinWithBackticks(self::SUPPORTED_OPERATORS).'.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([static function (array $option): bool {
                    foreach ($option as $operator => $value) {
                        if (!\in_array($operator, self::SUPPORTED_OPERATORS, true)) {
                            throw new InvalidOptionsException(
                                sprintf(
                                    'Unexpected "operators" key, expected any of "%s", got "%s" of type "%s".',
                                    Utils::naturalLanguageJoin(self::SUPPORTED_OPERATORS),
                                    Utils::toString($operator),
                                    get_debug_type($operator)
                                )
                            );
                        }

                        if (!\in_array($value, self::ALLOWED_SPACE_OPTIONS, true)) {
                            throw new InvalidOptionsException(
                                sprintf(
                                    'Unexpected value for operator "%s", expected any of %s, got "%s" of type "%s".',
                                    $operator,
                                    Utils::naturalLanguageJoin(array_map(
                                        static fn ($option): string => Utils::toString($option),
                                        self::ALLOWED_SPACE_OPTIONS
                                    )),
                                    Utils::toString($value),
                                    get_debug_type($value)
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

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokensAnalyzer->isUnarySuccessorOperator($index)) {
                if (!$tokens[$tokens->getPrevNonWhitespace($index)]->isComment()) {
                    $this->fixWhitespaceAroundOperator($tokens, $index, -1);
                }

                continue;
            }

            if ($tokensAnalyzer->isUnaryPredecessorOperator($index)) {
                $this->fixWhitespaceAroundOperator($tokens, $index, 1);

                continue;
            }
        }
    }

    /**
     * @return array<string, string>
     *
     * @phpstan-return array<value-of<self::SUPPORTED_OPERATORS>, self::OPTION_*>
     */
    private function resolveOperators(): array
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

    /**
     * @phpstan-param -1|1 $direction
     */
    private function fixWhitespaceAroundOperator(Tokens $tokens, int $index, int $direction): void
    {
        $fixStrategy = $this->getFixStrategyForOperator($tokens, $index);

        if (null === $fixStrategy) {
            return; // not configured to be changed
        }

        $prevIndex = $index - 1;
        $nextIndex = $index + 1;

        if (self::OPTION_LEADING_AND_TRAILING_SPACES === $fixStrategy) {
            $tokens->ensureWhitespaceAtIndex($nextIndex, 0, ' ');
            $tokens->ensureWhitespaceAtIndex($prevIndex, 1, ' ');

            return;
        }

        if (self::OPTION_LEADING_SPACE === $fixStrategy) {
            $tokens->ensureWhitespaceAtIndex($prevIndex, 1, ' ');

            return;
        }

        if (self::OPTION_TRAILING_SPACE === $fixStrategy) {
            $tokens->ensureWhitespaceAtIndex($nextIndex, 0, ' ');

            return;
        }

        if (self::OPTION_NO_SPACES === $fixStrategy && -1 === $direction) {
            $tokens->removeLeadingWhitespace($index);

            return;
        }

        $tokens->removeTrailingWhitespace($index);
    }

    /**
     * @phpstan-return value-of<self::ALLOWED_SPACE_OPTIONS>
     */
    private function getFixStrategyForOperator(Tokens $tokens, int $operatorIndex): ?string
    {
        $tokenContent = $tokens[$operatorIndex]->getContent();

        return $this->operators[$tokenContent] ?? null;
    }
}
