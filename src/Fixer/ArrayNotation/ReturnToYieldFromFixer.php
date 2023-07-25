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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class ReturnToYieldFromFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'When function return type is `iterable`, and it returns an array explicitly, then it must be changed to `yield from`.',
            [new CodeSample('<?php function giveMeData(): iterable {
    return [1, 2, 3];
}
')],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before YieldFromArrayToYieldsFixer.
     * Must run after PhpUnitDataProviderReturnTypeFixer, PhpdocToReturnTypeFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens->findGivenKind(T_RETURN) as $index => $token) {
            if (!$this->shouldBeFixed($tokens, $index)) {
                continue;
            }

            $tokens[$index] = new Token([T_YIELD_FROM, 'yield from']);
        }
    }

    private function shouldBeFixed(Tokens $tokens, int $returnIndex): bool
    {
        $nextIndex = $tokens->getNextMeaningfulToken($returnIndex);
        if (!$tokens[$nextIndex]->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
            return false;
        }

        $beforeReturnIndex = $tokens->getPrevMeaningfulToken($returnIndex);
        if (!$tokens[$beforeReturnIndex]->equals('{')) {
            return false;
        }

        $returnTypeIndex = $tokens->getPrevMeaningfulToken($beforeReturnIndex);
        if (!$tokens[$returnTypeIndex]->isGivenKind(T_STRING)) {
            return false;
        }

        return 'iterable' === strtolower($tokens[$returnTypeIndex]->getContent());
    }
}
