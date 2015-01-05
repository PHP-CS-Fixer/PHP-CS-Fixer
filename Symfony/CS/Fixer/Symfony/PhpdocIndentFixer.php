<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Utils;

/**
 * @author Ceeram <ceeram@cakephp.org>
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocIndentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $index => $token) {
            $nextIndex = $tokens->getNextMeaningfulToken($index);

            if (null === $nextIndex) {
                continue;
            }

            $prevToken = $tokens[$index - 1];

            // ignore inline docblocks
            if (
                ($prevToken->isWhitespace(array('whitespaces' => " \t")) && !$tokens[$index - 2]->isGivenKind(T_OPEN_TAG))
                || $prevToken->equalsAny(array(';', '{'))
            ) {
                continue;
            }

            $indent = Utils::calculateTrailingWhitespaceIndent($tokens[$nextIndex - 1]);

            $prevToken->setContent($this->fixWhitespaceBefore($prevToken->getContent(), $indent));
            $token->setContent($this->fixDocBlock($token->getContent(), $indent));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Docblocks should have the same indentation as the documented subject.';
    }

    /**
     * Fix indentation of Docblock.
     *
     * @param string $content Docblock contents
     * @param string $indent  Indentation to apply
     *
     * @return string Dockblock contents including correct indentation
     */
    private function fixDocBlock($content, $indent)
    {
        return ltrim(preg_replace('/^[ \t]*/m', $indent.' ', $content));
    }

    /**
     * Fix whitespace before the Docblock.
     *
     * @param string $content Whitespace before Docblock
     * @param string $indent  Indentation of the documented subject
     *
     * @return string Whitespace including correct indentation for Dockblock after this whitespace
     */
    private function fixWhitespaceBefore($content, $indent)
    {
        return rtrim($content, " \t").$indent;
    }
}
