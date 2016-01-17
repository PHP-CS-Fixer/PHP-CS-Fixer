<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Lucas Michot <lucas@semalead.com>
 */
abstract class AbstractBlankLineAboveFixer extends AbstractFixer
{
    /**
     * The token.
     *
     * @var int
     */
    protected static $token;

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            if (!$tokens[$index]->isGivenKind(static::$token) ||
                !$tokens[$tokens->getPrevNonWhitespace($index)]->equalsAny(array(';', '}'))) {
                continue;
            }

            $previousToken = $tokens[$index - 1];

            $content = $previousToken->getContent();

            if ($previousToken->isWhitespace()) {
                $parts = explode("\n", $content);
                $partsCount = count($parts);

                if (1 === $partsCount) {
                    $content = rtrim($content, " \t")."\n\n";
                } elseif ($partsCount <= 2) {
                    $content = "\n".$content;
                }

                $previousToken->setContent($content);
            } else {
                $tokens->insertAt($index, new Token(array(T_WHITESPACE, "\n\n")));
                ++$index;
                ++$limit;
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getDescription();
}
