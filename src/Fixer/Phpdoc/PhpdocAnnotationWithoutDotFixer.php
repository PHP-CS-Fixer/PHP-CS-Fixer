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
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocAnnotationWithoutDotFixer extends AbstractFixer
{
    private $configuration = array(
        'tags' => array('throws', 'return', 'param', 'internal', 'deprecated', 'var', 'type'),
    );

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
        foreach ($tokens as $token) {
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
                    !$annotation->getTag()->valid()
                    || !in_array($annotation->getTag()->getName(), $this->configuration['tags'], true)
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

            $token->setContent($doc->getContent());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Phpdocs annotation descriptions should not be a sentence.';
    }
}
