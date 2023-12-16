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
use PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class TrailingCommaInMultilineFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    public const ELEMENTS_ARRAYS = 'arrays';

    /**
     * @internal
     */
    public const ELEMENTS_ARGUMENTS = 'arguments';

    /**
     * @internal
     */
    public const ELEMENTS_PARAMETERS = 'parameters';

    private const MATCH_EXPRESSIONS = 'match';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Multi-line arrays, arguments list, parameters list and `match` expressions must have a trailing comma.',
            [
                new CodeSample("<?php\narray(\n    1,\n    2\n);\n"),
                new CodeSample(
                    <<<'SAMPLE'
                        <?php
                            $x = [
                                'foo',
                                <<<EOD
                                    bar
                                    EOD
                            ];

                        SAMPLE
                    ,
                    ['after_heredoc' => true]
                ),
                new CodeSample("<?php\nfoo(\n    1,\n    2\n);\n", ['elements' => [self::ELEMENTS_ARGUMENTS]]),
                new VersionSpecificCodeSample("<?php\nfunction foo(\n    \$x,\n    \$y\n)\n{\n}\n", new VersionSpecification(8_00_00), ['elements' => [self::ELEMENTS_PARAMETERS]]),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN, '(']);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('after_heredoc', 'Whether a trailing comma should also be placed after heredoc end.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('elements', sprintf('Where to fix multiline trailing comma (PHP >= 8.0 for `%s` and `%s`).', self::ELEMENTS_PARAMETERS, self::MATCH_EXPRESSIONS))) // @TODO: remove text when PHP 8.0+ is required
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset([self::ELEMENTS_ARRAYS, self::ELEMENTS_ARGUMENTS, self::ELEMENTS_PARAMETERS, self::MATCH_EXPRESSIONS])])
                ->setDefault([self::ELEMENTS_ARRAYS])
                ->setNormalizer(static function (Options $options, array $value) {
                    if (\PHP_VERSION_ID < 8_00_00) { // @TODO: drop condition when PHP 8.0+ is required
                        foreach ([self::ELEMENTS_PARAMETERS, self::MATCH_EXPRESSIONS] as $option) {
                            if (\in_array($option, $value, true)) {
                                throw new InvalidOptionsForEnvException(sprintf('"%s" option can only be enabled with PHP 8.0+.', $option));
                            }
                        }
                    }

                    return $value;
                })
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $fixArrays = \in_array(self::ELEMENTS_ARRAYS, $this->configuration['elements'], true);
        $fixArguments = \in_array(self::ELEMENTS_ARGUMENTS, $this->configuration['elements'], true);
        $fixParameters = \PHP_VERSION_ID >= 8_00_00 && \in_array(self::ELEMENTS_PARAMETERS, $this->configuration['elements'], true); // @TODO: drop condition when PHP 8.0+ is required
        $fixMatch = \PHP_VERSION_ID >= 8_00_00 && \in_array(self::MATCH_EXPRESSIONS, $this->configuration['elements'], true); // @TODO: drop condition when PHP 8.0+ is required

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $prevIndex = $tokens->getPrevMeaningfulToken($index);

            if (
                $fixArrays
                && (
                    $tokens[$index]->equals('(') && $tokens[$prevIndex]->isGivenKind(T_ARRAY) // long syntax
                    || $tokens[$index]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN) // short syntax
                )
            ) {
                $this->fixBlock($tokens, $index);

                continue;
            }

            if (!$tokens[$index]->equals('(')) {
                continue;
            }

            $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);

            if ($fixArguments
                && $tokens[$prevIndex]->equalsAny([']', [T_CLASS], [T_STRING], [T_VARIABLE], [T_STATIC]])
                && !$tokens[$prevPrevIndex]->isGivenKind(T_FUNCTION)
            ) {
                $this->fixBlock($tokens, $index);

                continue;
            }

            if (
                $fixParameters
                && (
                    $tokens[$prevIndex]->isGivenKind(T_STRING) && $tokens[$prevPrevIndex]->isGivenKind(T_FUNCTION)
                    || $tokens[$prevIndex]->isGivenKind([T_FN, T_FUNCTION])
                )
            ) {
                $this->fixBlock($tokens, $index);
            }

            if ($fixMatch && $tokens[$prevIndex]->isGivenKind(T_MATCH)) {
                $this->fixMatch($tokens, $index);
            }
        }
    }

    private function fixBlock(Tokens $tokens, int $startIndex): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        if (!$tokensAnalyzer->isBlockMultiline($tokens, $startIndex)) {
            return;
        }

        $blockType = Tokens::detectBlockType($tokens[$startIndex]);
        $endIndex = $tokens->findBlockEnd($blockType['type'], $startIndex);

        $beforeEndIndex = $tokens->getPrevMeaningfulToken($endIndex);
        if (!$tokens->isPartialCodeMultiline($beforeEndIndex, $endIndex)) {
            return;
        }
        $beforeEndToken = $tokens[$beforeEndIndex];

        // if there is some item between braces then add `,` after it
        if (
            $startIndex !== $beforeEndIndex && !$beforeEndToken->equals(',')
            && (true === $this->configuration['after_heredoc'] || !$beforeEndToken->isGivenKind(T_END_HEREDOC))
        ) {
            $tokens->insertAt($beforeEndIndex + 1, new Token(','));

            $endToken = $tokens[$endIndex];

            if (!$endToken->isComment() && !$endToken->isWhitespace()) {
                $tokens->ensureWhitespaceAtIndex($endIndex, 1, ' ');
            }
        }
    }

    private function fixMatch(Tokens $tokens, int $index): void
    {
        $index = $tokens->getNextTokenOfKind($index, ['{']);
        $closeIndex = $index;
        $isMultiline = false;
        $depth = 1;

        do {
            ++$closeIndex;

            if ($tokens[$closeIndex]->equals('{')) {
                ++$depth;
            } elseif ($tokens[$closeIndex]->equals('}')) {
                --$depth;
            } elseif (!$isMultiline && str_contains($tokens[$closeIndex]->getContent(), "\n")) {
                $isMultiline = true;
            }
        } while ($depth > 0);

        if (!$isMultiline) {
            return;
        }

        $previousIndex = $tokens->getPrevMeaningfulToken($closeIndex);
        if (!$tokens->isPartialCodeMultiline($previousIndex, $closeIndex)) {
            return;
        }

        if (!$tokens[$previousIndex]->equals(',')) {
            $tokens->insertAt($previousIndex + 1, new Token(','));
        }
    }
}
