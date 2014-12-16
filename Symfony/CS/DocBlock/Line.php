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
class Line
{
    /**
     * The docblock this line belongs to.
     *
     * @var \Symfony\CS\DocBlock\Docblock
     */
    protected $doc;

    /**
     * The position of this line in the docblock.
     *
     * @var \Symfony\CS\DocBlock\Docblock
     */
    protected $pos;

    /**
     * The content of this line.
     *
     * @var \Symfony\CS\DocBlock\Docblock
     */
    protected $content;

    /**
     * Create a new line instance.
     *
     * @param \Symfony\CS\DocBlock\Docblock $doc
     * @param int                           $pos
     * @param string                        $content
     */
    public function __construct(DocBlock $doc, $pos, $content)
    {
        $this->doc = $doc;
        $this->pos = $pos;
        $this->content = $content;
    }

    /**
     * Get the start position of this line.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->pos;
    }

    /**
     * Get the end position of this line.
     *
     * @return int
     */
    public function getEnd()
    {
        return $this->pos;
    }

    /**
     * Does this line contain useful content?
     *
     * This means it is not the the first or final line, and is not empty.
     *
     * @return bool
     */
    public function hasUsefulContent()
    {
        return 0 !== preg_match('/\\*\s+\S+/', $this->content) && 0 === preg_match('/\\*\//', $this->content);
    }

    /**
     * Is this line an annotation?
     *
     * @return bool
     */
    public function isAnnotation()
    {
        return false;
    }

    /**
     * Remove this line by clearing its contents.
     *
     * This method also persist the changes to the docblock class.
     *
     * @return void
     */
    public function remove()
    {
        $this->content = '';
        $this->doc->setLineContent($this->pos, $this->content);
    }

    /**
     * Append a blank docblock line to this line's contents.
     *
     * This method also persist the changes to the docblock class.
     *
     * @return void
     */
    public function addBlank()
    {
        $this->content .= "     *\n";
        $this->doc->setLineContent($this->pos, $this->content);
    }
}
