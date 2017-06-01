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

namespace PhpCsFixer\Fixer\Semicolon;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class NoMultilineWhitespaceBeforeSemicolonsFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(';');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Multi-line whitespace before closing semicolon are prohibited.',
            array(
                new CodeSample(
                    '<?php
function foo () {
    return 1 + 2
        ;
}
'
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        foreach ($tokens as $index => $token) {
            if (!$token->equals(';')) {
                continue;
            }

            $previousIndex = $index - 1;
            $previous = $tokens[$previousIndex];
            if (!$previous->isWhitespace() || false === strpos($previous->getContent(), "\n")) {
                continue;
            }

            $content = $previous->getContent();
            if (("\n" === $content[0] || "\r" === $content[0]) && $tokens[$index - 2]->isComment()) {
                $tokens[$previousIndex] = new Token(array($previous->getId(), $lineEnding));
            } else {
                $tokens->clearAt($previousIndex);
            }
        }
    }
}
