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

namespace PhpCsFixer\Fixer\Internal;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\InternalFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class KeysFixer extends AbstractFixer implements InternalFixerInterface, WhitespacesAwareFixerInterface
{
    public function getName(): string
    {
        return 'PhpCsFixerInternal/'.parent::getName();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Array and yield keys when strung must be trimmed and have no double spaces.',
            [
                new CodeSample(
                    <<<'PHP'
                        <?php return [
                             ' foo' => 1,
                             'bar ' => 1,
                             'foo  bar' => 1,
                        ];

                        PHP
                ),
            ],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_ARRAY, T_YIELD, CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_YIELD)) {
                $indexToFix = $tokens->getNextMeaningfulToken($index);
            } elseif ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $indexToFix = $tokens->getPrevMeaningfulToken($index);
            } else {
                continue;
            }

            if (!$tokens[$indexToFix]->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            $content = $tokens[$indexToFix]->getContent();
            $stringBorderQuote = $content[0];
            $innerContent = substr($content, 1, -1);

            $newInnerContent = Preg::replace('/\s{2,}/', ' ', $innerContent);

            $prevIndex = $tokens->getPrevMeaningfulToken($indexToFix);
            if (!$tokens[$prevIndex]->equals('.')) {
                $newInnerContent = ltrim($newInnerContent);
            }

            $nextIndex = $tokens->getNextMeaningfulToken($indexToFix);
            if (!$tokens[$nextIndex]->equals('.')) {
                $newInnerContent = rtrim($newInnerContent);
            }

            if ('' === $newInnerContent) {
                continue;
            }

            $newContent = $stringBorderQuote.$newInnerContent.$stringBorderQuote;

            if ($content === $newContent) {
                continue;
            }

            $tokens[$indexToFix] = new Token([T_CONSTANT_ENCAPSED_STRING, $newContent]);
        }
    }
}
