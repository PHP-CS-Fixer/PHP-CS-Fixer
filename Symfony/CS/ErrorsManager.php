<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * Manager of errors that occur during fixing.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ErrorsManager
{
    const ERROR_TYPE_EXCEPTION = 1;

    /**
     * Errors.
     *
     * @var array
     */
    private $errors = array();

    /**
     * Get all reported errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
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
     * @param int    $type     error type
     * @param string $filepath file, on which error occurs
     * @param string $message  description of error
     */
    public function report($type, $filepath, $message)
    {
        $this->errors[] = array(
            'type' => $type,
            'filepath' => $filepath,
            'message' => $message,
        );
    }
}
