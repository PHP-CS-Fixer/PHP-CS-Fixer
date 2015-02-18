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
 * This represents an entire annotation from a docblock.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class Annotation
{
    /**
     * The lines that make up the annotation.
     *
     * Note that the array indexes represent the position in the docblock.
     *
     * @var Lines[]
     */
    private $lines;

    /**
     * The associated tag.
     *
     * @var Tag|null
     */
    private $tag;

    /**
     * Create a new line instance.
     *
     * @param Lines[] $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    /**
     * Get the start position of this annotation.
     *
     * @return int
     */
    public function getStart()
    {
        $keys = array_keys($this->lines);

        return $keys[0];
    }

    /**
     * Get the end position of this annotation.
     *
     * @return int
     */
    public function getEnd()
    {
        $keys = array_keys($this->lines);

        return end($keys);
    }

    /**
     * Get the associated tag.
     *
     * @return Tag
     */
    public function getTag()
    {
        if (null === $this->tag) {
            $values = array_values($this->lines);
            $this->tag = new Tag($values[0]->getContent());
        }

        return $this->tag;
    }

    /**
     * Remove this annotation by removing all its lines.
     */
    public function remove()
    {
        foreach ($this->lines as $line) {
            $line->remove();
        }
    }

    /**
     * Get the annotation content.
     *
     * @return string
     */
    public function getContent()
    {
        return implode($this->lines);
    }

    /**
     * Get the string representation of object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
