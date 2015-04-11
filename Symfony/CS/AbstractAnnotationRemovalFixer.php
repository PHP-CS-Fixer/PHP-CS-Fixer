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
 * @author Graham Campbell <graham@mineuk.com>
 */
abstract class AbstractAnnotationRemovalFixer extends AbstractFixer
{
    /**
     * Make sure the expected number of new lines prefix a namespace.
     *
     * @param Tokens   $tokens
     * @param string[] $type
     *
     * @return void
     */
    protected function removeAnnotations(Tokens $tokens, array $type)
    {
        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType($type);

            // nothing to do if there are no annotations
            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $annotation->remove();
            }

            $token->setContent($doc->getContent());
        }
    }
}
