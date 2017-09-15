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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶4.4, ¶4.6.
 *
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 */
final class MethodArgumentSpaceFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * Method to insert space after comma and remove space before comma.
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    public function fixSpace(Tokens $tokens, $index)
    {
        @trigger_error(__METHOD__.' is deprecated and will be removed in 3.0.', E_USER_DEPRECATED);
        $this->fixSpace2($tokens, $index);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.',
            [
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);",
                    null
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);",
                    ['keep_multiple_spaces_after_comma' => false]
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\$b=20,\$c=30) {}\nsample(1,  2);",
                    ['keep_multiple_spaces_after_comma' => true]
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,\n    2);",
                    ['ensure_fully_multiline' => true]
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,  \n    2);\nsample('foo',    'foobarbaz', 'baz');\nsample('foobar', 'bar',       'baz');",
                    [
                        'ensure_fully_multiline' => true,
                        'keep_multiple_spaces_after_comma' => true,
                    ]
                ),
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\n    \$b=20,\$c=30) {}\nsample(1,  \n    2);\nsample('foo',    'foobarbaz', 'baz');\nsample('foobar', 'bar',       'baz');",
                    [
                        'ensure_fully_multiline' => true,
                        'keep_multiple_spaces_after_comma' => false,
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            if ($token->equals('(')) {
                $meaningfulTokenBeforeParenthesis = $tokens[$tokens->getPrevMeaningfulToken($index)];
                if (!$meaningfulTokenBeforeParenthesis->isKeyword()
                    || $meaningfulTokenBeforeParenthesis->isGivenKind([T_LIST, T_FUNCTION])) {
                    if ($this->fixFunction($tokens, $index) && $this->configuration['ensure_fully_multiline']) {
                        if (!$meaningfulTokenBeforeParenthesis->isGivenKind(T_LIST)) {
                            $this->ensureFunctionFullyMultiline($tokens, $index);
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('keep_multiple_spaces_after_comma', 'Whether keep multiple spaces after comma.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder(
                'ensure_fully_multiline',
                'Ensure every argument of a multiline argument list is on its own line'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false) // @TODO should be true at 3.0
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
    private function fixFunction(Tokens $tokens, $startFunctionIndex)
    {
        $endFunctionIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);
        $isMultiline = $this->isNewline($tokens[$startFunctionIndex + 1])
            || $this->isNewline($tokens[$endFunctionIndex - 1]);

        for ($index = $endFunctionIndex - 1; $index > $startFunctionIndex; --$index) {
            $token = $tokens[$index];

            if ($token->equals(')')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index, false);

                continue;
            }

            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index, false);

                continue;
            }

            if ($token->equals('}')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index, false);

                continue;
            }

            if ($token->equals(',')) {
                $this->fixSpace2($tokens, $index);
                if (!$isMultiline && $this->isNewline($tokens[$index + 1])) {
                    $isMultiline = true;

                    break;
                }
            }
        }

        return $isMultiline;
    }

    private function ensureFunctionFullyMultiline(Tokens $tokens, $startFunctionIndex)
    {
        // find out what the indentation is
        $searchIndex = $startFunctionIndex;
        do {
            $prevWhitespaceTokenIndex = $tokens->getPrevTokenOfKind(
                $searchIndex,
                [[T_WHITESPACE]]
            );
            $searchIndex = $prevWhitespaceTokenIndex;
        } while ($prevWhitespaceTokenIndex
            && false === strpos($tokens[$prevWhitespaceTokenIndex]->getContent(), "\n")
        );
        $existingIndentation = $prevWhitespaceTokenIndex
            ? ltrim($tokens[$prevWhitespaceTokenIndex]->getContent(), "\n\r")
            : '';

        $indentation = $existingIndentation.$this->whitespacesConfig->getIndent();
        $endFunctionIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);
        if (!$this->isNewline($tokens[$endFunctionIndex - 1])) {
            $this->addNewlineAndIndent(
                $tokens,
                $endFunctionIndex,
                $existingIndentation,
                false
            );
            ++$endFunctionIndex;
        }

        for ($index = $endFunctionIndex - 1; $index > $startFunctionIndex; --$index) {
            $token = $tokens[$index];

            // skip nested method calls and arrays
            if ($token->equals(')')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index, false);

                continue;
            }

            // skip nested arrays
            if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index, false);

                continue;
            }

            if ($token->equals('}')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index, false);

                continue;
            }

            if ($token->equals(',')) {
                $this->fixNewline($tokens, $index, $indentation);
            }
        }
        $this->fixNewLine($tokens, $startFunctionIndex, $indentation, false);
    }

    /**
     * Method to insert newline after comma or opening parenthesis.
     *
     * @param Tokens $tokens
     * @param int    $index       index of a comma
     * @param string $indentation the indentation that should be used
     * @param bool   $override    whether to override the existing character or not
     */
    private function fixNewline(Tokens $tokens, $index, $indentation, $override = true)
    {
        if ($this->isNewline($tokens[$index + 1]) || $tokens[$index + 1]->isComment()) {
            return;
        }
        if ($tokens[$index + 2]->isComment()) {
            $nextMeaningfulTokenIndex = $tokens->getNextMeaningfulToken($index + 2);
            if (!$this->isNewLine($tokens[$nextMeaningfulTokenIndex - 1])) {
                $this->addNewlineAndIndent(
                    $tokens,
                    $nextMeaningfulTokenIndex,
                    $indentation,
                    false
                );
            }

            return;
        }

        $this->addNewlineAndIndent($tokens, $index + 1, $indentation, $override);
    }

    /**
     * Makes sure there is a whitespace at the given location.
     *
     * @param Tokens $tokens      The token stream to modify
     * @param int    $index       where to insert the whitespace
     * @param string $indentation the indentation that should be used
     * @param bool   $override    whether to override the existing character or not
     */
    private function addNewlineAndIndent(Tokens $tokens, $index, $indentation, $override)
    {
        $whitespaceToken = new Token([
            T_WHITESPACE,
            $this->whitespacesConfig->getLineEnding().$indentation,
        ]);

        if ($override) {
            $tokens[$index] = $whitespaceToken;

            return;
        }

        $tokens->insertAt($index, $whitespaceToken);
    }

    /**
     * Method to insert space after comma and remove space before comma.
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixSpace2(Tokens $tokens, $index)
    {
        // remove space before comma if exist
        if ($tokens[$index - 1]->isWhitespace()) {
            $prevIndex = $tokens->getPrevNonWhitespace($index - 1);

            if (!$tokens[$prevIndex]->equalsAny([',', [T_END_HEREDOC]]) && !$tokens[$prevIndex]->isComment()) {
                $tokens->clearAt($index - 1);
            }
        }

        $nextIndex = $index + 1;
        $nextToken = $tokens[$nextIndex];

        // Two cases for fix space after comma (exclude multiline comments)
        //  1) multiple spaces after comma
        //  2) no space after comma
        if ($nextToken->isWhitespace()) {
            if (
                ($this->configuration['keep_multiple_spaces_after_comma'] && !preg_match('/\R/', $nextToken->getContent()))
                || $this->isCommentLastLineToken($tokens, $index + 2)
            ) {
                return;
            }

            $newContent = ltrim($nextToken->getContent(), " \t");
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
     *
     * @return bool
     */
    private function isCommentLastLineToken(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isComment() || !$tokens[$index + 1]->isWhitespace()) {
            return false;
        }

        $content = $tokens[$index + 1]->getContent();

        return $content !== ltrim($content, "\r\n");
    }

    /**
     * Checks if token is new line.
     *
     * @param Token $token
     *
     * @return bool
     */
    private function isNewLine(Token $token)
    {
        return $token->isWhitespace() && false !== strpos($token->getContent(), "\n");
    }
}
