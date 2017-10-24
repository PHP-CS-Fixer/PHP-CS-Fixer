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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jack Cherng <jfcherng@gmail.com>
 */
final class FunctionCompactNullableTypehintFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Remove extra spaces in a nullable typehint.',
            [new CodeSample("<?php\nfunction sample(? string \$str): ? string\n{}")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $startParenthesisIndex = $tokens->getNextTokenOfKind($index, ['(', ';', [T_CLOSE_TAG]]);
            if (!$tokens[$startParenthesisIndex]->equals('(')) {
                continue;
            }

            $startSquareBracketIndex = $tokens->getNextTokenOfKind($startParenthesisIndex, ['{', ';', [T_CLOSE_TAG]]);
            if (!$tokens[$startSquareBracketIndex]->equalsAny(['{', ';'])) {
                continue;
            }

            for ($iter = $startSquareBracketIndex - 1; $iter > $startParenthesisIndex; --$iter) {
                if (!$tokens[$iter]->isGivenKind(CT::T_NULLABLE_TYPE)) {
                    continue;
                }

                $tokens->removeTrailingWhitespace($iter);
            }
        }
    }
}
