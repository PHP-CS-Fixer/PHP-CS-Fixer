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

namespace PhpCsFixer\Fixer\ReturnNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BlankLineBeforeReturnFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'An empty line feed should precede a return statement.',
            [new CodeSample("<?php\nfunction A()\n{\n    echo 1;\n    return 1;\n}")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after NoUselessReturnFixer
        return -19;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_RETURN);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_RETURN)) {
                continue;
            }

            $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

            if (!$prevNonWhitespaceToken->equalsAny([';', '}'])) {
                continue;
            }

            $prevIndex = $index - 1;
            $prevToken = $tokens[$prevIndex];

            if ($prevToken->isWhitespace()) {
                $parts = explode("\n", $prevToken->getContent());
                $countParts = count($parts);

                if (1 === $countParts) {
                    $tokens[$prevIndex] = new Token([T_WHITESPACE, rtrim($prevToken->getContent(), " \t").$lineEnding.$lineEnding]);
                } elseif (count($parts) <= 2) {
                    $tokens[$prevIndex] = new Token([T_WHITESPACE, $lineEnding.$prevToken->getContent()]);
                }
            } else {
                $tokens->insertAt($index, new Token([T_WHITESPACE, $lineEnding.$lineEnding]));

                ++$index;
                ++$limit;
            }
        }
    }
}
