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
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
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
            [
                new VersionSpecificCodeSample(
                    "<?php\nfunction sample(? string \$str): string\n{}",
                    new VersionSpecification(70000)
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction sample(?string \$str): ? string\n{}",
                    new VersionSpecification(70100)
                ),
            ],
            'Rule is applied only in a PHP 7+ environment.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return
            PHP_VERSION_ID >= 70000 &&
            $tokens->isAllTokenKindsFound([T_FUNCTION, CT::T_NULLABLE_TYPE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        static $typehintKinds = [
            CT::T_ARRAY_TYPEHINT,
            T_CALLABLE,
            T_NS_SEPARATOR,
            T_STRING,
        ];

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $startParenthesisIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $startSquareBracketIndex = $tokens->getNextTokenOfKind($startParenthesisIndex, ['{', ';']);

            for ($iter = $startSquareBracketIndex - 1; $iter > $startParenthesisIndex; --$iter) {
                if (!$tokens[$iter]->isGivenKind(CT::T_NULLABLE_TYPE)) {
                    continue;
                }

                // remove whitespaces only if there are only whitespaces
                // between '?' and the variable type
                if (
                    !(
                        $tokens[$iter + 1]->isWhitespace() &&
                        $tokens[$iter + 2]->isGivenKind($typehintKinds)
                    )
                ) {
                    continue;
                }

                $tokens->removeTrailingWhitespace($iter);
            }
        }
    }
}
