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
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class RegularCallableCallFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Callables must be called without using `call_user_func*` when possible.',
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
            ],
            null,
            'Risky when the `call_user_func` or `call_user_func_array` function is overridden or when are used in constructions that should be avoided, like `call_user_func_array(\'foo\', [\'bar\' => \'baz\'])` or `call_user_func($foo, $foo = \'bar\')`.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $functionsAnalyzer = new FunctionsAnalyzer();
        $argumentsAnalyzer = new ArgumentsAnalyzer();

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->equalsAny([[T_STRING, 'call_user_func'], [T_STRING, 'call_user_func_array']], false)) {
                continue;
            }

            if (!$functionsAnalyzer->isGlobalFunctionCall($tokens, $index)) {
                continue; // redeclare/override
            }

            $openParenthesis = $tokens->getNextMeaningfulToken($index);
            $closeParenthesis = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesis);
            $arguments = $argumentsAnalyzer->getArguments($tokens, $openParenthesis, $closeParenthesis);

            if (1 > \count($arguments)) {
                return; // no arguments!
            }

            $this->processCall($tokens, $index, $arguments);
        }
    }

    /**
     * @param int $index
     */
    private function processCall(Tokens $tokens, $index, array $arguments)
    {
        $firstArgIndex = $tokens->getNextMeaningfulToken(
            $tokens->getNextMeaningfulToken($index)
        );

        /** @var Token $firstArgToken */
        $firstArgToken = $tokens[$firstArgIndex];

        if ($firstArgToken->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            $afterFirstArgIndex = $tokens->getNextMeaningfulToken($firstArgIndex);
            if (!$tokens[$afterFirstArgIndex]->equalsAny([',', ')'])) {
                return; // first argument is an expression like `call_user_func("foo"."bar", ...)`, not supported!
            }

            $newCallTokens = Tokens::fromCode('<?php '.substr($firstArgToken->getContent(), 1, -1).'();');
            $newCallTokensSize = $newCallTokens->count();
            $newCallTokens->clearAt(0);
            $newCallTokens->clearRange($newCallTokensSize - 3, $newCallTokensSize - 1);
            $newCallTokens->clearEmptyTokens();

            $this->replaceCallUserFuncWithCallback($tokens, $index, $newCallTokens, $firstArgIndex, $firstArgIndex);
        } elseif ($firstArgToken->isGivenKind([T_FUNCTION, T_STATIC])) {
            if (\PHP_VERSION_ID >= 70000) {
                $firstArgEndIndex = $tokens->findBlockEnd(
                    Tokens::BLOCK_TYPE_CURLY_BRACE,
                    $tokens->getNextTokenOfKind($firstArgIndex, ['{'])
                );

                $newCallTokens = $this->getTokensSubcollection($tokens, $firstArgIndex, $firstArgEndIndex);
                $newCallTokens->insertAt($newCallTokens->count(), new Token(')'));
                $newCallTokens->insertAt(0, new Token('('));
                $this->replaceCallUserFuncWithCallback($tokens, $index, $newCallTokens, $firstArgIndex, $firstArgEndIndex);
            }
        } elseif ($firstArgToken->isGivenKind(T_VARIABLE)) {
            $firstArgEndIndex = reset($arguments);

            // check if the same variable is used multiple times and if so do not fix

            foreach ($arguments as $argumentStart => $argumentEnd) {
                if ($firstArgEndIndex === $argumentEnd) {
                    continue;
                }

                for ($i = $argumentStart; $i <= $argumentEnd; ++$i) {
                    if ($tokens[$i]->equals($firstArgToken)) {
                        return;
                    }
                }
            }

            // check if complex statement and if so wrap the call in () if on PHP 7 or up, else do not fix

            $newCallTokens = $this->getTokensSubcollection($tokens, $firstArgIndex, $firstArgEndIndex);
            $complex = false;

            for ($newCallIndex = \count($newCallTokens) - 1; $newCallIndex >= 0; --$newCallIndex) {
                if ($newCallTokens[$newCallIndex]->isGivenKind([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT, T_VARIABLE])) {
                    continue;
                }

                $blockType = Tokens::detectBlockType($newCallTokens[$newCallIndex]);

                if (null !== $blockType && (Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE === $blockType['type'] || Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE === $blockType['type'])) {
                    $newCallIndex = $newCallTokens->findBlockStart($blockType['type'], $newCallIndex);

                    continue;
                }

                $complex = true;

                break;
            }

            if ($complex) {
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

        $prevIndex = $tokens->getPrevMeaningfulToken($callIndex);

        if ($tokens[$prevIndex]->isGivenKind(T_NS_SEPARATOR)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($prevIndex);
        }
    }

    private function getTokensSubcollection(Tokens $tokens, $indexStart, $indexEnd)
    {
        $size = $indexEnd - $indexStart + 1;
        $subcollection = new Tokens($size);

        for ($i = 0; $i < $size; ++$i) {
            /** @var Token $toClone */
            $toClone = $tokens[$i + $indexStart];
            $subcollection[$i] = clone $toClone;
        }

        return $subcollection;
    }
}
