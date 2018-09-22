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

namespace PhpCsFixer;

use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @author SpacePossum
 */
abstract class AbstractFopenFlagFixer extends AbstractFunctionReferenceFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([T_STRING, T_CONSTANT_ENCAPSED_STRING]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $argumentsAnalyzer = new ArgumentsAnalyzer();

        $index = 0;
        $end = $tokens->count() - 1;
        while (true) {
            $candidate = $this->find('fopen', $tokens, $index, $end);

            if (null === $candidate) {
                break;
            }

            $index = $candidate[1]; // proceed to '(' of `fopen`

            // fetch arguments
            $arguments = $argumentsAnalyzer->getArguments(
                $tokens,
                $index,
                $candidate[2]
            );

            $argumentsCount = \count($arguments); // argument count sanity check

            if ($argumentsCount < 2 || $argumentsCount > 4) {
                continue;
            }

            $argumentStartIndex = array_keys($arguments)[1]; // get second argument index

            $this->fixFopenFlagToken(
                $tokens,
                $argumentStartIndex,
                $arguments[$argumentStartIndex]
            );
        }
    }

    abstract protected function fixFopenFlagToken(Tokens $tokens, $argumentStartIndex, $argumentEndIndex);
}
