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
use Symfony\CS\DocBlock\Annotation;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocSeparationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $index => $token) {
            $doc = new DocBlock($token->getContent());
            $this->fixDescription($doc);
            $this->fixAnnotations($doc);
            $tokens[$index]->setContent($doc->getContent());
        }

        return $tokens->generateCode();
    }

    /**
     * Make sure the description is separated from the annotations.
     *
     * @param \Symfony\CS\DocBlock\DocBlock $doc
     */
    private function fixDescription(DocBlock $doc)
    {
        foreach ($doc->getLines() as $index => $line) {
            if ($line->isAnnotation()) {
                break;
            }

            if ($line->hasUsefulContent()) {
                $next = $doc->getLine($index + 1);
                if ($next->isAnnotation()) {
                    $line->addBlank();
                    break;
                }
            }
        }
    }

    /**
     * Make sure the annotations are correctly separated.
     *
     * @param \Symfony\CS\DocBlock\DocBlock $doc
     */
    private function fixAnnotations(DocBlock $doc)
    {
        foreach ($doc->getAnnotations() as $index => $annotation) {
            $next = $doc->getAnnotation($index + 1);

            if (null === $next) {
                break;
            }

            if ($annotation->getType() === $next->getType()) {
                $this->ensureAreTogether($doc, $annotation, $next);
            } else {
                $this->ensureAreSeparate($doc, $annotation, $next);
            }
        }

        return $doc->getContent();
    }

    /**
     * Force the given annotations to immediately follow each other.
     *
     * @param \Symfony\CS\DocBlock\DocBlock $doc
     */
    private function ensureAreTogether(DocBlock $doc, Annotation $first, Annotation $second)
    {
        $pos = $first->getEnd();
        $final = $second->getStart();

        for (++$pos; $pos < $final; ++$pos) {
            $doc->getLine($pos)->remove();
        }
    }

    /**
     * Force the given annotations to have one empty line between each other.
     *
     * @param \Symfony\CS\DocBlock\DocBlock $doc
     */
    private function ensureAreSeparate(DocBlock $doc, Annotation $first, Annotation $second)
    {
        $pos = $first->getEnd();
        $final = $second->getStart() - 1;

        // check if we need to add a line, or need to remove one or more lines
        if ($pos === $final) {
            $doc->getLine($pos)->addBlank();
        } else {
            for (++$pos; $pos < $final; ++$pos) {
                $doc->getLine($pos)->remove();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Annotations in phpdocs should be grouped together so that annotations of the same type immediately follow each other, and annotations of a different type are separated by a single blank line.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must be run before the PhpdocParamsFixer
        return 10;
    }
}
