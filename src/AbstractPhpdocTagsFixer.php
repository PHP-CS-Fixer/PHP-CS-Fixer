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
 *
 * @internal
 */
abstract class AbstractPhpdocTagsFixer extends AbstractFixer
{
    /**
     * The tags to search for.
     *
     * @var string[]
     */
    protected static $search;

    /**
     * The replacement tag.
     *
     * @var string
     */
    protected static $replace;

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType(static::$search);

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $annotation->getTag()->setName(static::$replace);
            }

            $token->setContent($doc->getContent());
        }
    }
}
