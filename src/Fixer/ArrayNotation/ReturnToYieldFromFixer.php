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
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
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
            'When function return type is iterable and it starts with `return` then it must be changed to `yield from`.',
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
        $functionsAnalyzer = new FunctionsAnalyzer();

        foreach ($tokens->findGivenKind(T_FUNCTION) as $index => $token) {
            $returnType = $functionsAnalyzer->getFunctionReturnType($tokens, $index);
            if (null === $returnType || 'iterable' !== strtolower($returnType->getName())) {
                continue;
            }

            $functionBodyStartIndex = $tokens->getNextTokenOfKind($index, ['{', ';']);
            if (!$tokens[$functionBodyStartIndex]->equals('{')) {
                continue;
            }

            $returnIndex = $tokens->getNextMeaningfulToken($functionBodyStartIndex);

            if (!$tokens[$returnIndex]->isGivenKind(T_RETURN)) {
                continue;
            }

            $tokens[$returnIndex] = new Token([T_YIELD_FROM, 'yield from']);
        }
    }
}
