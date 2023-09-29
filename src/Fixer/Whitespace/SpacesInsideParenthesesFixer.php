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

namespace PhpCsFixer\Fixer\Whitespace;

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
 * Fixer for rules defined in PSR2 ¶4.3, ¶4.6, ¶5.
 *
 * @author Marc Aubé
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SpacesInsideParenthesesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Parentheses must be declared using the configured whitespace.',
            [
                new CodeSample("<?php\nif ( \$a ) {\n    foo( );\n}\n"),
                new CodeSample(
                    "<?php
function foo( \$bar, \$baz )
{
}\n",
                    ['space' => 'none']
                ),
                new CodeSample(
                    "<?php\nif (\$a) {\n    foo( );\n}\n",
                    ['space' => 'single']
                ),
                new CodeSample(
                    "<?php
function foo(\$bar, \$baz)
{
}\n",
                    ['space' => 'single']
                ),
            ],
            'By default there are not any additional spaces inside parentheses, however with `space=single` configuration option whitespace inside parentheses will be unified to single space.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before FunctionToConstantFixer, GetClassToClassKeywordFixer, StringLengthToEmptyFixer.
     * Must run after CombineConsecutiveIssetsFixer, CombineNestedDirnameFixer, IncrementStyleFixer, LambdaNotUsedImportFixer, ModernizeStrposFixer, NoUselessSprintfFixer, PowToExponentiationFixer.
     */
    public function getPriority(): int
    {
        return 3;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound('(');
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if ('none' === $this->configuration['space']) {
            foreach ($tokens as $index => $token) {
                if (!$token->equals('(')) {
                    continue;
                }

                $prevIndex = $tokens->getPrevMeaningfulToken($index);

                // ignore parenthesis for T_ARRAY
                if (null !== $prevIndex && $tokens[$prevIndex]->isGivenKind(T_ARRAY)) {
                    continue;
                }

                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                // remove space after opening `(`
                if (!$tokens[$tokens->getNextNonWhitespace($index)]->isComment()) {
                    $this->removeSpaceAroundToken($tokens, $index + 1);
                }

                // remove space before closing `)` if it is not `list($a, $b, )` case
                if (!$tokens[$tokens->getPrevMeaningfulToken($endIndex)]->equals(',')) {
                    $this->removeSpaceAroundToken($tokens, $endIndex - 1);
                }
            }
        }

        if ('single' === $this->configuration['space']) {
            foreach ($tokens as $index => $token) {
                if (!$token->equals('(')) {
                    continue;
                }

                $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                // if not other content than spaces in block remove spaces
                $blockContent = $this->getBlockContent($index, $endParenthesisIndex, $tokens);
                if (1 === \count($blockContent) && \in_array(' ', $blockContent, true)) {
                    $this->removeSpaceAroundToken($tokens, $index + 1);

                    continue;
                }

                // don't process if the next token is `)`
                $nextMeaningfulTokenIndex = $tokens->getNextMeaningfulToken($index);
                if (')' === $tokens[$nextMeaningfulTokenIndex]->getContent()) {
                    continue;
                }

                $afterParenthesisIndex = $tokens->getNextNonWhitespace($endParenthesisIndex);
                $afterParenthesisToken = $tokens[$afterParenthesisIndex];

                if ($afterParenthesisToken->isGivenKind(CT::T_USE_LAMBDA)) {
                    $useStartParenthesisIndex = $tokens->getNextTokenOfKind($afterParenthesisIndex, ['(']);
                    $useEndParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $useStartParenthesisIndex);

                    // add single-line edge whitespaces inside use parentheses
                    $this->fixParenthesisInnerEdge($tokens, $useStartParenthesisIndex, $useEndParenthesisIndex);
                }

                // add single-line edge whitespaces inside parameters list parentheses
                $this->fixParenthesisInnerEdge($tokens, $index, $endParenthesisIndex);
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('space', 'Whether to have `single` or `none` space inside parentheses.'))
                ->setAllowedValues(['none', 'single'])
                ->setDefault('none')
                ->getOption(),
        ]);
    }

    /**
     * Remove spaces from token at a given index.
     */
    private function removeSpaceAroundToken(Tokens $tokens, int $index): void
    {
        $token = $tokens[$index];

        if ($token->isWhitespace() && !str_contains($token->getContent(), "\n")) {
            $tokens->clearAt($index);
        }
    }

    private function fixParenthesisInnerEdge(Tokens $tokens, int $start, int $end): void
    {
        // fix white space before ')'
        if ($tokens[$end - 1]->isWhitespace()) {
            $content = $tokens[$end - 1]->getContent();
            if (' ' !== $content && !str_contains($content, "\n") && !$tokens[$tokens->getPrevNonWhitespace($end - 1)]->isComment()) {
                $tokens[$end - 1] = new Token([T_WHITESPACE, ' ']);
            }
        } else {
            $tokens->insertAt($end, new Token([T_WHITESPACE, ' ']));
        }

        // fix white space after '('
        if ($tokens[$start + 1]->isWhitespace()) {
            $content = $tokens[$start + 1]->getContent();
            if (' ' !== $content && !str_contains($content, "\n") && !$tokens[$tokens->getNextNonWhitespace($start + 1)]->isComment()) {
                $tokens[$start + 1] = new Token([T_WHITESPACE, ' ']);
            }
        } else {
            $tokens->insertAt($start + 1, new Token([T_WHITESPACE, ' ']));
        }
    }

    /**
     * @return list<string>
     */
    private function getBlockContent(int $startIndex, int $endIndex, Tokens $tokens): array
    {
        // + 1 for (
        $contents = [];
        for ($i = ($startIndex + 1); $i < $endIndex; ++$i) {
            $contents[] = $tokens[$i]->getContent();
        }

        return $contents;
    }
}
