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
 * @author Bram Gotink <bram@gotink.me>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoLeadingNamespaceWhitespaceFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
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
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $beforeNamespace = $tokens[$index - 1];

            if (!$beforeNamespace->isWhitespace()) {
                if (!self::endsWithWhitespace($beforeNamespace->getContent())) {
                    $tokens->insertAt($index, new Token(array(T_WHITESPACE, $this->whitespacesConfig->getLineEnding())));
                }

                continue;
            }

            $lastNewline = strrpos($beforeNamespace->getContent(), "\n");

            if (false === $lastNewline) {
                $beforeBeforeNamespace = $tokens[$index - 2];

                if (self::endsWithWhitespace($beforeBeforeNamespace->getContent())) {
                    $beforeNamespace->clear();
                } else {
                    $beforeNamespace->setContent(' ');
                }
            } else {
                $beforeNamespace->setContent(substr($beforeNamespace->getContent(), 0, $lastNewline + 1));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The namespace declaration line shouldn\'t contain leading whitespace.',
            array(
                new CodeSample(
                    '<?php
 namespace Test8a;
    namespace Test8b;'
                ),
            )
        );
    }

    private static function endsWithWhitespace($str)
    {
        if ('' === $str) {
            return false;
        }

        return '' === trim(substr($str, -1));
    }
}
