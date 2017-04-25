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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @author Ceeram <ceeram@cakephp.org>
 */
final class NoBlankLinesAfterClassOpeningFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There should be no empty lines after class opening brace.',
            [
                new CodeSample(
                    '<?php
final class Sample
{

    protected function foo()
    {
    }
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isClassy()) {
                continue;
            }

            $startBraceIndex = $tokens->getNextTokenOfKind($index, ['{']);
            if (!$tokens[$startBraceIndex + 1]->isWhitespace()) {
                continue;
            }

            $this->fixWhitespace($tokens[$startBraceIndex + 1]);
        }
    }

    /**
     * Cleanup a whitespace token.
     *
     * @param Token $token
     */
    private function fixWhitespace(Token $token)
    {
        $content = $token->getContent();
        // if there is more than one new line in the whitespace, then we need to fix it
        if (substr_count($content, "\n") > 1) {
            // the final bit of the whitespace must be the next statement's indentation
            $lines = Utils::splitLines($content);
            $token->setContent($this->whitespacesConfig->getLineEnding().end($lines));
        }
    }
}
