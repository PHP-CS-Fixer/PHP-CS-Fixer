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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class SpaceAfterSemicolonFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Fix whitespace after a semicolon.',
            [
                new CodeSample(
                    "<?php
                        sample();     \$test = 1;
                        sample();\$test = 2;
                        for ( ;;++\$sample) {
                        }\n"
                ),
                new CodeSample("<?php\nfor (\$i = 0; ; ++\$i) {\n}\n", [
                    'remove_in_empty_for_expressions' => true,
                ]),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after CombineConsecutiveUnsetsFixer, MultilineWhitespaceBeforeSemicolonsFixer, NoEmptyStatementFixer, OrderedClassElementsFixer, SingleImportPerStatementFixer, SingleTraitInsertPerStatementFixer.
     */
    public function getPriority(): int
    {
        return -1;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(';');
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('remove_in_empty_for_expressions', 'Whether spaces should be removed for empty `for` expressions.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $insideForParenthesesUntil = null;

        for ($index = 0, $max = \count($tokens) - 1; $index < $max; ++$index) {
            if (true === $this->configuration['remove_in_empty_for_expressions']) {
                if ($tokens[$index]->isGivenKind(T_FOR)) {
                    $index = $tokens->getNextMeaningfulToken($index);
                    $insideForParenthesesUntil = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                    continue;
                }

                if ($index === $insideForParenthesesUntil) {
                    $insideForParenthesesUntil = null;

                    continue;
                }
            }

            if (!$tokens[$index]->equals(';')) {
                continue;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                if (
                    !$tokens[$index + 1]->equalsAny([')', [T_INLINE_HTML]]) && (
                        false === $this->configuration['remove_in_empty_for_expressions']
                        || !$tokens[$index + 1]->equals(';')
                    )
                ) {
                    $tokens->insertAt($index + 1, new Token([T_WHITESPACE, ' ']));
                    ++$max;
                }

                continue;
            }

            if (
                null !== $insideForParenthesesUntil
                && ($tokens[$index + 2]->equals(';') || $index + 2 === $insideForParenthesesUntil)
                && !Preg::match('/\R/', $tokens[$index + 1]->getContent())
            ) {
                $tokens->clearAt($index + 1);

                continue;
            }

            if (
                isset($tokens[$index + 2])
                && !$tokens[$index + 1]->equals([T_WHITESPACE, ' '])
                && $tokens[$index + 1]->isWhitespace(" \t")
                && !$tokens[$index + 2]->isComment()
                && !$tokens[$index + 2]->equals(')')
            ) {
                $tokens[$index + 1] = new Token([T_WHITESPACE, ' ']);
            }
        }
    }
}
