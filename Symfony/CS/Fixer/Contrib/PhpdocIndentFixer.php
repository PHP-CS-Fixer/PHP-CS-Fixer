<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Ceeram <ceeram@cakephp.org>
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
            $next = $tokens->getNextMeaningfulToken($index);
            if ($next === null) {
                continue;
            }

            $indent = $this->calculateIndent($tokens[$next - 1]->getContent());
            if (!$indent) {
                continue;
            }

            $prevToken = $tokens[$index - 1];
            if ($this->hasMatchingIndent($prevToken->getContent(), $indent)) {
                continue;
            }

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
     * Fix indentation of Docblock
     *
     * @param  string $content Docblock contents
     * @param  string $indent  Indentation to apply
     * @return string Dockblock contents including correct indentation
     */
    private function fixDocBlock($content, $indent)
    {
        $lines = explode("\n", str_replace(array("\r\n", "\r"), "\n", $content));

        for ($i = 1, $l = count($lines); $i < $l; $i++) {
            $lines[$i] = $indent." ".ltrim($lines[$i], " ");
        }

        return implode("\n", $lines);
    }

    /**
     * Check if Dockblock uses same indentation as the documented subject
     *
     * @param  string $content Whitespace before Docblock
     * @param  string $indent  Indentation of the subject
     * @return bool
     */
    private function hasMatchingIndent($content, $indent)
    {
        return rtrim($content, " ").$indent === $content;
    }

    /**
     * Fix whitespace before the Docblock
     *
     * @param  string $content Whitespace before Docblock
     * @param  string $indent  Indentation of the documented subject
     * @return string Whitespace including correct indentation for Dockblock after this whitespace
     */
    private function fixWhitespaceBefore($content, $indent)
    {
        return rtrim($content, " ").$indent;
    }

    /**
     * Calculate used indentation from the whitespace before documented subject
     *
     * @param  string      $content Whitespace before documented subject
     * @return string|null
     */
    private function calculateIndent($content)
    {
        $lines = explode("\n", str_replace(array("\r\n", "\r"), "\n", $content));

        return end($lines);
    }
}
