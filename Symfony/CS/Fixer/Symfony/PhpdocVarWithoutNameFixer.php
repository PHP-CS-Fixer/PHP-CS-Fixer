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
use Symfony\CS\DocBlock\Line;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocVarWithoutNameFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
            $doc = new DocBlock($token->getContent());

            // don't process single line docblocks
            if (1 === count($doc->getLines())) {
                continue;
            }

            $annotations = $doc->getAnnotationsOfType(array('param', 'return', 'type', 'var'));

            // only process docblocks where the first meaningful annotation is @type or @var
            if (!isset($annotations[0]) || !in_array($annotations[0]->getTag()->getName(), array('type', 'var'), true)) {
                continue;
            }

            $this->fixLine($doc->getLine($annotations[0]->getStart()));

            $token->setContent($doc->getContent());
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '@var and @type annotations should not contain the variable name.';
    }

    private function fixLine(Line $line)
    {
        $content = $line->getContent();

        preg_match_all('/ \$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $content, $matches);

        if (isset($matches[0][0])) {
            $line->setContent(str_replace($matches[0][0], '', $content));
        }
    }
}
