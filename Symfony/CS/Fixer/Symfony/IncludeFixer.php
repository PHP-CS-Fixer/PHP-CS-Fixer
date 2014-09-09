<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class IncludeFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $includies = $this->findIncludies($tokens);
        $this->clearIncludies($tokens, $includies);

        return $tokens->generateCode();
    }

    private function clearIncludies(Tokens $tokens, array $includies)
    {
        foreach (array_reverse($includies) as $includy) {
            if ($includy['end']) {
                $tokens->removeLeadingWhitespace($includy['end']);
            }

            $braces = $includy['braces'];

            if ($braces) {
                $nextToken = $tokens->getNextNonWhitespace($includy['braces']['close']);

                if (!$nextToken->isArray() && ';' === $nextToken->content) {
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
                $nextToken->content = ' ';
            } elseif ($braces) {
                $tokens->insertAt($includy['begin'] + 1, new Token(array(T_WHITESPACE, ' ')));
            }
        }
    }

    private function findIncludies(Tokens $tokens)
    {
        static $includyTokens = array(T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE);

        $inStatement = false;
        $inBraces = false;
        $bracesLevel = 0;

        $includies = array();
        $includiesCount = 0;

        for ($index = 0, $indexLimit = count($tokens); $index < $indexLimit; ++$index) {
            $token = $tokens[$index];

            if (!$inStatement) {
                $inStatement = $token->isGivenKind($includyTokens);

                if (!$inStatement) {
                    continue;
                }

                $includies[$includiesCount] = array(
                    'begin' => $index,
                    'braces' => null,
                    'end' => null,
                );

                // Don't remove when the statement is wrapped. include is also legal as function parameter
                // but requires being wrapped then
                if ('(' !== $tokens->getPrevNonWhitespace($index)->content) {
                    $nextTokenIndex = null;
                    $nextToken = $tokens->getNextNonWhitespace($index, array(), $nextTokenIndex);

                    if ('(' === $nextToken->content) {
                        $inBraces = true;
                        $bracesLevel = 1;
                        $index = $nextTokenIndex;
                        $includies[$includiesCount]['braces'] = array(
                            'open' => $index,
                            'close' => null,
                        );
                    }
                }

                continue;
            }

            if ($token->isArray() || $token->isWhitespace()) {
                continue;
            }

            if ('(' === $token->content) {
                ++$bracesLevel;

                continue;
            }

            if (')' === $token->content) {
                --$bracesLevel;

                if ($inBraces && 0 === $bracesLevel) {
                    $inStatement = false;
                    $includies[$includiesCount]['braces']['close'] = $index;

                    $nextTokenIndex = null;
                    $nextToken = $tokens->getNextNonWhitespace($index, array(), $nextTokenIndex);

                    if (';' === $nextToken->content) {
                        $includies[$includiesCount]['end'] = $nextTokenIndex;
                        ++$includiesCount;
                    }

                    $index = $nextTokenIndex;
                }

                continue;
            }

            if ($inStatement && ';' === $token->content) {
                $inStatement = false;
                $includies[$includiesCount]['end'] = $index;
                ++$includiesCount;
            }
        }

        return $includies;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Include and file path should be divided with a single space. File path should not be placed under brackets.';
    }
}
