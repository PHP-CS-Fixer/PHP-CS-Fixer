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
    const TYPE_LINTING = 1;
    const TYPE_FIXING = 2;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $message;

    /**
     * @param string $type
     * @param string $filePath
     * @param string $message
     */
    public function __construct($type, $filePath, $message)
    {
        $this->type = $type;
        $this->filePath = $filePath;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
