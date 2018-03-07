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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class PhpdocVarWithoutNameFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            '@var and @type annotations should not contain the variable name.',
            [new CodeSample('<?php
final class Foo
{
    /**
     * @var int $bar
     */
    public $bar;

    /**
     * @var $baz float
     */
    public $baz;

}
')]
        );
    }

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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());

            // don't process single line docblocks
            if (1 === count($doc->getLines())) {
                continue;
            }

            $annotations = $doc->getAnnotationsOfType(['param', 'return', 'type', 'var']);

            // only process docblocks where the first meaningful annotation is @type or @var
            if (!isset($annotations[0]) || !in_array($annotations[0]->getTag()->getName(), ['type', 'var'], true)) {
                continue;
            }

            $this->fixLine($doc->getLine($annotations[0]->getStart()));

            $tokens[$index] = new Token([T_DOC_COMMENT, $doc->getContent()]);
        }
    }

    private function fixLine(Line $line)
    {
        $content = $line->getContent();

        Preg::matchAll('/ \$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $content, $matches);

        if (isset($matches[0][0])) {
            $line->setContent(str_replace($matches[0][0], '', $content));
        }
    }
}
