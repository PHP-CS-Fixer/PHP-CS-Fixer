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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Utils;

/**
 * @author ErickSkrauch <erickskrauch@ely.by>
 */
final class BlankLineAroundClassBodyFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Ensure that class body contains one blank line after class definition and before its end.',
            [
                new CodeSample(
                    '<?php
class Sample
{
    protected function foo()
    {
    }
}
'
                ),
                new CodeSample(
                    '<?php
new class extends Foo {

    protected function foo()
    {
    }

};
',
                    ['apply_to_anonymous_classes' => false]
                ),
                new CodeSample(
                    '<?php
new class extends Foo {
    protected function foo()
    {
    }
};
',
                    ['apply_to_anonymous_classes' => true]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the BracesFixer
        return -26;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $analyzer = new TokensAnalyzer($tokens);
        foreach ($tokens as $index => $token) {
            if (!$token->isClassy()) {
                continue;
            }

            $countLines = $this->configuration['blank_lines_count'];
            if (!$this->configuration['apply_to_anonymous_classes'] && $analyzer->isAnonymousClass($index)) {
                $countLines = 0;
            }

            $startBraceIndex = $tokens->getNextTokenOfKind($index, ['{']);
            if ($tokens[$startBraceIndex + 1]->isWhitespace()) {
                $nextStatementIndex = $tokens->getNextMeaningfulToken($startBraceIndex);
                // Traits should be placed right after a class opening brace,
                if ('use' !== $tokens[$nextStatementIndex]->getContent()) {
                    $this->fixBlankLines($tokens, $startBraceIndex + 1, $countLines);
                }
            }

            $endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startBraceIndex);
            if ($tokens[$endBraceIndex - 1]->isWhitespace()) {
                $this->fixBlankLines($tokens, $endBraceIndex - 1, $countLines);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('blank_lines_count', 'adjusts an amount of the blank lines.'))
                ->setAllowedTypes(['int'])
                ->setDefault(1)
                ->getOption(),
            (new FixerOptionBuilder('apply_to_anonymous_classes', 'whether this fixer should be applied to anonymous classes.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    /**
     * Cleanup a whitespace token.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $countLines
     */
    private function fixBlankLines(Tokens $tokens, $index, $countLines)
    {
        $content = $tokens[$index]->getContent();
        // Apply fix only in the case when the count lines do not equals to expected
        if (substr_count($content, "\n") === $countLines + 1) {
            return;
        }

        // The final bit of the whitespace must be the next statement's indentation
        $lines = Utils::splitLines($content);
        $eol = $this->whitespacesConfig->getLineEnding();
        $tokens[$index] = new Token([T_WHITESPACE, str_repeat($eol, $countLines + 1).end($lines)]);
    }
}
