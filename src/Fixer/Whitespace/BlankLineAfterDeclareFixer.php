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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class BlankLineAfterDeclareFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There MUST be a blank line after a `declare()`.',
            [
                new CodeSample(
                    <<<'EOT'
                        <?php
                        declare(strict_types=1);echo "Foo";

                        EOT,
                ),
                new CodeSample(
                    <<<'EOT'
                        <?php
                        declare(strict_types=1); echo "Foo";

                        EOT,
                ),
                new CodeSample(
                    <<<'EOT'
                        <?php
                        declare(strict_types=1);
                        echo "Foo";

                        EOT,
                ),
                new CodeSample(
                    <<<'EOT'
                        <?php
                        declare(ticks=1) {
                            // Do stuff
                        }
                        echo "Foo";

                        EOT,
                ),
            ],
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after DeclareStrictTypesFixer, FullyQualifiedStrictTypesFixer.
     */
    public function getPriority(): int
    {
        return -40;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_DECLARE);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $newline = $this->whitespacesConfig->getLineEnding();
        $prototype = [\T_WHITESPACE, "{$newline}{$newline}"];

        for ($index = \count($tokens) - 7; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_DECLARE)) {
                continue;
            }

            $endIndex = $tokens->getNextTokenOfKind($index, [';', '{']);

            if ($tokens[$endIndex]->equals('{')) {
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $endIndex);
            }

            $whitespaceIndex = $endIndex + 1;
            $nextToken = $tokens[$whitespaceIndex] ?? null;

            if (null !== $nextToken && $nextToken->isWhitespace()) {
                if (!Preg::match('/\R\s*\R/', $nextToken->getContent())) {
                    // Case: whitespace but not at least 2 newline
                    $tokens[$whitespaceIndex] = new Token($prototype);
                }

                // Case: whitespace with at least 2 newlines
                continue;
            }

            // Case: no whitespace at all
            $tokens->insertSlices([$whitespaceIndex => [new Token($prototype)]]);
        }
    }
}
