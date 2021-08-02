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

namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

/**
 * @internal
 */
final class ClassAnalysis
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $class;

    /**
     * @var int
     */
    private $open;

    /**
     * @var array
     */
    private $extends;

    /**
     * @var array
     */
    private $implements;

    /**
     * @var bool
     */
    private $anonymous;

    /**
     * ClassAnalysis constructor.
     *
     * @param int  $start
     * @param int  $class
     * @param int  $open
     * @param bool $anonymous
     */
    public function __construct($start, $class, $open, array $extends, array $implements, $anonymous)
    {
        $this->start = $start;
        $this->class = $class;
        $this->open = $open;
        $this->extends = $extends;
        $this->implements = $implements;
        $this->anonymous = $anonymous;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * @return array
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * @return array
     */
    public function getImplements()
    {
        return $this->implements;
    }

    /**
     * @return bool
     */
    public function getAnonymous()
    {
        return $this->anonymous;
    }

    public function toArray()
    {
        return [
            'start' => $this->start,
            'classy' => $this->class,
            'open' => $this->open,
            'extends' => $this->extends,
            'implements' => $this->implements,
            'anonymous' => $this->anonymous,
        ];
    }
}
