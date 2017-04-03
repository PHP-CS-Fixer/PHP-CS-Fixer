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

namespace PhpCsFixer\Error;

/**
 * An abstraction for errors that can occur before and during fixing.
 *
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class Error
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
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var \Throwable|null
     */
    private $source;

    /**
     * @param int             $type
     * @param string          $filePath
     * @param \Throwable|null $source
     */
    public function __construct($type, $filePath, $source = null)
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
     * @return \Throwable|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
}
