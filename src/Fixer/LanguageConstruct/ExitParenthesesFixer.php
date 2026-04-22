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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ExitParenthesesFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Language constructs `exit` and `die` must be called with parentheses.',
            [
                new CodeSample(
                    <<<'PHP'
                        <?php
                        exit;
                        die;

                        PHP,
                ),
            ],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_EXIT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $slices = [];

        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            if (!$tokens[$index]->isGivenKind(\T_EXIT)) {
                continue;
            }

            $prevIndex = $tokens->getPrevMeaningfulToken($index);

            if (null !== $prevIndex && $tokens[$prevIndex]->isGivenKind([\T_DOUBLE_COLON, \T_CASE, \T_CONST])) {
                continue;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($index);

            if (null !== $nextIndex && $tokens[$nextIndex]->equals('(')) {
                continue;
            }

            $slices[$index + 1] = [new Token('('), new Token(')')];
        }

        if ([] !== $slices) {
            $tokens->insertSlices($slices);
        }
    }
}
