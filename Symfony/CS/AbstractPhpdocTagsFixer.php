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

use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\Tokenizer\Tokens;

/**
 * This abstract fixer provides a base for fixers to rename tags.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
abstract class AbstractPhpdocTagsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType(static::$search);

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $line = $doc->getLine($annotation->getStart());
                $line->setContent(str_replace(static::$input, static::$output, $line->getContent()));
            }

            $token->setContent($doc->getContent());
        }

        return $tokens->generateCode();
    }
}
