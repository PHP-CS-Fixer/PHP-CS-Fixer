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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gabriel Caruso <carusogabriel34@gmail.com>
 */
final class TernaryToElvisOperatorFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Converts a ternary operation with first and second operands identicals to an elvis operation (?:).',
            [
                new CodeSample(
                    "<?php \$foo = \$bar ? \$bar : baz;\n"
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(['?', ':']);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->equals('?')) {
                continue;
            }

            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];

            $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
            $nextToken = $tokens[$nextTokenIndex];

            if (!$prevToken->equals($nextToken)) {
                continue;
            }

            // previous and before content of ternary operation are identical, continue...
            $tokens->clearTokenAndMergeSurroundingWhitespace($nextTokenIndex);
        }
    }
}
