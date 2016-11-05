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

namespace PhpCsFixer\DocBlock;

/**
 * This represents an entire annotation from a docblock.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Annotation
{
    /**
     * All the annotation tag names with types.
     *
     * @var string[]
     */
    private static $tags = array(
        'method',
        'param',
        'property',
        'property-read',
        'property-write',
        'return',
        'throws',
        'type',
        'var',
    );

    /**
     * The lines that make up the annotation.
     *
     * @var Line[]
     */
    private $lines;

    /**
     * The position of the first line of the annotation in the docblock.
     *
     * @var int
     */
    private $start;

    /**
     * The position of the last line of the annotation in the docblock.
     *
     * @var int
     */
    private $end;

    /**
     * The associated tag.
     *
     * @var Tag|null
     */
    private $tag;

    /**
     * The cached types content.
     *
     * @var string|null
     */
    private $typesContent;

    /**
     * Create a new line instance.
     *
     * @param Line[] $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = array_values($lines);

        $keys = array_keys($lines);

        $this->start = $keys[0];
        $this->end = end($keys);
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

    /**
     * Get all the annotation tag names with types.
     *
     * @return string[]
     */
    public static function getTagsWithTypes()
    {
        return self::$tags;
    }

    /**
     * Get the start position of this annotation.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get the end position of this annotation.
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get the associated tag.
     *
     * @return Tag
     */
    public function getTag()
    {
        if (null === $this->tag) {
            $this->tag = new Tag($this->lines[0]);
        }

        return $this->tag;
    }

    /**
     * Get the types associated with this annotation.
     *
     * @return string[]
     */
    public function getTypes()
    {
        return explode('|', $this->getTypesContent());
    }

    /**
     * Set the types associated with this annotation.
     *
     * @param string[] $types
     */
    public function setTypes(array $types)
    {
        $pattern = '/'.preg_quote($this->getTypesContent()).'/';

        $this->lines[0]->setContent(preg_replace($pattern, implode('|', $types), $this->lines[0]->getContent(), 1));

        $this->typesContent = null;
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
     * Get the current types content.
     *
     * Be careful modifying the underlying line as that won't flush the cache.
     *
     * @return string
     */
    private function getTypesContent()
    {
        if (null === $this->typesContent) {
            $name = $this->getTag()->getName();

            if (!in_array($name, self::$tags, true)) {
                throw new \RuntimeException('This tag does not support types');
            }

            $tagSplit = preg_split('/\s*\@'.$name.'\s*/', $this->lines[0]->getContent(), 2);
            $spaceSplit = preg_split('/\s/', $tagSplit[1], 2);

            $this->typesContent = $spaceSplit[0];
        }

        return $this->typesContent;
    }
}
