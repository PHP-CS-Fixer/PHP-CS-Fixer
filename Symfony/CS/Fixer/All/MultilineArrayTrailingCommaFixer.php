<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\FixerInterface;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class MultilineArrayTrailingCommaFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens->isArray($index)) {
                $this->fixArray($tokens, $index);
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
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'multiline_array_trailing_comma';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP multi-line arrays should have a trailing comma.';
    }

    private function fixArray(Tokens $tokens, $index)
    {
        $bracesLevel = 0;

        // Skip only when it is an array, for short arrays we need the brace for correct
        // level counting
        if ($tokens[$index]->isGivenKind(T_ARRAY)) {
            ++$index;
        }

        if (!$tokens->isArrayMultiLine($index)) {
            return ;
        }

        for ($c = $tokens->count(); $index < $c; ++$index) {
            $token = $tokens[$index];

            if ('(' === $token->content || '[' === $token->content) {
                ++$bracesLevel;

                continue;
            }

            if (')' === $token->content || ']' === $token->content) {
                --$bracesLevel;

                if (0 !== $bracesLevel) {
                    continue;
                }

                $foundIndex = null;
                $prevToken = $tokens->getTokenNotOfKindSibling($index, -1, array(array(T_WHITESPACE), array(T_COMMENT), array(T_DOC_COMMENT)), $foundIndex);

                if (',' !== $prevToken->content) {
                    $tokens->insertAt($foundIndex + 1, array(new Token(',')));
                }

                break;
            }
        }
    }
}
