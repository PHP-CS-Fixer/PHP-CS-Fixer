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
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocTypeToVarFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
            $doc = new DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType('type');

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                $line = $doc->getLine($annotation->getStart());
                $line->setContent(str_replace('@type', '@var', $line->getContent()));
            }

            $token->setContent($doc->getContent());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@type should always be written as @var.';
    }
}
