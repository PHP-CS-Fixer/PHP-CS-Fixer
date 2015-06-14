<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Error;

/**
 * An abstraction for errors that can occur before and during fixing.
 *
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
class Error
{
    /**
     * Error which has occurred in linting phase, before applying any fixers.
     */
    const TYPE_INVALID = 1;

    /**
     * Error which has occurred during fixing phase.
     */
    const TYPE_EXCEPTION = 2;

    /**
     * Error which has occurred in linting phase, after applying any fixers.
     */
    const TYPE_LINT = 3;

    /**
     * Error with the configuration, before applying any fixers.
     */
    const TYPE_CONFIGURATION = 4;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var \Exception
     */
    private $source;

    /**
     * @param string     $type
     * @param string     $filePath
     * @param \Exception $source
     */
    public function __construct($type, $filePath, \Exception $source = null)
    {
        $this->type = $type;
        $this->filePath = $filePath;
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return \Exception
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
