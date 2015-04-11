<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocOrderFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
            $content = $token->getContent();
            // move param to start, return to end, leave throws in the middle
            $content = $this->moveParamAnnotations($content);
            // we're parsing the content again to make sure the internal
            // state of the dockblock is correct after the modifications
            $content = $this->moveReturnAnnotations($content);
            // persist the content at the end
            $token->setContent($content);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Annotations in phpdocs should be ordered so that param annotations come first, then throws annotations, then return annotations.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must be run before the PhpdocSeparationFixer

        /*
         * Should be run before the php_doc_separation fixer so that if we
         * create incorrect annotation grouping while moving the annotations
         * about, we're still ok.
         */
        return 5;
    }

    /**
     * Move all param annotations in before throws and return annotations.
     *
     * @param string $content
     *
     * @return string
     */
    private function moveParamAnnotations($content)
    {
        $doc = new DocBlock($content);
        $params = $doc->getAnnotationsOfType('param');

        // nothing to do if there are no param annotations
        if (empty($params)) {
            return $content;
        }

        $others = $doc->getAnnotationsOfType(array('throws', 'return'));

        if (empty($others)) {
            return $content;
        }

        // get the index of the final line of the final param annotation
        $end = end($params)->getEnd();

        $line = $doc->getLine($end);

        // move stuff about if required
        foreach ($others as $other) {
            if ($other->getStart() < $end) {
                // we're doing this to maintain the original line indexes
                $line->setContent($line->getContent().$other->getContent());
                $other->remove();
            }
        }

        return $doc->getContent();
    }

    /**
     * Move all return annotations after param and throws annotations.
     *
     * @param string $content
     *
     * @return string
     */
    private function moveReturnAnnotations($content)
    {
        $doc = new DocBlock($content);
        $returns = $doc->getAnnotationsOfType('return');

        // nothing to do if there are no return annotations
        if (empty($returns)) {
            return $content;
        }

        $others = $doc->getAnnotationsOfType(array('param', 'throws'));

        // nothing to do if there are no other annotations
        if (empty($others)) {
            return $content;
        }

        // get the index of the first line of the first return annotation
        $start = $returns[0]->getStart();
        $line = $doc->getLine($start);

        // move stuff about if required
        foreach (array_reverse($others) as $other) {
            if ($other->getEnd() > $start) {
                // we're doing this to maintain the original line indexes
                $line->setContent($other->getContent().$line->getContent());
                $other->remove();
            }
        }

        return $doc->getContent();
    }
}
