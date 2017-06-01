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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocAnnotationWithoutDotFixer extends AbstractFixer
{
    private $tags = array('throws', 'return', 'param', 'internal', 'deprecated', 'var', 'type');

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Phpdocs annotation descriptions should not be a sentence.',
            array(new CodeSample('<?php
/**
 * @param string $bar Some string.
 */
function foo ($bar) {}
'))
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
            $annotations = $doc->getAnnotations();

            if (empty($annotations)) {
                continue;
            }

            foreach ($annotations as $annotation) {
                if (
                    !$annotation->getTag()->valid() || !in_array($annotation->getTag()->getName(), $this->tags, true)
                ) {
                    continue;
                }

                $content = $annotation->getContent();

                if (
                    1 !== preg_match('/[.。]$/u', $content)
                    || 0 !== preg_match('/[.。](?!$)/u', $content, $matches)
                ) {
                    continue;
                }

                $endLine = $doc->getLine($annotation->getEnd());
                $endLine->setContent(preg_replace('/(?<![.。])[.。](\s+)$/u', '\1', $endLine->getContent()));

                $startLine = $doc->getLine($annotation->getStart());
                $optionalTypeRegEx = $annotation->supportTypes()
                    ? sprintf('(?:%s\s+(?:\$\w+\s+)?)?', preg_quote(implode('|', $annotation->getTypes())))
                    : '';
                $content = preg_replace_callback('/^(\s*\*\s*@\w+\s+'.$optionalTypeRegEx.')(.*)$/', function (array $matches) {
                    return $matches[1].lcfirst($matches[2]);
                }, $startLine->getContent(), 1);
                $startLine->setContent($content);
            }

            $tokens[$index] = new Token(array(T_DOC_COMMENT, $doc->getContent()));
        }
    }
}
