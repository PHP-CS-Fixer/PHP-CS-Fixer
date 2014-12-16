<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\DocBlock;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class Annotation extends Line
{
    /**
     * Get the end position of this line.
     *
     * This method will reliably find the true end of the entire annotation
     * including its description.
     *
     * @return int
     */
    public function getEnd()
    {
        $index = $this->pos;

        while ($line = $this->doc->getLine(++$index)) {
            if ($line->isAnnotation()) {
                return $index - 1;
            } elseif (!$line->hasUsefulContent()) {
                return $index - 1;
            }
        }

        return $index - 1;
    }

    /**
     * Is this line an annotation?
     *
     * @return bool
     */
    public function isAnnotation()
    {
        return true;
    }

    /**
     * Get the annotation type.
     *
     * @return string
     */
    public function getType()
    {
        // TODO: make this actually detect the annotation type

        if (preg_match('/\\*\s+@param/', $this->content)) {
            return 'param';
        }

        if (preg_match('/\\*\s+@throws/', $this->content)) {
            return 'throws';
        }

        if (preg_match('/\\*\s+@return/', $this->content)) {
            return 'return';
        }
    }
}
