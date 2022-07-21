<?php

declare(strict_types=1);

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
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class BlankLinesInsideBlockFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There must not be blank lines at start and end of braces blocks.',
            [
                new CodeSample(
                    '<?php
class Foo {

    public function foo() {

        if ($baz == true) {

            echo "foo";

        }

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
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound('{');
    }

    /**
     * {@inheritdoc}
     *
     * Must run after ControlStructureBracesFixer.
     */
    public function getPriority(): int
    {
        return parent::getPriority();
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isWhitespace()) {
                continue;
            }

            if (
                !$tokens[$index - 1]->equals('{')
                && (!isset($tokens[$index + 1]) || !$tokens[$index + 1]->equals('}'))
            ) {
                continue;
            }

            $content = Preg::replace('/^.*?(\R\h*)$/Ds', '$1', $token->getContent());

            $tokens[$index] = new Token([$token->getId(), $content]);
        }
    }
}
