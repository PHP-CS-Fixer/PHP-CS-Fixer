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
final class MethodArgumentSpaceFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
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
            'In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma.',
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

            if ($token->equals('(') && !$tokens[$index - 1]->isGivenKind(T_ARRAY)) {
                $this->fixFunction($tokens, $index);
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
        ]);
    }

    /**
     * Fix arguments spacing for given function.
     *
     * @param Tokens $tokens             Tokens to handle
     * @param int    $startFunctionIndex Start parenthesis position
     */
    private function fixFunction(Tokens $tokens, $startFunctionIndex)
    {
        $endFunctionIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);

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

            if ($token->equals(',')) {
                $this->fixSpace2($tokens, $index);
            }
        }
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
            if ($this->configuration['keep_multiple_spaces_after_comma'] || $this->isCommentLastLineToken($tokens, $index + 2)) {
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
}
