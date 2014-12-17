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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 */
class DoubleArrowMultilineWhitespacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOUBLE_ARROW) as $index => $token) {
            $this->fixWhitespaceBefore($tokens[$index - 1]);

            // do not move anything about if there is a comment following the whitespace
            if (false === $tokens[$index + 2]->isGivenKind(array(T_COMMENT, T_DOC_COMMENT))) {
                $this->fixWhitespaceAfter($tokens[$index + 1]);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * Does the given token need fixing?
     *
     * @param \Symfony\CS\Tokenizer\Token $token
     *
     * @return bool
     */
    private static function needsFixing(Token $token)
    {
        return false === $token->isWhitespace(array('whitespaces' => " \t"));
    }

    /**
     * Fix whitespace before the double arrow.
     *
     * @param \Symfony\CS\Tokenizer\Token $token
     *
     * @return void
     */
    private function fixWhitespaceBefore(Token $token)
    {
        if (self::needsFixing($token)) {
            $token->setContent(rtrim($token->getContent()).' ');
        }
    }

    /**
     * Fix whitespace after the double arrow.
     *
     * @param \Symfony\CS\Tokenizer\Token $token
     *
     * @return void
     */
    private function fixWhitespaceAfter(Token $token)
    {
        if (self::needsFixing($token)) {
            $token->setContent(' '.ltrim($token->getContent()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Operator => should not be arounded by multi-line whitespaces.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the MultilineArrayTrailingCommaFixer and AlignDoubleArrowFixer
        return 1;
    }
}
