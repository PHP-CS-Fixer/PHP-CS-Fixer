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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

final class ArraySingleMultiLineFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Big arrays must be multiline.',
            [
                new CodeSample(
                    "<?php\n\$a = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];\n"
                ),
                new CodeSample(
                    "<?php\n\$a = [1, 2, 3];\n",
                    ['element_count' => 2, 'inner_length' => 10000, 'conditions' => 'any']
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ArrayIndentationFixer, TrailingCommaInMultilineFixer.
     * Must run after TrimArraySpacesFixer, WhitespaceAfterCommaInArrayFixer.
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
        return $tokens->isAnyTokenKindsFound([T_ARRAY, CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN, CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->findArrays($tokens) as $array) {
            if ($array['is_multi_line']) {
                continue;
            }

            $elementCountThreshold = $array['element_count'] >= $this->configuration['element_count'];
            $innerLengthThreshold = $array['inner_length'] >= $this->configuration['inner_length'];

            if ('any' === $this->configuration['conditions']) {
                if ($elementCountThreshold || $innerLengthThreshold) {
                    $this->expandArray($tokens, $array);
                }
            } elseif ($elementCountThreshold && $innerLengthThreshold) { // 'conditions' is always 'all' here
                $this->expandArray($tokens, $array);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $assertIntMoreThanZero = [static function (int $value): bool { return $value > 0; }];

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('element_count', 'Threshold: # of elements an array must have be be written multiline.'))
                ->setDefault(25)
                ->setAllowedTypes(['int'])
                ->setAllowedValues($assertIntMoreThanZero)
                ->getOption(),
            (new FixerOptionBuilder('inner_length', 'Threshold: # of characters there must be between the braces of an array for it to be made multiline.'))
                ->setDefault(120)
                ->setAllowedTypes(['int'])
                ->setAllowedValues($assertIntMoreThanZero)
                ->getOption(),
            (new FixerOptionBuilder('conditions', 'How the thresholds must be evaluated combined.'))
                ->setAllowedValues(['any', 'all'])
                ->setDefault('any')
                ->getOption(),
        ]);
    }

    private function findArrays(Tokens $tokens): iterable
    {
        $tokenCount = \count($tokens);

        for ($index = 1; $index < $tokenCount; ++$index) {
            if ($tokens[$index]->isGivenKind(CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN)) {
                $closeType = [CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE];
            } elseif ($tokens[$index]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $closeType = [CT::T_ARRAY_SQUARE_BRACE_CLOSE];
            } elseif ($tokens[$index]->isGivenKind(T_ARRAY)) {
                $index = $tokens->getNextTokenOfKind($index, ['(']);
                $closeType = ')';
            } else {
                continue;
            }

            yield $this->getArrayDetails($tokens, $index, $closeType);

            $tokenCount = \count($tokens); // reset the count as fixing might have changed it
        }
    }

    private function getArrayDetails(Tokens $tokens, int $index, $closeType): array
    {
        $tokenCount = \count($tokens);
        $array = [
            'commas' => [],
            'element_count' => 0,
            'inner_length' => 0,
            'is_multi_line' => false,
            'open_index' => $index,
        ];

        for ($i = $index + 1; $i < $tokenCount; ++$i) {
            $arrayToken = $tokens[$i];
            if ($arrayToken->equals($closeType)) {
                $array['close_index'] = $i;

                break;
            }

            $content = $arrayToken->getContent();
            $array['inner_length'] += \strlen($content);

            $blockType = Tokens::detectBlockType($arrayToken);

            if (null !== $blockType && true === $blockType['isStart']) {
                $i = $tokens->findBlockEnd($blockType['type'], $i);

                continue;
            }

            if ($arrayToken->equals(',')) {
                ++$array['element_count'];
                $array['commas'][] = $i;

                continue;
            }

            if (!$array['is_multi_line'] && str_contains($content, "\n")) {
                $array['is_multi_line'] = true;
            }
        }

        $prevMeaningfulTokenIndex = $tokens->getPrevMeaningfulToken($array['close_index']);
        $array['trailing_comma'] = $tokens[$prevMeaningfulTokenIndex]->equals(',');

        if ($prevMeaningfulTokenIndex !== $array['open_index'] && !$array['trailing_comma']) {
            ++$array['element_count'];
        }

        return $array;
    }

    private function expandArray(Tokens $tokens, array $array): void
    {
        $this->ensureMultiLine($tokens, $tokens->getPrevNonWhitespace($array['close_index']));

        foreach (array_reverse($array['commas']) as $commaIndex) {
            $this->ensureMultiLine($tokens, $commaIndex);
        }

        $this->ensureMultiLine($tokens, $array['open_index']);
    }

    private function ensureMultiLine(Tokens $tokens, int $candidateIndex): void
    {
        while (true) {
            $currentIndex = $candidateIndex;
            $candidateIndex = $tokens->getNonEmptySibling($candidateIndex, 1);
            $candidateToken = $tokens[$candidateIndex];

            if (str_contains($candidateToken->getContent(), "\n")) {
                return;
            }

            if ($candidateToken->isComment() || $candidateToken->isWhitespace()) {
                continue;
            }

            $tokens->ensureWhitespaceAtIndex($currentIndex, 1, $this->whitespacesConfig->getLineEnding());

            return;
        }
    }
}
