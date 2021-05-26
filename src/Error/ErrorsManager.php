<?php

declare(strict_types=1);

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
 * Manager of errors that occur during fixing.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ErrorsManager
{
    /**
     * @var Error[]
     */
    private $errors = [];

    /**
     * Returns errors reported during linting before fixing.
     *
     * @return Error[]
     */
    public function getInvalidErrors(): array
    {
        return array_filter($this->errors, static function (Error $error) {
            return Error::TYPE_INVALID === $error->getType();
        });
    }

    /**
     * Returns errors reported during fixing.
     *
     * @return Error[]
     */
    public function getExceptionErrors(): array
    {
        return array_filter($this->errors, static function (Error $error) {
            return Error::TYPE_EXCEPTION === $error->getType();
        });
    }

    /**
     * Returns errors reported during linting after fixing.
     *
     * @return Error[]
     */
    public function getLintErrors(): array
    {
        return array_filter($this->errors, static function (Error $error) {
            return Error::TYPE_LINT === $error->getType();
        });
    }

    /**
     * Returns true if no errors were reported.
     */
    public function isEmpty(): bool
    {
        return empty($this->errors);
    }

    public function report(Error $error): void
    {
        $this->errors[] = $error;
    }
}
