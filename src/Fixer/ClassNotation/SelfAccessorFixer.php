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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class SelfAccessorFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Inside class or interface element "self" should be preferred to the class name itself.',
            [
                new CodeSample(
                    '<?php
class Sample
{
    const BAZ = 1;
    const BAR = Sample::BAZ;

    public function getBar()
    {
        return Sample::BAR;
    }
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([T_CLASS, T_INTERFACE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($i = 0, $c = $tokens->count(); $i < $c; ++$i) {
            if (!$tokens[$i]->isGivenKind([T_CLASS, T_INTERFACE]) || $tokensAnalyzer->isAnonymousClass($i)) {
                continue;
            }

            $nameIndex = $tokens->getNextTokenOfKind($i, [[T_STRING]]);
            $startIndex = $tokens->getNextTokenOfKind($nameIndex, ['{']);
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);

            $name = $tokens[$nameIndex]->getContent();

            $this->replaceNameOccurrences($tokens, $name, $startIndex, $endIndex);

            // continue after the class declaration
            $i = $endIndex;
        }
    }

    /**
     * Replace occurrences of the name of the classy element by "self" (if possible).
     *
     * @param Tokens $tokens
     * @param string $name
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function replaceNameOccurrences(Tokens $tokens, $name, $startIndex, $endIndex)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $insideMethodSignatureUntil = null;

        for ($i = $startIndex; $i < $endIndex; ++$i) {
            if ($i === $insideMethodSignatureUntil) {
                $insideMethodSignatureUntil = null;
            }

            $token = $tokens[$i];

            if (
                // skip anonymous classes
                ($token->isGivenKind(T_CLASS) && $tokensAnalyzer->isAnonymousClass($i)) ||
                // skip lambda functions (PHP < 5.4 compatibility)
                ($token->isGivenKind(T_FUNCTION) && $tokensAnalyzer->isLambda($i))
            ) {
                $i = $tokens->getNextTokenOfKind($i, ['{']);
                $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $i);

                continue;
            }

            if ($token->isGivenKind(T_FUNCTION)) {
                $i = $tokens->getNextTokenOfKind($i, ['(']);
                $insideMethodSignatureUntil = $tokens->getNextTokenOfKind($i, ['{', ';']);

                continue;
            }

            if (!$token->equals([T_STRING, $name], false)) {
                continue;
            }

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($i)];
            $nextToken = $tokens[$tokens->getNextMeaningfulToken($i)];

            // skip tokens that are part of a fully qualified name or used in class property access
            if ($prevToken->isGivenKind([T_NS_SEPARATOR, T_OBJECT_OPERATOR]) || $nextToken->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            if (
                $prevToken->isGivenKind([T_INSTANCEOF, T_NEW])
                || $nextToken->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)
                || (
                    null !== $insideMethodSignatureUntil
                    && $i < $insideMethodSignatureUntil
                    && $prevToken->equalsAny(['(', ',', [CT::T_TYPE_COLON], [CT::T_NULLABLE_TYPE]])
                )
            ) {
                $tokens[$i] = new Token([T_STRING, 'self']);
            }
        }
    }
}
