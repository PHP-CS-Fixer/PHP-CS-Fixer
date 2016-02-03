<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
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
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must be run before the PhpdocSeparationFixer, PhpdocOrderFixer, and
        // PhpdocTrimFixer
        return 10;
    }

    /**
     * Make sure the expected number of new lines prefix a namespace.
     *
     * @param Tokens   $tokens
     * @param string[] $type
     */
    protected function removeAnnotations(Tokens $tokens, array $type)
    {
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

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
