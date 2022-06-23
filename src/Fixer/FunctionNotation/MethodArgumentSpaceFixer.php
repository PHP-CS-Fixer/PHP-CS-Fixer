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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Options;

/**
 * Fixer for rules defined in PSR2 ¶4.4, ¶4.6.
 *
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 */
final class MethodArgumentSpaceFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.',
            [
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);\n",
                    null
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);\n",
                    ['keep_multiple_spaces_after_comma' => false]
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);\n",
                    ['keep_multiple_spaces_after_comma' => true]
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,\n    2);\n",
                    ['on_multiline' => 'ensure_fully_multiline']
                ),
                new CodeSample(
                    "<?php\nfunction sample(\n    \$a=10,\n    \$b=20,\n    \$c=30\n) {}\nsample(\n    1,\n    2\n);\n",
                    ['on_multiline' => 'ensure_single_line']
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,  \n    2);\nsample('foo',    'foobarbaz', 'baz');\nsample('foobar', 'bar',       'baz');\n",
                    [
                        'on_multiline' => 'ensure_fully_multiline',
                        'keep_multiple_spaces_after_comma' => true,
                    ]
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,  \n    2);\nsample('foo',    'foobarbaz', 'baz');\nsample('foobar', 'bar',       'baz');\n",
                    [
                        'on_multiline' => 'ensure_fully_multiline',
                        'keep_multiple_spaces_after_comma' => false,
                    ]
                ),
                new VersionSpecificCodeSample(
                    <<<'SAMPLE'
<?php
sample(
    <<<EOD
        foo
        EOD
    ,
    'bar'
);

SAMPLE
                    ,
                    new VersionSpecification(70300),
                    ['after_heredoc' => true]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound('(');
    }

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        if (isset($configuration['ensure_fully_multiline'])) {
            $this->configuration['on_multiline'] = $this->configuration['ensure_fully_multiline']
                ? 'ensure_fully_multiline'
                : 'ignore';
        }
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ArrayIndentationFixer.
     * Must run after CombineNestedDirnameFixer, FunctionDeclarationFixer, ImplodeCallFixer, LambdaNotUsedImportFixer, MethodChainingIndentationFixer, NoMultilineWhitespaceAroundDoubleArrowFixer, NoUselessSprintfFixer, PowToExponentiationFixer, StrictParamFixer.
     */
    public function getPriority(): int
    {
        return 30;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $expectedTokens = [T_LIST, T_FUNCTION, CT::T_USE_LAMBDA, T_FN, T_CLASS];

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            if (!$token->equals('(')) {
                continue;
            }

            $meaningfulTokenBeforeParenthesis = $tokens[$tokens->getPrevMeaningfulToken($index)];

            if (
                $meaningfulTokenBeforeParenthesis->isKeyword()
                && !$meaningfulTokenBeforeParenthesis->isGivenKind($expectedTokens)
            ) {
                continue;
            }

            $isMultiline = $this->fixFunction($tokens, $index);

            if (
                $isMultiline
                && 'ensure_fully_multiline' === $this->configuration['on_multiline']
                && !$meaningfulTokenBeforeParenthesis->isGivenKind(T_LIST)
            ) {
                $this->ensureFunctionFullyMultiline($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('keep_multiple_spaces_after_comma', 'Whether keep multiple spaces after comma.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder(
                'on_multiline',
                'Defines how to handle function arguments lists that contain newlines.'
            ))
                ->setAllowedValues(['ignore', 'ensure_single_line', 'ensure_fully_multiline'])
                ->setDefault('ensure_fully_multiline')
                ->getOption(),
            (new FixerOptionBuilder('after_heredoc', 'Whether the whitespace between heredoc end and comma should be removed.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->setNormalizer(static function (Options $options, $value) {
                    return $value;
                })
                ->getOption(),
        ]);
    }

    /**
     * Fix arguments spacing for given function.
     *
     * @param Tokens $tokens             Tokens to handle
     * @param int    $startFunctionIndex Start parenthesis position
     *
     * @return bool whether the function is multiline
     */
    private function fixFunction(Tokens $tokens, int $startFunctionIndex): bool
    {
        $isMultiline = false;

        $endFunctionIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);
        $firstWhitespaceIndex = $this->findWhitespaceIndexAfterParenthesis($tokens, $startFunctionIndex, $endFunctionIndex);
        $lastWhitespaceIndex = $this->findWhitespaceIndexAfterParenthesis($tokens, $endFunctionIndex, $startFunctionIndex);

        foreach ([$firstWhitespaceIndex, $lastWhitespaceIndex] as $index) {
            if (null === $index || !Preg::match('/\R/', $tokens[$index]->getContent())) {
                continue;
            }

            if ('ensure_single_line' !== $this->configuration['on_multiline']) {
                $isMultiline = true;

                continue;
            }

            $newLinesRemoved = $this->ensureSingleLine($tokens, $index);

            if (!$newLinesRemoved) {
                $isMultiline = true;
            }
        }

        for ($index = $endFunctionIndex - 1; $index > $startFunctionIndex; --$index) {
            $token = $tokens[$index];

            if ($token->equals(')')) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);

                continue;
            }

            if ($token->equals('}')) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            if ($token->equals(',')) {
                $this->fixSpace($tokens, $index);
                if (!$isMultiline && $this->isNewline($tokens[$index + 1])) {
                    $isMultiline = true;
                }
            }
        }

        return $isMultiline;
    }

    private function findWhitespaceIndexAfterParenthesis(Tokens $tokens, int $startParenthesisIndex, int $endParenthesisIndex): ?int
    {
        $direction = $endParenthesisIndex > $startParenthesisIndex ? 1 : -1;
        $startIndex = $startParenthesisIndex + $direction;
        $endIndex = $endParenthesisIndex - $direction;

        for ($index = $startIndex; $index !== $endIndex; $index += $direction) {
            $token = $tokens[$index];

            if ($token->isWhitespace()) {
                return $index;
            }

            if (!$token->isComment()) {
                break;
            }
        }

        return null;
    }

    /**
     * @return bool Whether newlines were removed from the whitespace token
     */
    private function ensureSingleLine(Tokens $tokens, int $index): bool
    {
        $previousToken = $tokens[$index - 1];

        if ($previousToken->isComment() && !str_starts_with($previousToken->getContent(), '/*')) {
            return false;
        }

        $content = Preg::replace('/\R\h*/', '', $tokens[$index]->getContent());

        $tokens->ensureWhitespaceAtIndex($index, 0, $content);

        return true;
    }

    private function ensureFunctionFullyMultiline(Tokens $tokens, int $startFunctionIndex): void
    {
        // find out what the indentation is
        $searchIndex = $startFunctionIndex;
        do {
            $prevWhitespaceTokenIndex = $tokens->getPrevTokenOfKind(
                $searchIndex,
                [[T_WHITESPACE]]
            );

            $searchIndex = $prevWhitespaceTokenIndex;
        } while (null !== $prevWhitespaceTokenIndex
            && !str_contains($tokens[$prevWhitespaceTokenIndex]->getContent(), "\n")
        );

        if (null === $prevWhitespaceTokenIndex) {
            $existingIndentation = '';
        } else {
            $existingIndentation = $tokens[$prevWhitespaceTokenIndex]->getContent();
            $lastLineIndex = strrpos($existingIndentation, "\n");
            $existingIndentation = false === $lastLineIndex
                ? $existingIndentation
                : substr($existingIndentation, $lastLineIndex + 1)
            ;
        }

        $indentation = $existingIndentation.$this->whitespacesConfig->getIndent();
        $endFunctionIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);

        $wasWhitespaceBeforeEndFunctionAddedAsNewToken = $tokens->ensureWhitespaceAtIndex(
            $tokens[$endFunctionIndex - 1]->isWhitespace() ? $endFunctionIndex - 1 : $endFunctionIndex,
            0,
            $this->whitespacesConfig->getLineEnding().$existingIndentation
        );

        if ($wasWhitespaceBeforeEndFunctionAddedAsNewToken) {
            ++$endFunctionIndex;
        }

        for ($index = $endFunctionIndex - 1; $index > $startFunctionIndex; --$index) {
            $token = $tokens[$index];

            // skip nested method calls and arrays
            if ($token->equals(')')) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

                continue;
            }

            // skip nested arrays
            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);

                continue;
            }

            if ($token->equals('}')) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            if ($token->equals(',') && !$tokens[$tokens->getNextMeaningfulToken($index)]->equals(')')) {
                $this->fixNewline($tokens, $index, $indentation);
            }
        }

        $this->fixNewline($tokens, $startFunctionIndex, $indentation, false);
    }

    /**
     * Method to insert newline after comma or opening parenthesis.
     *
     * @param int    $index       index of a comma
     * @param string $indentation the indentation that should be used
     * @param bool   $override    whether to override the existing character or not
     */
    private function fixNewline(Tokens $tokens, int $index, string $indentation, bool $override = true): void
    {
        if ($tokens[$index + 1]->isComment()) {
            return;
        }

        if ($tokens[$index + 2]->isComment()) {
            $nextMeaningfulTokenIndex = $tokens->getNextMeaningfulToken($index + 2);
            if (!$this->isNewline($tokens[$nextMeaningfulTokenIndex - 1])) {
                $tokens->ensureWhitespaceAtIndex($nextMeaningfulTokenIndex, 0, $this->whitespacesConfig->getLineEnding().$indentation);
            }

            return;
        }

        $nextMeaningfulTokenIndex = $tokens->getNextMeaningfulToken($index);

        if ($tokens[$nextMeaningfulTokenIndex]->equals(')')) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($index + 1, 0, $this->whitespacesConfig->getLineEnding().$indentation);
    }

    /**
     * Method to insert space after comma and remove space before comma.
     */
    private function fixSpace(Tokens $tokens, int $index): void
    {
        // remove space before comma if exist
        if ($tokens[$index - 1]->isWhitespace()) {
            $prevIndex = $tokens->getPrevNonWhitespace($index - 1);

            if (
                !$tokens[$prevIndex]->equals(',') && !$tokens[$prevIndex]->isComment()
                && (true === $this->configuration['after_heredoc'] || !$tokens[$prevIndex]->isGivenKind(T_END_HEREDOC))
            ) {
                $tokens->clearAt($index - 1);
            }
        }

        $nextIndex = $index + 1;
        $nextToken = $tokens[$nextIndex];

        // Two cases for fix space after comma (exclude multiline comments)
        //  1) multiple spaces after comma
        //  2) no space after comma
        if ($nextToken->isWhitespace()) {
            $newContent = $nextToken->getContent();

            if ('ensure_single_line' === $this->configuration['on_multiline']) {
                $newContent = Preg::replace('/\R/', '', $newContent);
            }

            if (
                (false === $this->configuration['keep_multiple_spaces_after_comma'] || Preg::match('/\R/', $newContent))
                && !$this->isCommentLastLineToken($tokens, $index + 2)
            ) {
                $newContent = ltrim($newContent, " \t");
            }

            $tokens[$nextIndex] = new Token([T_WHITESPACE, '' === $newContent ? ' ' : $newContent]);

            return;
        }

        if (!$this->isCommentLastLineToken($tokens, $index + 1)) {
            $tokens->insertAt($index + 1, new Token([T_WHITESPACE, ' ']));
        }
    }

    /**
     * Check if last item of current line is a comment.
     *
     * @param Tokens $tokens tokens to handle
     * @param int    $index  index of token
     */
    private function isCommentLastLineToken(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->isComment() || !$tokens[$index + 1]->isWhitespace()) {
            return false;
        }

        $content = $tokens[$index + 1]->getContent();

        return $content !== ltrim($content, "\r\n");
    }

    /**
     * Checks if token is new line.
     */
    private function isNewline(Token $token): bool
    {
        return $token->isWhitespace() && str_contains($token->getContent(), "\n");
    }
}
