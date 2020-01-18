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
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SimpleLambdaCallFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Calling lambdas without using `call_user_func*`, when possible.',
            [
                new CodeSample(
                    '<?php
    call_user_func("var_dump", 1, 2);

    call_user_func("Bar\Baz::d", 1, 2);

    call_user_func_array($callback, [1, 2]);
'
                ),
                new VersionSpecificCodeSample(
                    '<?php
call_user_func(function ($a, $b) { var_dump($a, $b); }, 1, 2);

call_user_func(static function ($a, $b) { var_dump($a, $b); }, 1, 2);
',
                    new VersionSpecification(70000)
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            /** @var Token $token */
            $token = $tokens[$index];

            if ($token->equalsAny([[T_STRING, 'call_user_func'], [T_STRING, 'call_user_func_array']], false)) {
                $this->processCall($tokens, $index);
            }
        }
    }

    /**
     * @param int $index
     */
    private function processCall(Tokens $tokens, $index)
    {
        $firstArgIndex = $tokens->getNextMeaningfulToken(
            $tokens->getNextMeaningfulToken($index)
        );

        if (null === $firstArgIndex) {
            // no arguments!
            return;
        }

        /** @var Token $firstArgToken */
        $firstArgToken = $tokens[$firstArgIndex];

        if ($firstArgToken->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            $afterFirstArgIndex = $tokens->getNextMeaningfulToken($firstArgIndex);
            $afterFirstArgToken = $tokens[$afterFirstArgIndex];

            if (!$afterFirstArgToken->equalsAny([',', ')'])) {
                // first argument is an expression like `call_user_func("foo"."bar", ...)`, not supported!
                return;
            }

            $newCallTokens = Tokens::fromCode('<?php '.substr($firstArgToken->getContent(), 1, -1).'();');
            $newCallTokensSize = $newCallTokens->count();
            $newCallTokens->clearAt(0);
            $newCallTokens->clearRange($newCallTokensSize - 3, $newCallTokensSize - 1);
            $newCallTokens->clearEmptyTokens();

            $this->replaceCallUserFuncWithCallback($tokens, $index, $newCallTokens, $firstArgIndex, $firstArgIndex);
        } elseif (\PHP_VERSION_ID >= 70000 && $firstArgToken->isGivenKind([T_FUNCTION, T_STATIC])) {
            $firstArgEndIndex = $tokens->findBlockEnd(
                Tokens::BLOCK_TYPE_CURLY_BRACE,
                $tokens->getNextTokenOfKind($firstArgIndex, ['{'])
            );
            $newCallTokens = $this->getTokensSubcollection($tokens, $firstArgIndex, $firstArgEndIndex);
            $newCallTokens->insertAt($newCallTokens->count(), new Token(')'));
            $newCallTokens->insertAt(0, new Token('('));
            $this->replaceCallUserFuncWithCallback($tokens, $index, $newCallTokens, $firstArgIndex, $firstArgEndIndex);
        } elseif ($firstArgToken->isGivenKind(T_VARIABLE)) {
            $firstArgEndIndex = $tokens->getPrevMeaningfulToken(
                $tokens->getNextTokenOfKind($firstArgIndex, [','])
            );

            $newCallTokens = $this->getTokensSubcollection($tokens, $firstArgIndex, $firstArgEndIndex);
            if ($newCallTokens->isTokenKindFound(T_OBJECT_OPERATOR)) {
                if (\PHP_VERSION_ID < 70000) {
                    return;
                }

                $newCallTokens->insertAt($newCallTokens->count(), new Token(')'));
                $newCallTokens->insertAt(0, new Token('('));
            }
            $this->replaceCallUserFuncWithCallback($tokens, $index, $newCallTokens, $firstArgIndex, $firstArgEndIndex);
        }
    }

    /**
     * @param int $callIndex
     * @param int $firstArgStartIndex
     * @param int $firstArgEndIndex
     */
    private function replaceCallUserFuncWithCallback(Tokens $tokens, $callIndex, Tokens $newCallTokens, $firstArgStartIndex, $firstArgEndIndex)
    {
        $tokens->clearRange($firstArgStartIndex, $firstArgEndIndex); // FRS end?

        $afterFirstArgIndex = $tokens->getNextMeaningfulToken($firstArgEndIndex);
        $afterFirstArgToken = $tokens[$afterFirstArgIndex];

        if ($afterFirstArgToken->equals(',')) {
            $useEllipsis = $tokens[$callIndex]->equals([T_STRING, 'call_user_func_array']);

            if ($useEllipsis) {
                $secondArgIndex = $tokens->getNextMeaningfulToken($afterFirstArgIndex);
                $tokens->insertAt($secondArgIndex, new Token([T_ELLIPSIS, '...']));
            }

            $tokens->clearAt($afterFirstArgIndex);
            $tokens->removeTrailingWhitespace($afterFirstArgIndex);
        }

        $tokens->overrideRange($callIndex, $callIndex, $newCallTokens);
    }

    private function getTokensSubcollection(Tokens $tokens, $indexStart, $indexEnd)
    {
        $size = $indexEnd - $indexStart + 1;
        $subcollection = new Tokens($size);

        for ($i = 0; $i < $size; ++$i) {
            $subcollection[$i] = clone $tokens[$i + $indexStart];
        }

        return $subcollection;
    }
}
