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

final class BlankLineBeforeCatchBlockFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'An empty line feed must precede any catch or finally codeblock.',
            [
                new CodeSample(
                    '<?php
try {
    foo();

} catch (\Exception $b) {
    bar();

} finally {
    baz();
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should run after no_blank_lines_after_phpdoc
        return -26;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CATCH) || $tokens->isTokenKindFound(T_FINALLY);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        /** @var string $lineEnding */
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        foreach ($tokens as $index => $token) {
            /** @var Token $token */
            if ($token->isGivenKind([T_CATCH, T_FINALLY])) {
                $index = $tokens->getPrevMeaningfulToken($index);

                if ($tokens[$index]->equals('}')) {
                    --$index;

                    while ($tokens[$index]->isGivenKind([T_COMMENT, T_DOC_COMMENT])) {
                        --$index;
                    }

                    /** @var Token $whitespace */
                    $whitespace = $tokens[$index];

                    /** @var int $presentNewlines */
                    $presentNewlines = substr_count($whitespace->getContent(), $lineEnding);

                    if ($whitespace->isWhitespace() && $presentNewlines < 2) {
                        $tokens[$index] = $this->convertWhitespaceToken($whitespace);
                    }
                }
            }
        }
    }

    private function convertWhitespaceToken(Token $whitespace): Token
    {
        /** @var string $lineEnding */
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        return new Token([
            T_WHITESPACE,
            substr_replace(
                $whitespace->getContent(),
                $lineEnding.$lineEnding,
                strpos($whitespace->getContent(), $lineEnding),
                1
            ),
        ]);
    }
}
