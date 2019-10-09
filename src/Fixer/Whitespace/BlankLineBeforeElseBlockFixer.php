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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class BlankLineBeforeElseBlockFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'An empty line feed must precede any else or elseif codeblock.',
            [
                new CodeSample(
                    '<?php
if ($a) {
    foo();

} elseif ($b) {
    bar();

} else {
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
        return $tokens->isTokenKindFound(T_ELSEIF) || $tokens->isTokenKindFound(T_ELSE);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            /** @var Token $token */
            if ($token->isGivenKind([T_ELSE, T_ELSEIF])) {
                $index = $tokens->getPrevMeaningfulToken($index);

                if ($tokens[$index]->equals('}')) {
                    --$index;

                    while ($tokens[$index]->isGivenKind([T_COMMENT, T_DOC_COMMENT])) {
                        --$index;
                    }

                    /** @var Token $whitespace */
                    $whitespace = $tokens[$index];

                    /** @var int $presentNewlines */
                    $presentNewlines = substr_count($whitespace->getContent(), "\n");

                    if ($whitespace->isWhitespace() && $presentNewlines < 2) {
                        $tokens[$index] = $this->convertWhitespaceToken($whitespace);
                    }
                }
            }
        }
    }

    private function convertWhitespaceToken(Token $whitespace): Token
    {
        return new Token([
            $whitespace->getId(),
            substr_replace(
                $whitespace->getContent(),
                "\n\n",
                strpos($whitespace->getContent(), "\n"),
                1
            ),
        ]);
    }
}
