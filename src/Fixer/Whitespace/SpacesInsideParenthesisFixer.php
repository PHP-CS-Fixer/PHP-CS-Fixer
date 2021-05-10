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
final class SpacesInsideParenthesisFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    private $singleLineWhitespaceOptions = " \t\n\r\0\x0B";

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Parenthesis must be declared using the configured syntax.',
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
                    ['space' => 'spaces']
                ),
                new CodeSample(
                    "<?php
function foo(\$bar, \$baz)
{
}\n",
                    ['space' => 'spaces']
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before FunctionToConstantFixer.
     * Must run after CombineConsecutiveIssetsFixer, CombineNestedDirnameFixer, LambdaNotUsedImportFixer, NoUselessSprintfFixer, PowToExponentiationFixer.
     */
    public function getPriority(): int
    {
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     */
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

        if ('spaces' === $this->configuration['space']) {
            foreach ($tokens as $index => $token) {
                if (!$token->equals('(')) {
                    continue;
                }

                $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                // if not other content than spaces in block remove spaces
                $blockContent = $this->getBlockContent($index, $endParenthesisIndex, $tokens);
                if (1 === \count($blockContent) && !$this->blockHasOtherContentThan($blockContent, ' ')) {
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

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('space', 'whether to have `spaces` or `none` spaces inside parenthesis.'))
                ->setAllowedValues(['none', 'spaces'])
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

        if ($token->isWhitespace() && false === strpos($token->getContent(), "\n")) {
            $tokens->clearAt($index);
        }
    }

    private function fixParenthesisInnerEdge(Tokens $tokens, $start, $end): void
    {
        // fix white space before ')'
        if ($tokens[$end - 1]->isWhitespace()) {
            $content = $tokens[$end - 1]->getContent();
            if (' ' !== $content && false === strpos($content, "\n") && !$tokens[$tokens->getPrevNonWhitespace($end - 1)]->isComment()) {
                $tokens[$end - 1] = new Token([T_WHITESPACE, ' ']);
            }
        } else {
            $tokens->insertAt($end, new Token([T_WHITESPACE, ' ']));
        }

        // fix white space after '('
        if ($tokens[$start + 1]->isWhitespace()) {
            $content = $tokens[$start + 1]->getContent();
            if (' ' !== $content && false === strpos($content, "\n") && !$tokens[$tokens->getNextNonWhitespace($start + 1)]->isComment()) {
                $tokens[$start + 1] = new Token([T_WHITESPACE, ' ']);
            }
        } else {
            $tokens->insertAt($start + 1, new Token([T_WHITESPACE, ' ']));
        }
    }

    private function getBlockContent(int $startIndex, int $endIndex, Tokens $tokens): array
    {
        // + 1 for (
        $contents = [];
        for ($i = ($startIndex + 1); $i < $endIndex; ++$i) {
            $contents[] = $tokens[$i]->getContent();
        }

        return $contents;
    }

    private function blockHasOtherContentThan(array $block, $otherThan): bool
    {
        if (!\in_array($otherThan, $block, true)) {
            return true;
        }

        $isOther = true;
        for ($i = 0; $i < \count($block); ++$i) {
            if ($block[$i] === $otherThan) {
                $isOther = false;
            }
        }

        return $isOther;
    }
}
