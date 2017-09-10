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
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Code MUST use configured indentation type.',
            array(
                new CodeSample("<?php\n\nif (true) {\n\techo 'Hello!';\n}"),
            )
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
        return $tokens->isAnyTokenKindsFound(array(T_COMMENT, T_DOC_COMMENT, T_WHITESPACE));
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $expectedIndent = $this->whitespacesConfig->getIndent();

        foreach ($tokens as $index => $token) {
            if ($token->isComment()) {
                $content = preg_replace('/^(?:(?<! ) {1,3})?\t/m', '\1    ', $token->getContent(), -1, $count);

                // Also check for more tabs.
                while (0 !== $count) {
                    $content = preg_replace('/^(\ +)?\t/m', '\1    ', $content, -1, $count);
                }

                // change indent to expected one
                $content = preg_replace_callback('/^(?:    )+/m', function ($matches) use ($expectedIndent) {
                    return str_replace('    ', $expectedIndent, $matches[0]);
                }, $content);

                $tokens[$index] = new Token(array($token->getId(), $content));

                continue;
            }

            if ($token->isWhitespace()) {
                // normalize mixed indent
                $content = preg_replace('/(?:(?<! ) {1,3})?\t/', '    ', $token->getContent());

                // change indent to expected one
                $content = str_replace('    ', $this->whitespacesConfig->getIndent(), $content);

                $tokens[$index] = new Token(array(T_WHITESPACE, $content));
            }
        }
    }
}
