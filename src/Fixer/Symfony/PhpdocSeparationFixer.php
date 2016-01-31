<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\TagComparator;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
final class PhpdocSeparationFixer extends AbstractFixer
{
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
            $this->fixDescription($doc);
            $this->fixAnnotations($doc);

            $token->setContent($doc->getContent());
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
     * Make sure the description is separated from the annotations.
     *
     * @param DocBlock $doc
     */
    private function fixDescription(DocBlock $doc)
    {
        foreach ($doc->getLines() as $index => $line) {
            if ($line->containsATag()) {
                break;
            }

            if ($line->containsUsefulContent()) {
                $next = $doc->getLine($index + 1);

                if ($next->containsATag()) {
                    $line->addBlank();
                    break;
                }
            }
        }
    }

    /**
     * Make sure the annotations are correctly separated.
     *
     * @param DocBlock $doc
     *
     * @return string
     */
    private function fixAnnotations(DocBlock $doc)
    {
        foreach ($doc->getAnnotations() as $index => $annotation) {
            $next = $doc->getAnnotation($index + 1);

            if (null === $next) {
                break;
            }

            if (true === $next->getTag()->valid()) {
                if (TagComparator::shouldBeTogether($annotation->getTag(), $next->getTag())) {
                    $this->ensureAreTogether($doc, $annotation, $next);
                } else {
                    $this->ensureAreSeparate($doc, $annotation, $next);
                }
            }
        }

        return $doc->getContent();
    }

    /**
     * Force the given annotations to immediately follow each other.
     *
     * @param DocBlock   $doc
     * @param Annotation $first
     * @param Annotation $second
     */
    private function ensureAreTogether(DocBlock $doc, Annotation $first, Annotation $second)
    {
        $pos = $first->getEnd();
        $final = $second->getStart();

        for ($pos = $pos + 1; $pos < $final; ++$pos) {
            $doc->getLine($pos)->remove();
        }
    }

    /**
     * Force the given annotations to have one empty line between each other.
     *
     * @param DocBlock   $doc
     * @param Annotation $first
     * @param Annotation $second
     */
    private function ensureAreSeparate(DocBlock $doc, Annotation $first, Annotation $second)
    {
        $pos = $first->getEnd();
        $final = $second->getStart() - 1;

        // check if we need to add a line, or need to remove one or more lines
        if ($pos === $final) {
            $doc->getLine($pos)->addBlank();

            return;
        }

        for ($pos = $pos + 1; $pos < $final; ++$pos) {
            $doc->getLine($pos)->remove();
        }
    }
}
