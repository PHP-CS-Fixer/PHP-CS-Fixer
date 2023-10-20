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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\ControlCaseStructuresAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class MatchHasOneCaseOnEveryArmFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 80000 && $tokens->isTokenKindFound(T_MATCH);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Match only allow has one case on every arm.',
            [
                new CodeSample(
                    '<?php
                return   match ($bar) {
                    2 => "c",
                    3,4,5 =>"e",
                    default => "d"
                };
'
                ),
            ]
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        /** @var \PhpCsFixer\Tokenizer\Analyzer\Analysis\MatchAnalysis $analysis */
        foreach (ControlCaseStructuresAnalyzer::findControlStructures($tokens, [T_MATCH]) as $analysis) {
            for ($index = $analysis->getOpenIndex() + 1; $index <= $analysis->getCloseIndex(); ++$index) {
                if (str_contains($tokens[$index]->getContent(), PHP_EOL)) {
                    continue;
                }

                if (str_contains($tokens[$index]->getContent(), ',') && !str_contains($tokens[$index + 1]->getContent(), PHP_EOL)) {
                    $tokens->insertSlices([$index + 1 => new Token([T_WHITESPACE, $tokens[$analysis->getOpenIndex() + 1]->getContent()])]);
                }
            }
        }
    }
}
