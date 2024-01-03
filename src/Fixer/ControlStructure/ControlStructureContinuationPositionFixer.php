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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

final class ControlStructureContinuationPositionFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    public const NEXT_LINE = 'next_line';

    /**
     * @internal
     */
    public const SAME_LINE = 'same_line';

    private const CONTROL_CONTINUATION_TOKENS = [
        T_CATCH,
        T_ELSE,
        T_ELSEIF,
        T_FINALLY,
        T_WHILE,
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Control structure continuation keyword must be on the configured line.',
            [
                new CodeSample(
                    '<?php
if ($baz == true) {
    echo "foo";
}
else {
    echo "bar";
}
'
                ),
                new CodeSample(
                    '<?php
if ($baz == true) {
    echo "foo";
} else {
    echo "bar";
}
',
                    ['position' => self::NEXT_LINE]
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::CONTROL_CONTINUATION_TOKENS);
    }

    /**
     * {@inheritdoc}
     *
     * Must run after ControlStructureBracesFixer.
     */
    public function getPriority(): int
    {
        return parent::getPriority();
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('position', 'The position of the keyword that continues the control structure.'))
                ->setAllowedValues([self::NEXT_LINE, self::SAME_LINE])
                ->setDefault(self::SAME_LINE)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->fixControlContinuationBraces($tokens);
    }

    private function fixControlContinuationBraces(Tokens $tokens): void
    {
        for ($index = \count($tokens) - 1; 0 < $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(self::CONTROL_CONTINUATION_TOKENS)) {
                continue;
            }

            $prevIndex = $tokens->getPrevNonWhitespace($index);
            $prevToken = $tokens[$prevIndex];

            if (!$prevToken->equals('}')) {
                continue;
            }

            if ($token->isGivenKind(T_WHILE)) {
                $prevIndex = $tokens->getPrevMeaningfulToken(
                    $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $prevIndex)
                );

                if (!$tokens[$prevIndex]->isGivenKind(T_DO)) {
                    continue;
                }
            }

            $tokens->ensureWhitespaceAtIndex(
                $index - 1,
                1,
                self::NEXT_LINE === $this->configuration['position'] ?
                    $this->whitespacesConfig->getLineEnding().WhitespacesAnalyzer::detectIndent($tokens, $index)
                    : ' '
            );
        }
    }
}
