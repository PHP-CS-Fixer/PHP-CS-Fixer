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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class EmptyLoopConditionFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private const STYLE_FOR = 'for';

    private const STYLE_WHILE = 'while';

    private const TOKEN_LOOP_KINDS = [T_FOR, T_WHILE];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Empty loop-condition must be in configured style.',
            [
                new CodeSample("<?php\nfor(;;) {\n    foo();\n}\n\ndo {\n    foo();\n} while(true); // do while\n"),
                new CodeSample("<?php\nwhile(true) {\n    foo();\n}\n", ['style' => 'for']),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoExtraBlankLinesFixer, NoTrailingWhitespaceFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::TOKEN_LOOP_KINDS);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if (self::STYLE_WHILE === $this->configuration['style']) {
            $candidateLoopKinds = [T_FOR, T_WHILE];
            $replacement = [new Token([T_WHILE, 'while']), new Token([T_WHITESPACE, ' ']), new Token('('), new Token([T_STRING, 'true']), new Token(')')];

            $fixLoop = static function (int $index, int $openIndex, int $endIndex) use ($tokens, $replacement): void {
                if (self::isForLoopWithEmptyCondition($tokens, $index, $openIndex, $endIndex)) {
                    self::clearNotCommentsInRange($tokens, $index, $endIndex);
                    self::cloneAndInsert($tokens, $index, $replacement);
                } elseif (self::isWhileLoopWithEmptyCondition($tokens, $index, $openIndex, $endIndex)) {
                    $doIndex = self::getDoIndex($tokens, $index);

                    if (null !== $doIndex) {
                        self::clearNotCommentsInRange($tokens, $index, $tokens->getNextMeaningfulToken($endIndex)); // clear including `;`
                        $tokens->clearAt($doIndex);
                        self::cloneAndInsert($tokens, $doIndex, $replacement);
                    }
                }
            };
        } else { // self::STYLE_FOR
            $candidateLoopKinds = [T_WHILE];
            $replacement = [new Token([T_FOR, 'for']), new Token('('), new Token(';'), new Token(';'), new Token(')')];

            $fixLoop = static function (int $index, int $openIndex, int $endIndex) use ($tokens, $replacement): void {
                if (!self::isWhileLoopWithEmptyCondition($tokens, $index, $openIndex, $endIndex)) {
                    return;
                }

                $doIndex = self::getDoIndex($tokens, $index);

                if (null === $doIndex) {
                    self::clearNotCommentsInRange($tokens, $index, $endIndex);
                    self::cloneAndInsert($tokens, $index, $replacement);
                } else {
                    self::clearNotCommentsInRange($tokens, $index, $tokens->getNextMeaningfulToken($endIndex)); // clear including `;`
                    $tokens->clearAt($doIndex);
                    self::cloneAndInsert($tokens, $doIndex, $replacement);
                }
            };
        }

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if ($tokens[$index]->isGivenKind($candidateLoopKinds)) {
                $openIndex = $tokens->getNextTokenOfKind($index, ['(']); // proceed to open '('
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex); // proceed to close ')'
                $fixLoop($index, $openIndex, $endIndex); // fix loop if needed
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('style', 'Style of empty loop-condition.'))
                ->setAllowedTypes(['string'])
                ->setAllowedValues([self::STYLE_WHILE, self::STYLE_FOR])
                ->setDefault(self::STYLE_WHILE)
                ->getOption(),
        ]);
    }

    private static function clearNotCommentsInRange(Tokens $tokens, int $indexStart, int $indexEnd): void
    {
        for ($i = $indexStart; $i <= $indexEnd; ++$i) {
            if (!$tokens[$i]->isComment()) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($i);
            }
        }
    }

    /**
     * @param Token[] $replacement
     */
    private static function cloneAndInsert(Tokens $tokens, int $index, array $replacement): void
    {
        $replacementClones = [];

        foreach ($replacement as $token) {
            $replacementClones[] = clone $token;
        }

        $tokens->insertAt($index, $replacementClones);
    }

    private static function getDoIndex(Tokens $tokens, int $index): ?int
    {
        $endIndex = $tokens->getPrevMeaningfulToken($index);

        if (!$tokens[$endIndex]->equals('}')) {
            return null;
        }

        $startIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $endIndex);
        $index = $tokens->getPrevMeaningfulToken($startIndex);

        return null === $index || !$tokens[$index]->isGivenKind(T_DO) ? null : $index;
    }

    private static function isForLoopWithEmptyCondition(Tokens $tokens, int $index, int $openIndex, int $endIndex): bool
    {
        if (!$tokens[$index]->isGivenKind(T_FOR)) {
            return false;
        }

        $index = $tokens->getNextMeaningfulToken($openIndex);

        if (null === $index || !$tokens[$index]->equals(';')) {
            return false;
        }

        $index = $tokens->getNextMeaningfulToken($index);

        return null !== $index && $tokens[$index]->equals(';') && $endIndex === $tokens->getNextMeaningfulToken($index);
    }

    private static function isWhileLoopWithEmptyCondition(Tokens $tokens, int $index, int $openIndex, int $endIndex): bool
    {
        if (!$tokens[$index]->isGivenKind(T_WHILE)) {
            return false;
        }

        $index = $tokens->getNextMeaningfulToken($openIndex);

        return null !== $index && $tokens[$index]->equals([T_STRING, 'true']) && $endIndex === $tokens->getNextMeaningfulToken($index);
    }
}
