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

namespace PhpCsFixer\Tests\Error;

use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Error\ErrorsManager
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ErrorsManagerTest extends TestCase
{
    public function testDefaults(): void
    {
        $errorsManager = new ErrorsManager();

        self::assertTrue($errorsManager->isEmpty());
        self::assertEmpty($errorsManager->getInvalidErrors());
        self::assertEmpty($errorsManager->getExceptionErrors());
        self::assertEmpty($errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveInvalidErrors(): void
    {
        $error = new Error(
            Error::TYPE_INVALID,
            'foo.php',
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        self::assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getInvalidErrors();

        self::assertCount(1, $errors);
        self::assertSame($error, array_shift($errors));

        self::assertCount(0, $errorsManager->getExceptionErrors());
        self::assertCount(0, $errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveExceptionErrors(): void
    {
        $error = new Error(
            Error::TYPE_EXCEPTION,
            'foo.php',
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        self::assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getExceptionErrors();

        self::assertCount(1, $errors);
        self::assertSame($error, array_shift($errors));

        self::assertCount(0, $errorsManager->getInvalidErrors());
        self::assertCount(0, $errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveInvalidFileErrors(): void
    {
        $error = new Error(
            Error::TYPE_LINT,
            'foo.php',
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        self::assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getLintErrors();

        self::assertCount(1, $errors);
        self::assertSame($error, array_shift($errors));

        self::assertCount(0, $errorsManager->getInvalidErrors());
        self::assertCount(0, $errorsManager->getExceptionErrors());
    }

    public function testThatCanReportAndRetrieveErrorsForSpecificPath(): void
    {
        $errorsManager = new ErrorsManager();

        // All kind of errors for the same path
        $errorsManager->report(new Error(Error::TYPE_LINT, 'foo.php'));
        $errorsManager->report(new Error(Error::TYPE_EXCEPTION, 'foo.php'));
        $errorsManager->report(new Error(Error::TYPE_INVALID, 'foo.php'));

        // Additional errors for a different path
        $errorsManager->report(new Error(Error::TYPE_INVALID, 'bar.php'));
        $errorsManager->report(new Error(Error::TYPE_LINT, 'baz.php'));

        self::assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->forPath('foo.php');

        self::assertCount(3, $errors);
    }
}
