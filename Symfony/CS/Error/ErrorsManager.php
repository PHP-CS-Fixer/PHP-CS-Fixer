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
 * Manager of errors that occur during fixing.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class ErrorsManager
{
    /**
     * Errors.
     *
     * @var Error[]
     */
    private $errors = array();

    /**
     * Get all reported external errors.
     *
     * @return Error[]
     */
    public function getExternalErrors()
    {
        return array_filter($this->errors, function (Error $error) {
            return $error->getType() === Error::ERROR_TYPE_EXTERNAL;
        });
    }

    /**
     * Get all reported internal errors.
     *
     * @return Error[]
     */
    public function getInternalErrors()
    {
        return array_filter($this->errors, function (Error $error) {
            return $error->getType() === Error::ERROR_TYPE_INTERNAL;
        });
    }

    /**
     * Check if no errors was reported.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->errors);
    }

    /**
     * Report error.
     *
     * @param Error $error
     */
    public function report(Error $error)
    {
        $this->errors[] = $error;
    }
}
