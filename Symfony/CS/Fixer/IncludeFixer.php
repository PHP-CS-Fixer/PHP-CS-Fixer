<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class IncludeFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $inStatement = false;
        $inBraces = false;
        $bracesLevel = 0;

        foreach ($tokens as $index => $token) {
            if (!$inStatement) {
                $inStatement = Tokens::isKeyword($token) && in_array($token[0], array(T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE));

                // Don't remove when the statement is wrapped. include is also legal as function parameter
                // but requires being wrapped then
                if ($inStatement && '(' !== $tokens->getPrevNonWhitespace($index)) {
                    // Check this explicitly as there must be exactly one space after the statement
                    // And we can't add another tokens while removing this one
                    if ('(' === $tokens[$index+1]) {
                        $tokens->next();
                        $tokens->removeTrailingWhitespace($index+1);

                        $inBraces = true;
                        $bracesLevel = 1; // pre-increase so the removal of the last ones works
                        $tokens[$index+1] = ' ';
                    } elseif ('(' === $tokens->getNextNonWhitespace($index)) {
                        $inBraces = true;
                    }
                }

                continue;
            }

            if (Tokens::isWhitespace($token)) {
                $tokens->removeTrailingWhitespace($index);
                $tokens[$index] = ' ';
                $tokens->removeLeadingWhitespace($index);
            }

            if ('(' === $token) {
                if ($inBraces && 0 === $bracesLevel) {
                    $tokens->clear($index);
                }
                $tokens->removeTrailingWhitespace($index);
                ++$bracesLevel;

                continue;
            }

            if (')' === $token) {
                $tokens->removeLeadingWhitespace($index);
                --$bracesLevel;

                if ($inBraces && 0 === $bracesLevel) {
                    $tokens->clear($index);
                    $inStatement = false;
                }

                continue;
            }

            if ($inStatement && ';' === $token) {
                $tokens->removeLeadingWhitespace($index);
                $inStatement = false;
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'include';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Include and file path should be divided with a single space. File path should not be placed under brackets.';
    }
}
