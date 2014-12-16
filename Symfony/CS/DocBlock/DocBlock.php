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

use Symfony\CS\Utils;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class DocBlock
{
    /**
     * The raw array of lines.
     *
     * @var string[]
     */
    private $splits;

    /**
     * The array of lines.
     *
     * @var \Symfony\CS\DocBlock\Line[]
     */
    private $lines;

    /**
     * The array of annotations.
     *
     * @var \Symfony\CS\DocBlock\Annotation[]
     */
    private $annotations;

    /**
     * Create a new docblock instance.
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $this->splits = Utils::splitLines($content);
    }

    /**
     * Is this content an annotation?
     *
     * @param string $content
     *
     * @return bool
     */
    private function isAnnotation($content)
    {
        return 0 !== preg_match('/\\*\s+@/', $content);
    }

    /**
     * Get this docblock's lines.
     *
     * @return \Symfony\CS\DocBlock\Line[]
     */
    public function getLines()
    {
        if (null === $this->lines) {
            $this->lines = array();
            foreach ($this->splits as $pos => $content) {
                if ($this->isAnnotation($content)) {
                    $this->lines[$pos] = new Annotation($this, $pos, $content);
                } else {
                    $this->lines[$pos] = new Line($this, $pos, $content);
                }
            }
        }

        return $this->lines;
    }

    /**
     * Get a single line.
     *
     * @param int $pos
     *
     * @return \Symfony\CS\DocBlock\Line
     */
    public function getLine($pos)
    {
        $lines = $this->getLines();

        if (isset($lines[$pos])) {
            return $lines[$pos];
        }
    }

    /**
     * Get this docblock's annotations.
     *
     * @return \Symfony\CS\DocBlock\Annotation[]
     */
    public function getAnnotations()
    {
        if (null === $this->annotations) {
            $this->annotations = array();
            foreach ($this->getLines() as $line) {
                if ($line->isAnnotation()) {
                    $this->annotations[] = $line;
                }
            }
        }

        return $this->annotations;
    }

    /**
     * Get a single annotation.
     *
     * @param int $pos
     *
     * @return \Symfony\CS\DocBlock\Annotation
     */
    public function getAnnotation($pos)
    {
        $annotations = $this->getAnnotations();

        if (isset($annotations[$pos])) {
            return $annotations[$pos];
        }
    }

    /**
     * Set the content for a line.
     *
     * This method should only be called by the line class.
     *
     * @internal
     *
     * @param int    $pos
     * @param string $content
     *
     * @return void
     */
    public function setLineContent($pos, $content)
    {
        $this->splits[$pos] = $content;
    }

    /**
     * Get the actual content of this docblock.
     *
     * This method should only be called by the line class.
     *
     * @return string
     */
    public function getContent()
    {
        return implode($this->splits);
    }
}
