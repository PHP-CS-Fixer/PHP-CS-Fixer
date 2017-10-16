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

namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class ExplicitEscapeFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Escape implicit backslashes.',
            [new CodeSample('<?php $a = \'My\\Prefix\\\\\';')]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            $content = $token->getContent();
            if (!$token->isGivenKind([T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING]) || false === strpos($content, '\\')) {
                continue;
            }

            // Single-quoted strings
            if ($token->isGivenKind(T_CONSTANT_ENCAPSED_STRING) && '\'' === $content[0]) {
                $newContent = preg_replace('/(?<!\\\\)\\\\(?![\\\\\\\'])/', '\\\\\\\\', $content);
                if ($newContent !== $content) {
                    $tokens[$index] = new Token([T_CONSTANT_ENCAPSED_STRING, $newContent]);
                }
                continue;
            }

            // Nowdoc syntax
            if ($token->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                $prevTokenContent = rtrim($tokens[$index - 1]->getContent());
                if ('\'' === substr($prevTokenContent, -1)) {
                    continue;
                }
            }

            // Double-quoted strings and Heredoc syntax
            $newContent = preg_replace('/(?<!\\\\)\\\\(?!([efnrtv$"\\\\0-7]|x[0-9A-Fa-f]|u{))/', '\\\\\\\\', $content);
            if ($newContent !== $content) {
                $tokens[$index] = new Token([$token->getId(), $newContent]);
            }
        }
    }
}
