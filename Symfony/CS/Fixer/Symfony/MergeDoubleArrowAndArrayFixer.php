<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
class MergeDoubleArrowAndArrayFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $idx => $token) {
            if ($token->isGivenKind(T_ARRAY)) {
                $prevTokenIdx = $tokens->getPrevNonWhitespace($idx);
                $prevToken = $tokens[$prevTokenIdx];
                $prevWhitespace = $tokens[$idx - 1];
                $prevWhitespaceContent = $prevWhitespace->getContent();

                if (false !== strpos($prevWhitespaceContent, "\n") && $prevToken->isGivenKind(T_DOUBLE_ARROW)) {
                    $prevWhitespace->setContent(rtrim($prevWhitespaceContent).' ');
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Merge in a single line double arrows and array statements.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the MultilineArrayTrailingComma
        return 1;
    }
}
