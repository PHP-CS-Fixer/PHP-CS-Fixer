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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Abdurrahman Uymaz <abdurrahman.uymaz@mobian.global>
 * @author Lars Grevelink <lars.grevelink@mobian.global>
 * @author Leander Philippo <lphilippo@adventive.es>
 */
final class AlphabeticalArrayKeySortFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        // Return a definition of the fixer, it will be used in the documentation.
        return new FixerDefinition(
            'Sorts keyed array by alphabetical order.',
            [
                new CodeSample(
                    "<?php\n\$sample = array('b' => '2', 'a' => '1', 'd' => '5');\n"
                ),
                new CodeSample(
                    "<?php\n\$sample = array('b' => '2', 'a' => '1', foo() => 'bar', 'd' => '5');\n",
                    ['sort_special_key_mode' => 'special_case_on_bottom']
                ),
                new CodeSample(
                    "<?php\n\$sample = array('b' => '2', 'a' => '1', foo() => 'bar', 'd' => '5');\n",
                    ['sort_special_key_mode' => 'special_case_on_top']
                ),
            ],
            null,
            'Risky when the order of the array has an impact on the code execution.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_ARRAY) || $tokens->isTokenKindFound(CT::T_ARRAY_SQUARE_BRACE_OPEN);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->sortTokens($tokens);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('sort_special_key_mode', 'In which way to sort the special keys'))
                ->setAllowedValues(['special_case_on_bottom', 'special_case_on_top'])
                ->setDefault('special_case_on_bottom')
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function sortTokens(Tokens $tokens)
    {
        $lastProcessedIndex = null;
        foreach ($tokens as $index => $token) {
            if (null !== $lastProcessedIndex && $index < $lastProcessedIndex) {
                continue;
            }

            if ($token->isGivenKind(T_ARRAY) || $token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $clonedTokens = clone $tokens;

                list($startIndex, $endIndex) = $this->getArrayIndexes($tokens, $index);

                $content = [];
                while (null !== $startIndex && $startIndex < $endIndex) {
                    $startIndex = $tokens->getNextMeaningfulToken($startIndex);
                    if ($tokens[$startIndex]->isGivenKind(T_ARRAY) || $tokens[$startIndex]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                        $lastProcessedIndex = $startIndex = $this->sortNestedTokens($clonedTokens, $startIndex);
                    }

                    if ($tokens[$startIndex]->isGivenKind(T_DOUBLE_ARROW)) {
                        list($key, $keyTokenIndex) = $this->getKeyAndEndPosition($clonedTokens, $startIndex);

                        $valueTokenIndex = $tokens->getNextMeaningfulToken($startIndex);

                        if ($tokens[$valueTokenIndex]->isGivenKind(T_ARRAY) || $tokens[$valueTokenIndex]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                            $startIndex = $this->sortNestedTokens($clonedTokens, $valueTokenIndex);
                        }

                        $valueRange = $startIndex;
                        while ($valueRange = $tokens->getNextTokenOfKind($valueRange, [',', '('])) {
                            if ($tokens[$valueRange]->equals('(')) {
                                $valueRange = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $valueRange);
                            } else {
                                break;
                            }
                        }

                        if (!$valueRange || $valueRange > $endIndex) {
                            $valueRange = $endIndex;
                        }

                        $valueEndIndex = $tokens->getPrevMeaningfulToken($valueRange);

                        $content[$key] = [$keyTokenIndex, $valueEndIndex];

                        $lastProcessedIndex = $startIndex = $valueEndIndex;
                    }
                }

                $contentKeys = array_keys($content);
                $sortedContentKeys = $this->sortContentKeys($contentKeys);

                $sorting = array_combine($contentKeys, $sortedContentKeys);

                if (0 === \count($content)) {
                    $tokens->overrideRange(0, $tokens->count() - 1, $clonedTokens);
                } else {
                    foreach (array_reverse($sorting, true) as $original => $replacement) {
                        list($startOriginalIndex, $endOriginalIndex) = $content[$original];
                        list($startReplacementIndex, $endReplacementIndex) = $content[$replacement];

                        $newTokens = \array_slice($clonedTokens->toArray(), $startReplacementIndex, $endReplacementIndex - $startReplacementIndex + 1);

                        $tokens->overrideRange($startOriginalIndex, $endOriginalIndex, $newTokens);
                    }
                }

                $tokens->clearEmptyTokens();
            }
        }
    }

    /**
     * Calculation sorting score base on configuration.
     *
     * @return array
     */
    protected function sortContentKeys(array $contentKeys)
    {
        $sortMode = $this->configuration['sort_special_key_mode'];
        $specialSortDirection = ('special_case_on_top' === $sortMode) ? -1 : 1;

        usort($contentKeys, function ($a, $b) use ($contentKeys, $specialSortDirection) {
            $aIsSpecial = $this->isSpecialKey($a);
            $bIsSpecial = $this->isSpecialKey($b);

            if ($aIsSpecial && $bIsSpecial) {
                // We want to ensure that "special keys" keep their previous order among eachother
                return array_search($a, $contentKeys, true) - array_search($b, $contentKeys, true);
            }

            if ($aIsSpecial) {
                return $specialSortDirection;
            }

            if ($bIsSpecial) {
                return -$specialSortDirection;
            }

            return strcmp($a, $b);
        });

        return $contentKeys;
    }

    /**
     * Get the key and the end position index.
     *
     * @param int $startIndex
     *
     * @return array
     */
    private function getKeyAndEndPosition(Tokens $clonedTokens, $startIndex)
    {
        $prevItemEndIndex = $clonedTokens->getPrevMeaningfulToken($clonedTokens->getPrevMeaningfulToken($startIndex));
        $prevItemEndToken = $clonedTokens[$prevItemEndIndex];

        if ($prevItemEndToken->equals('(')) {
            $hasSpecialKeys = !$clonedTokens[$clonedTokens->getPrevMeaningfulToken($prevItemEndIndex)]->isGivenKind(T_ARRAY);
        } else {
            $hasSpecialKeys = !($prevItemEndToken->isGivenKind([CT::T_ARRAY_SQUARE_BRACE_OPEN, T_ARRAY]) || $prevItemEndToken->equals(','));
        }

        if ($hasSpecialKeys) {
            $keyTokenIndex = $startIndex;

            for ($i = $startIndex; $i >= 0; --$i) {
                if ($clonedTokens[$i]->equals(')')) {
                    $i = $clonedTokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i);

                    continue;
                }

                if ($clonedTokens[$i]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                    $i = $clonedTokens->findBlockStart(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $i);

                    continue;
                }

                if ($clonedTokens[$i]->isGivenKind(T_ARRAY)) {
                    $keyTokenIndex = $clonedTokens->getNextMeaningfulToken($clonedTokens->getNextMeaningfulToken($i));

                    break;
                }

                if ($clonedTokens[$i]->equals(',') || $clonedTokens[$i]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                    $keyTokenIndex = $clonedTokens->getNextMeaningfulToken($i);

                    break;
                }
            }

            $keyTokens = Tokens::fromArray(\array_slice($clonedTokens->toArray(), $keyTokenIndex, $startIndex - $keyTokenIndex - 1));

            $key = implode('', array_map(function ($item) {
                return $item->getContent();
            }, $keyTokens->toArray()));
        } else {
            $keyTokenIndex = $clonedTokens->getPrevMeaningfulToken($startIndex);
            $key = $clonedTokens[$keyTokenIndex]->getContent();
        }

        return [$key, $keyTokenIndex];
    }

    /**
     * Sort nested array tokens and return the end index.
     *
     * @param int $index
     *
     * @return int
     */
    private function sortNestedTokens(Tokens $clonedTokens, $index)
    {
        list($nestedTokenStartIndex, $nestedTokenEndIndex) = $this->getArrayIndexes($clonedTokens, $index);

        $nestedArrayTokens = Tokens::fromArray(\array_slice($clonedTokens->toArray(), $nestedTokenStartIndex, $nestedTokenEndIndex - $nestedTokenStartIndex + 1));

        $this->sortTokens($nestedArrayTokens);

        $clonedTokens->overrideRange($nestedTokenStartIndex, $nestedTokenEndIndex, $nestedArrayTokens);

        if ($nestedArrayTokens->count() !== $clonedTokens->count()) {
            $clonedTokens->clearEmptyTokens();
        }

        return $nestedTokenEndIndex;
    }

    /**
     * Get start and end index of an array.
     *
     * @param int $startIndex
     *
     * @return array
     */
    private function getArrayIndexes(Tokens $tokens, $startIndex)
    {
        if ($tokens[$startIndex]->isGivenKind(T_ARRAY)) {
            $startParentheses = $tokens->getNextTokenOfKind($startIndex, ['(']);
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParentheses);
        } else {
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
        }

        return [
            $startIndex,
            $endIndex,
        ];
    }

    /**
     * Checks if the token represents a special key.
     *
     * @param string $value
     *
     * @return bool
     */
    private function isSpecialKey($value)
    {
        $tokens = Tokens::fromCode($value);

        if ($tokens->count() > 1) {
            return true;
        }

        return !Preg::match('/^(\'|").+(\'|")$/i', $value);
    }
}
