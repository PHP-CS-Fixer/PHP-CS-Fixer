<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class MultilineEndingArgumentWithCommaFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'In multiline method arguments and method call, there MUST be a trailing comma.',
            [
                new CodeSample(
                    "<?php\nfunction sample(\$a=10,\n\$b=20,\$c=30\n) {}\n",
                    null
                ),
            ],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     *
     * Must run after MethodArgumentSpaceFixer
     */
    public function getPriority(): int
    {
        return 29;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $expectedTokens = [T_LIST, T_FUNCTION, CT::T_USE_LAMBDA, T_FN, T_CLASS];

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            if (!$token->equals('(')) {
                continue;
            }

            $meaningfulTokenBeforeParenthesis = $tokens[$tokens->getPrevMeaningfulToken($index)];

            if (
                $meaningfulTokenBeforeParenthesis->isKeyword()
                && !$meaningfulTokenBeforeParenthesis->isGivenKind($expectedTokens)
            ) {
                continue;
            }
            $startFunctionIndex = $index;

            $endFunctionIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);

            // is the token before the parenthesis a newLine and the token before not a comma
            if (Preg::match('/\R/', $tokens[$endFunctionIndex-1]->getContent())
                && !($tokens[$endFunctionIndex-2]->equals(','))
            ) {
                $tokens->insertSlices([
                    $endFunctionIndex-1 => new Token(','),
                ]);
            }
        }
    }
}
