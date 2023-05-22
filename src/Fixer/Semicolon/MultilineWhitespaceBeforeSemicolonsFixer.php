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

namespace PhpCsFixer\Fixer\Semicolon;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Egidijus Girčys <e.gircys@gmail.com>
 */
final class MultilineWhitespaceBeforeSemicolonsFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    public const STRATEGY_NO_MULTI_LINE = 'no_multi_line';

    /**
     * @internal
     */
    public const STRATEGY_NEW_LINE_FOR_CHAINED_CALLS = 'new_line_for_chained_calls';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.',
            [
                new CodeSample(
                    '<?php
function foo() {
    return 1 + 2
        ;
}
'
                ),
                new CodeSample(
                    '<?php
$object->method1()
    ->method2()
    ->method(3);
',
                    ['strategy' => self::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before SpaceAfterSemicolonFixer.
     * Must run after CombineConsecutiveIssetsFixer, GetClassToClassKeywordFixer, NoEmptyStatementFixer, SimplifiedIfReturnFixer, SingleImportPerStatementFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(';');
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(
                'strategy',
                'Forbid multi-line whitespace or move the semicolon to the new line for chained calls.'
            ))
                ->setAllowedValues([self::STRATEGY_NO_MULTI_LINE, self::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS])
                ->setDefault(self::STRATEGY_NO_MULTI_LINE)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($index = 0, $count = \count($tokens); $index < $count; ++$index) {
            if (!$tokens[$index]->equals(';')) {
                continue;
            }

            $previousIndex = $index - 1;
            $previous = $tokens[$previousIndex];

            $indent = $this->findWhitespaceBeforeFirstCall($index, $tokens);
            if (self::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS === $this->configuration['strategy'] && null !== $indent) {
                if ($previous->isWhitespace() && $previous->getContent() === $lineEnding.$indent) {
                    continue;
                }

                // unset whitespace and semicolon
                if ($previous->isWhitespace()) {
                    $tokens->clearAt($previousIndex);
                }
                $tokens->clearAt($index);

                // find the line ending token index after the semicolon
                $index = $this->getNewLineIndex($index, $tokens);

                // appended new line to the last method call
                $newline = new Token([T_WHITESPACE, $lineEnding.$indent]);

                // insert the new line with indented semicolon
                $tokens->insertAt($index++, [$newline, new Token(';')]);
            } else {
                if (!$previous->isWhitespace() || !str_contains($previous->getContent(), "\n")) {
                    continue;
                }

                $content = $previous->getContent();
                if (str_starts_with($content, $lineEnding) && $tokens[$index - 2]->isComment()) {
                    // if there is comment between closing bracket and semicolon

                    // unset whitespace and semicolon
                    $tokens->clearAt($previousIndex);
                    $tokens->clearAt($index);

                    // find the significant token index before the semicolon
                    $significantTokenIndex = $this->getPreviousSignificantTokenIndex($index, $tokens);

                    // insert the semicolon
                    $tokens->insertAt($significantTokenIndex + 1, [new Token(';')]);
                } else {
                    // if there is whitespace between closing bracket and semicolon, just remove it
                    $tokens->clearAt($previousIndex);
                }
            }
        }
    }

    /**
     * Find the index for the next new line. Return the given index when there's no new line.
     */
    private function getNewLineIndex(int $index, Tokens $tokens): int
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($index, $count = \count($tokens); $index < $count; ++$index) {
            if (false !== strstr($tokens[$index]->getContent(), $lineEnding)) {
                return $index;
            }
        }

        return $index;
    }

    /**
     * Find the index for the previous significant token. Return the given index when there's no significant token.
     */
    private function getPreviousSignificantTokenIndex(int $index, Tokens $tokens): int
    {
        $stopTokens = [
            T_LNUMBER,
            T_DNUMBER,
            T_STRING,
            T_VARIABLE,
            T_CONSTANT_ENCAPSED_STRING,
        ];
        for ($index; $index > 0; --$index) {
            if ($tokens[$index]->isGivenKind($stopTokens) || $tokens[$index]->equals(')')) {
                return $index;
            }
        }

        return $index;
    }

    /**
     * Checks if the semicolon closes a multiline call and returns the whitespace of the first call at $index.
     * i.e. it will return the whitespace marked with '____' in the example underneath.
     *
     * ..
     * ____$this->methodCall()
     *          ->anotherCall();
     * ..
     */
    private function findWhitespaceBeforeFirstCall(int $index, Tokens $tokens): ?string
    {
        $isMultilineCall = false;
        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        while (!$tokens[$prevIndex]->equalsAny([';', '{', '}', [T_OPEN_TAG], [T_ELSE]])) {
            $index = $prevIndex;
            $prevIndex = $tokens->getPrevMeaningfulToken($index);

            $blockType = Tokens::detectBlockType($tokens[$index]);
            if (null !== $blockType && !$blockType['isStart']) {
                $prevIndex = $tokens->findBlockStart($blockType['type'], $index);

                continue;
            }

            if ($tokens[$index]->isObjectOperator() || $tokens[$index]->isGivenKind(T_DOUBLE_COLON)) {
                $prevIndex = $tokens->getPrevMeaningfulToken($index);
                $isMultilineCall |= $tokens->isPartialCodeMultiline($prevIndex, $index);
            }
        }

        return $isMultilineCall ? WhitespacesAnalyzer::detectIndent($tokens, $index) : null;
    }
}
