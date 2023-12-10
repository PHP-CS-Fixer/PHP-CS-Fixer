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

/**
 * @author Adam Marczuk <adam@marczuk.info>
 */
final class WhitespaceAfterCommaInArrayFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'In array declaration, there MUST be a whitespace after each comma.',
            [
                new CodeSample("<?php\n\$sample = array(1,'a',\$b,);\n"),
                new CodeSample("<?php\n\$sample = [1,2, 3,  4,    5];\n", ['ensure_single_space' => true]),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('ensure_single_space', 'If there are only horizontal whitespaces after the comma then ensure it is a single space.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokensToInsert = [];

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
                continue;
            }

            if ($tokens[$index]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $startIndex = $index;
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
            } else {
                $startIndex = $tokens->getNextTokenOfKind($index, ['(']);
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
            }

            for ($i = $endIndex - 1; $i > $startIndex; --$i) {
                $i = $this->skipNonArrayElements($i, $tokens);
                if (!$tokens[$i]->equals(',')) {
                    continue;
                }
                if (!$tokens[$i + 1]->isWhitespace()) {
                    $tokensToInsert[$i + 1] = new Token([T_WHITESPACE, ' ']);
                } elseif (
                    $this->configuration['ensure_single_space']
                    && ' ' !== $tokens[$i + 1]->getContent()
                    && Preg::match('/^\h+$/', $tokens[$i + 1]->getContent())
                    && (!$tokens[$i + 2]->isComment() || Preg::match('/^\h+$/', $tokens[$i + 3]->getContent()))
                ) {
                    $tokens[$i + 1] = new Token([T_WHITESPACE, ' ']);
                }
            }
        }

        if ([] !== $tokensToInsert) {
            $tokens->insertSlices($tokensToInsert);
        }
    }

    /**
     * Method to move index over the non-array elements like function calls or function declarations.
     *
     * @return int New index
     */
    private function skipNonArrayElements(int $index, Tokens $tokens): int
    {
        if ($tokens[$index]->equals('}')) {
            return $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }

        if ($tokens[$index]->equals(')')) {
            $startIndex = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
            $startIndex = $tokens->getPrevMeaningfulToken($startIndex);
            if (!$tokens[$startIndex]->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
                return $startIndex;
            }
        }

        if ($tokens[$index]->equals(',') && $this->commaIsPartOfImplementsList($index, $tokens)) {
            --$index;
        }

        return $index;
    }

    private function commaIsPartOfImplementsList(int $index, Tokens $tokens): bool
    {
        do {
            $index = $tokens->getPrevMeaningfulToken($index);

            $current = $tokens[$index];
        } while ($current->isGivenKind(T_STRING) || $current->equals(','));

        return $current->isGivenKind(T_IMPLEMENTS);
    }
}
