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
     * Returns errors reported during linting, prior to fixing.
     *
     * @return Error[]
     */
    public function getLintingErrors()
    {
        return array_filter($this->errors, function (Error $error) {
            return $error->getType() === Error::TYPE_LINTING;
        });
    }

    /**
     * Returns errors reported during fixing.
     *
     * @return Error[]
     */
    public function getFixingErrors()
    {
        return array_filter($this->errors, function (Error $error) {
            return $error->getType() === Error::TYPE_FIXING;
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
