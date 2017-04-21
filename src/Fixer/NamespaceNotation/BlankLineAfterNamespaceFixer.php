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

namespace PhpCsFixer\Fixer\NamespaceNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BlankLineAfterNamespaceFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There MUST be one blank line after the namespace declaration.',
            [
                new CodeSample("<?php\nnamespace Sample\\Sample;\n\n\n\$a;"),
                new CodeSample("<?php\nnamespace Sample\\Sample;\nClass Test{}"),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the NoUnusedImportsFixer
        return -20;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_NAMESPACE);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $ending = $this->whitespacesConfig->getLineEnding();
        $lastIndex = $tokens->count() - 1;

        for ($index = $lastIndex; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $semicolonIndex = $tokens->getNextTokenOfKind($index, [';', '{', [T_CLOSE_TAG]]);
            $semicolonToken = $tokens[$semicolonIndex];

            if (!isset($tokens[$semicolonIndex + 1]) || !$semicolonToken->equals(';')) {
                continue;
            }

            $nextIndex = $semicolonIndex + 1;
            $nextToken = $tokens[$nextIndex];

            if (!$nextToken->isWhitespace()) {
                $tokens->insertAt($semicolonIndex + 1, new Token([T_WHITESPACE, $ending.$ending]));
            } else {
                $nextToken->setContent(
                    ($nextIndex === $lastIndex ? $ending : $ending.$ending).ltrim($nextToken->getContent())
                );
            }
        }
    }
}
