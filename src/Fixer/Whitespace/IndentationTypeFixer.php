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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.4.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class IndentationTypeFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * @var string
     */
    private $indent;

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Code MUST use configured indentation type.',
            [
                new CodeSample("<?php\n\nif (true) {\n\techo 'Hello!';\n}\n"),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 50;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_COMMENT, T_DOC_COMMENT, T_WHITESPACE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->indent = $this->whitespacesConfig->getIndent();

        foreach ($tokens as $index => $token) {
            if ($token->isComment()) {
                $tokens[$index] = $this->fixIndentInComment($tokens, $index);

                continue;
            }

            if ($token->isWhitespace()) {
                $tokens[$index] = $this->fixIndentToken($tokens, $index);

                continue;
            }
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return Token
     */
    private function fixIndentInComment(Tokens $tokens, $index)
    {
        $content = preg_replace('/^(?:(?<! ) {1,3})?\t/m', '\1    ', $tokens[$index]->getContent(), -1, $count);

        // Also check for more tabs.
        while (0 !== $count) {
            $content = preg_replace('/^(\ +)?\t/m', '\1    ', $content, -1, $count);
        }

        $indent = $this->indent;

        // change indent to expected one
        $content = preg_replace_callback('/^(?:    )+/m', function ($matches) use ($indent) {
            return str_replace('    ', $indent, $matches[0]);
        }, $content);

        return new Token([$tokens[$index]->getId(), $content]);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return Token
     */
    private function fixIndentToken(Tokens $tokens, $index)
    {
        $content = $tokens[$index]->getContent();
        $previousTokenHasTrailingLinebreak = false;

        // @TODO 3.0 this can be removed when we have a transformer for "T_OPEN_TAG" to "T_OPEN_TAG + T_WHITESPACE"
        if (false !== strpos($tokens[$index - 1]->getContent(), "\n")) {
            $content = "\n".$content;
            $previousTokenHasTrailingLinebreak = true;
        }

        $indent = $this->indent;
        $newContent = preg_replace_callback(
            '/(\R)(\h+)/', // find indent
            function (array $matches) use ($indent) {
                // normalize mixed indent
                $content = preg_replace('/(?:(?<! ) {1,3})?\t/', '    ', $matches[2]);

                // change indent to expected one
                return $matches[1].str_replace('    ', $indent, $content);
            },
            $content
        );

        if ($previousTokenHasTrailingLinebreak) {
            $newContent = substr($newContent, 1);
        }

        return new Token([T_WHITESPACE, $newContent]);
    }
}
