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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * If using named arguments, there MUST NOT be a space between the argument name and colon, and there MUST be a single space between the colon and the argument value.
 *
 * @see https://github.com/php-fig/per-coding-style/blob/2.0.0/spec.md
 */
final class NamedArgumentSpaceFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_00_00 && $tokens->isTokenKindFound(CT::T_NAMED_ARGUMENT_NAME);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There MUST NOT be a space between the argument name and colon, and there MUST be a single space between the colon and the argument value.',
            [
                new VersionSpecificCodeSample(
                    "<?php\nfoo(foo  :1);\n",
                    new VersionSpecification(8_00_00),
                ),
            ],
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(CT::T_NAMED_ARGUMENT_NAME)) {
                continue;
            }

            $afterColonIndex = $tokens->getNextTokenOfKind($index, [[CT::T_NAMED_ARGUMENT_COLON]]) + 1;

            if ($tokens[$afterColonIndex]->isWhitespace()) {
                $tokens[$afterColonIndex] = new Token([T_WHITESPACE, ' ']);
            } elseif (!$tokens[$afterColonIndex]->isWhitespace()) {
                $tokens->insertAt($afterColonIndex, new Token([T_WHITESPACE, ' ']));
            }

            if ($tokens[$index + 1]->isWhitespace()) {
                $tokens->clearAt($index + 1);
            }
        }
    }
}
