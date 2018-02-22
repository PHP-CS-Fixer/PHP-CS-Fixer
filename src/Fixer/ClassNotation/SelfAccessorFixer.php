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
            array(
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
}'
                ),
            ),
            null,
            'Risky when using dynamic calls like get_called_class() or late static binding.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_CLASS, T_INTERFACE));
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($i = 0, $c = $tokens->count(); $i < $c; ++$i) {
            if (!$tokens[$i]->isGivenKind(array(T_CLASS, T_INTERFACE)) || $tokensAnalyzer->isAnonymousClass($i)) {
                continue;
            }

            $nameIndex = $tokens->getNextTokenOfKind($i, array(array(T_STRING)));
            $startIndex = $tokens->getNextTokenOfKind($nameIndex, array('{'));
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

        for ($i = $startIndex; $i < $endIndex; ++$i) {
            $token = $tokens[$i];

            if (
                // skip anonymous classes
                ($token->isGivenKind(T_CLASS) && $tokensAnalyzer->isAnonymousClass($i)) ||
                // skip lambda functions (PHP < 5.4 compatibility)
                ($token->isGivenKind(T_FUNCTION) && $tokensAnalyzer->isLambda($i))
            ) {
                $i = $tokens->getNextTokenOfKind($i, array('{'));
                $i = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $i);

                continue;
            }

            if (!$token->equals(array(T_STRING, $name), false)) {
                continue;
            }

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($i)];
            $nextToken = $tokens[$tokens->getNextMeaningfulToken($i)];

            // skip tokens that are part of a fully qualified name or used in class property access
            if ($prevToken->isGivenKind(array(T_NS_SEPARATOR, T_OBJECT_OPERATOR)) || $nextToken->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            if (
                $prevToken->isGivenKind(array(T_INSTANCEOF, T_NEW)) ||
                $nextToken->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)
            ) {
                $tokens[$i] = new Token(array(T_STRING, 'self'));
            }
        }
    }
}
