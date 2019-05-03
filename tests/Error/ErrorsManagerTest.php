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

namespace PhpCsFixer\Tests\Error;

use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Error\ErrorsManager
 */
final class ErrorsManagerTest extends TestCase
{
    public function testDefaults()
    {
        $errorsManager = new ErrorsManager();

        static::assertTrue($errorsManager->isEmpty());
        static::assertEmpty($errorsManager->getInvalidErrors());
        static::assertEmpty($errorsManager->getExceptionErrors());
        static::assertEmpty($errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveInvalidErrors()
    {
        $error = new Error(
            Error::TYPE_INVALID,
            'foo.php'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        static::assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getInvalidErrors();

        static::assertInternalType('array', $errors);
        static::assertCount(1, $errors);
        static::assertSame($error, array_shift($errors));

        static::assertCount(0, $errorsManager->getExceptionErrors());
        static::assertCount(0, $errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveExceptionErrors()
    {
        $error = new Error(
            Error::TYPE_EXCEPTION,
            'foo.php'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        static::assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getExceptionErrors();

        static::assertInternalType('array', $errors);
        static::assertCount(1, $errors);
        static::assertSame($error, array_shift($errors));

        static::assertCount(0, $errorsManager->getInvalidErrors());
        static::assertCount(0, $errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveInvalidFileErrors()
    {
        $error = new Error(
            Error::TYPE_LINT,
            'foo.php'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        static::assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getLintErrors();

        static::assertInternalType('array', $errors);
        static::assertCount(1, $errors);
        static::assertSame($error, array_shift($errors));

        static::assertCount(0, $errorsManager->getInvalidErrors());
        static::assertCount(0, $errorsManager->getExceptionErrors());
    }
}
