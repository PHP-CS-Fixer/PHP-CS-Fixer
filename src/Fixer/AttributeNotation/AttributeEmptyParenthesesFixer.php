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

namespace PhpCsFixer\Fixer\AttributeNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author HypeMC <hypemc@gmail.com>
 */
final class AttributeEmptyParenthesesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHP attributes declared without arguments must (not) be followed by empty parentheses.',
            [
                new CodeSample("<?php\n\n#[Foo()]\nclass Sample1 {}\n\n#[Bar(), Baz()]\nclass Sample2 {}\n"),
                new CodeSample(
                    "<?php\n\n#[Foo]\nclass Sample1 {}\n\n#[Bar, Baz]\nclass Sample2 {}\n",
                    ['use_parentheses' => true]
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \defined('T_ATTRIBUTE') && $tokens->isTokenKindFound(T_ATTRIBUTE);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('use_parentheses', 'Whether attributes should be followed by parentheses or not.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $index = 0;

        while (null !== $index = $tokens->getNextTokenOfKind($index, [[T_ATTRIBUTE]])) {
            $nextIndex = $index;

            do {
                $parenthesesIndex = $tokens->getNextTokenOfKind($nextIndex, ['(', ',', [CT::T_ATTRIBUTE_CLOSE]]);

                if (true === $this->configuration['use_parentheses']) {
                    $this->ensureParenthesesAt($tokens, $parenthesesIndex);
                } else {
                    $this->ensureNoParenthesesAt($tokens, $parenthesesIndex);
                }

                $nextIndex = $tokens->getNextTokenOfKind($nextIndex, ['(', ',', [CT::T_ATTRIBUTE_CLOSE]]);

                // Find closing parentheses, we need to do this in case there's a comma inside the parentheses
                if ($tokens[$nextIndex]->equals('(')) {
                    $nextIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextIndex);
                    $nextIndex = $tokens->getNextTokenOfKind($nextIndex, [',', [CT::T_ATTRIBUTE_CLOSE]]);
                }

                // In case there's a comma right before T_ATTRIBUTE_CLOSE
                if (!$tokens[$nextIndex]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                    $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
                }
            } while (!$tokens[$nextIndex]->isGivenKind(CT::T_ATTRIBUTE_CLOSE));
        }
    }

    private function ensureParenthesesAt(Tokens $tokens, int $index): void
    {
        if ($tokens[$index]->equals('(')) {
            return;
        }

        $tokens->insertAt(
            $tokens->getPrevMeaningfulToken($index) + 1,
            [new Token('('), new Token(')')]
        );
    }

    private function ensureNoParenthesesAt(Tokens $tokens, int $index): void
    {
        if (!$tokens[$index]->equals('(')) {
            return;
        }

        $closingIndex = $tokens->getNextMeaningfulToken($index);

        // attribute has arguments - parentheses can not be removed
        if (!$tokens[$closingIndex]->equals(')')) {
            return;
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($closingIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }
}
