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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixes the line endings in multi-line strings.
 *
 * @author Ilija Tovilo <ilija.tovilo@me.com>
 */
final class StringLineEndingFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'All multi-line strings must use same line ending.',
            [
                new CodeSample(
                    "<?php \$a = 'my\r\nmulti\nline\r\nstring';\r\n"
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $ending = $this->whitespacesConfig->getLineEnding();

        for ($index = 0, $count = count($tokens); $index < $count; ++$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind([T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE])) {
                $tokens[$index] = new Token([
                    $token->getId(),
                    preg_replace(
                        "#\r\n|\n#",
                        $ending,
                        $token->getContent()
                    ),
                ]);
            }
        }
    }
}
