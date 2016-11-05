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

namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class IncludeFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $includies = $this->findIncludies($tokens);
        $this->clearIncludies($tokens, $includies);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Include/Require and file path should be divided with a single space. File path should not be placed under brackets.';
    }

    private function clearIncludies(Tokens $tokens, array $includies)
    {
        foreach (array_reverse($includies) as $includy) {
            if ($includy['end'] && !$tokens[$includy['end']]->isGivenKind(T_CLOSE_TAG)) {
                $tokens->removeLeadingWhitespace($includy['end']);
            }

            $braces = $includy['braces'];

            if ($braces) {
                $nextToken = $tokens[$tokens->getNextMeaningfulToken($braces['close'])];

                if ($nextToken->equalsAny(array(';', array(T_CLOSE_TAG)))) {
                    $tokens->removeLeadingWhitespace($braces['open']);
                    $tokens->removeTrailingWhitespace($braces['open']);
                    $tokens->removeLeadingWhitespace($braces['close']);
                    $tokens->removeTrailingWhitespace($braces['close']);

                    $tokens[$braces['open']] = new Token(array(T_WHITESPACE, ' '));
                    $tokens[$braces['close']]->clear();
                }
            }

            $nextIndex = $includy['begin'] + 1;
            $nextToken = $tokens[$nextIndex];

            while ($nextToken->isEmpty()) {
                $nextToken = $tokens[++$nextIndex];
            }

            if ($nextToken->isWhitespace()) {
                $nextToken->setContent(' ');
            } elseif ($braces || $tokens[$nextIndex]->isGivenKind(array(T_VARIABLE, T_CONSTANT_ENCAPSED_STRING, T_COMMENT))) {
                $tokens->insertAt($includy['begin'] + 1, new Token(array(T_WHITESPACE, ' ')));
            }
        }
    }

    private function findIncludies(Tokens $tokens)
    {
        static $includyTokenKinds = array(T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE);

        $includies = array();

        foreach ($tokens->findGivenKind($includyTokenKinds) as $includyTokens) {
            foreach ($includyTokens as $index => $token) {
                $includy = array(
                    'begin' => $index,
                    'braces' => null,
                    'end' => $tokens->getNextTokenOfKind($index, array(';', array(T_CLOSE_TAG))),
                );

                $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
                $nextToken = $tokens[$nextTokenIndex];

                if ($nextToken->equals('(')) {
                    // Don't remove braces when the statement is wrapped.
                    // Include is also legal as function parameter or condition statement but requires being wrapped then.
                    $braceCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $nextTokenIndex);

                    if ($tokens[$tokens->getNextMeaningfulToken($braceCloseIndex)]->equalsAny(array(';', array(T_CLOSE_TAG)))) {
                        $includy['braces'] = array(
                            'open' => $nextTokenIndex,
                            'close' => $braceCloseIndex,
                        );
                    }
                }

                $includies[] = $includy;
            }
        }

        return $includies;
    }
}
