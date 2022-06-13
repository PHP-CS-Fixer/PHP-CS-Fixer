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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NoUselessNullsafeOperatorFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There should not be useless `null-safe-operators` `?->` used.',
            [
                new VersionSpecificCodeSample(
                    '<?php
class Foo extends Bar
{
    public function test() {
        echo $this?->parentMethod();
    }
}
',
                    new VersionSpecification(80000)
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 80000 && $tokens->isAllTokenKindsFound([T_VARIABLE, T_NULLSAFE_OBJECT_OPERATOR]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_NULLSAFE_OBJECT_OPERATOR)) {
                continue;
            }

            $nullsafeObjectOperatorIndex = $index;
            $index = $tokens->getPrevMeaningfulToken($index);

            if (!$tokens[$index]->isGivenKind(T_VARIABLE)) {
                continue;
            }

            if ('$this' !== strtolower($tokens[$index]->getContent())) {
                continue;
            }

            $tokens[$nullsafeObjectOperatorIndex] = new Token([T_OBJECT_OPERATOR, '->']);
        }
    }
}
