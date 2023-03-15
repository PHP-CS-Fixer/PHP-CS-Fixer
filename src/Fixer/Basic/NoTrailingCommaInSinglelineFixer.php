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

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\AttributeAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

final class NoTrailingCommaInSinglelineFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'If a list of values separated by a comma is contained on a single line, then the last item MUST NOT have a trailing comma.',
            [
                new CodeSample("<?php\nfoo(\$a,);\n\$foo = array(1,);\n[\$foo, \$bar,] = \$array;\nuse a\\{ClassA, ClassB,};\n"),
                new CodeSample("<?php\nfoo(\$a,);\n[\$foo, \$bar,] = \$array;\n", ['elements' => ['array_destructuring']]),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return
            $tokens->isTokenKindFound(',')
            && $tokens->isAnyTokenKindsFound([')', CT::T_ARRAY_SQUARE_BRACE_CLOSE, CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE, CT::T_GROUP_IMPORT_BRACE_CLOSE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        $elements = ['arguments', 'array_destructuring', 'array', 'group_import'];

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('elements', 'Which elements to fix.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset($elements)])
                ->setDefault($elements)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens[$index]->equals(')') && !$tokens[$index]->isGivenKind([CT::T_ARRAY_SQUARE_BRACE_CLOSE, CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE, CT::T_GROUP_IMPORT_BRACE_CLOSE])) {
                continue;
            }

            $commaIndex = $tokens->getPrevMeaningfulToken($index);

            if (!$tokens[$commaIndex]->equals(',')) {
                continue;
            }

            $block = Tokens::detectBlockType($tokens[$index]);
            $blockOpenIndex = $tokens->findBlockStart($block['type'], $index);

            if ($tokens->isPartialCodeMultiline($blockOpenIndex, $index)) {
                continue;
            }

            if (!$this->shouldBeCleared($tokens, $blockOpenIndex)) {
                continue;
            }

            do {
                $tokens->clearTokenAndMergeSurroundingWhitespace($commaIndex);
                $commaIndex = $tokens->getPrevMeaningfulToken($commaIndex);
            } while ($tokens[$commaIndex]->equals(','));

            $tokens->removeTrailingWhitespace($commaIndex);
        }
    }

    private function shouldBeCleared(Tokens $tokens, int $openIndex): bool
    {
        /** @var string[] $elements */
        $elements = $this->configuration['elements'];

        if ($tokens[$openIndex]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
            return \in_array('array', $elements, true);
        }

        if ($tokens[$openIndex]->isGivenKind(CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN)) {
            return \in_array('array_destructuring', $elements, true);
        }

        if ($tokens[$openIndex]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_OPEN)) {
            return \in_array('group_import', $elements, true);
        }

        if (!$tokens[$openIndex]->equals('(')) {
            return false;
        }

        $beforeOpen = $tokens->getPrevMeaningfulToken($openIndex);

        if ($tokens[$beforeOpen]->isGivenKind(T_ARRAY)) {
            return \in_array('array', $elements, true);
        }

        if ($tokens[$beforeOpen]->isGivenKind(T_LIST)) {
            return \in_array('array_destructuring', $elements, true);
        }

        if ($tokens[$beforeOpen]->isGivenKind([T_UNSET, T_ISSET, T_VARIABLE, T_CLASS])) {
            return \in_array('arguments', $elements, true);
        }

        if ($tokens[$beforeOpen]->isGivenKind(T_STRING)) {
            return !AttributeAnalyzer::isAttribute($tokens, $beforeOpen) && \in_array('arguments', $elements, true);
        }

        if ($tokens[$beforeOpen]->equalsAny([')', ']', [CT::T_DYNAMIC_VAR_BRACE_CLOSE], [CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE]])) {
            $block = Tokens::detectBlockType($tokens[$beforeOpen]);

            return
                (
                    Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE === $block['type']
                    || Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE === $block['type']
                    || Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE === $block['type']
                    || Tokens::BLOCK_TYPE_PARENTHESIS_BRACE === $block['type']
                ) && \in_array('arguments', $elements, true);
        }

        return false;
    }
}
