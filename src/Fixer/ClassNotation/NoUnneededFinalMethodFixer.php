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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class NoUnneededFinalMethodFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'A final class must not have final methods.',
            [
                new CodeSample('<?php
final class Foo {
    final public function foo() {}
    final protected function bar() {}
    final private function baz() {}
}'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FINAL]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $end = count($tokens);
        for ($index = 0; $index < $end; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_CLASS)) {
                continue;
            }

            $classOpen = $tokens->getNextTokenOfKind($index, ['{']);
            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
            $index = $this->fixClass($tokens, $classOpen, $end, $prevToken->isGivenKind(T_FINAL));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $classOpenIndex
     * @param int    $end
     * @param bool   $isFinalClass
     *
     * @return int
     */
    private function fixClass(Tokens $tokens, $classOpenIndex, $end, $isFinalClass)
    {
        for ($index = $classOpenIndex + 1; $index < $end; ++$index) {
            // Class end
            if ($tokens[$index]->equals('}')) {
                return $index;
            }

            // Skip method content
            if ($tokens[$index]->equals('{')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            if ($isFinalClass && $tokens[$index]->isGivenKind(T_FINAL)) {
                $tokens->clearAt($index);

                $nextTokenIndex = $index + 1;
                if ($tokens[$nextTokenIndex]->isWhitespace()) {
                    $tokens->clearAt($nextTokenIndex);
                }
            }
        }
    }
}
