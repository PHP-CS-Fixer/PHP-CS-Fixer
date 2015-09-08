<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Error;

use Symfony\CS\Error\Error;
use Symfony\CS\Error\ErrorsManager;

final class ErrorsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $errorsManager = new ErrorsManager();

        $this->assertTrue($errorsManager->isEmpty());
        $this->assertEmpty($errorsManager->getInvalidErrors());
        $this->assertEmpty($errorsManager->getExceptionErrors());
        $this->assertEmpty($errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveInvalidErrors()
    {
        $error = new Error(
            Error::TYPE_INVALID,
            'foo.php'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getInvalidErrors();

        $this->assertInternalType('array', $errors);
        $this->assertCount(1, $errors);
        $this->assertSame($error, array_shift($errors));

        $this->assertCount(0, $errorsManager->getExceptionErrors());
        $this->assertCount(0, $errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveExceptionErrors()
    {
        $error = new Error(
            Error::TYPE_EXCEPTION,
            'foo.php'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getExceptionErrors();

        $this->assertInternalType('array', $errors);
        $this->assertCount(1, $errors);
        $this->assertSame($error, array_shift($errors));

        $this->assertCount(0, $errorsManager->getInvalidErrors());
        $this->assertCount(0, $errorsManager->getLintErrors());
    }

    public function testThatCanReportAndRetrieveInvalidFileErrors()
    {
        $error = new Error(
            Error::TYPE_LINT,
            'foo.php'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getLintErrors();

        $this->assertInternalType('array', $errors);
        $this->assertCount(1, $errors);
        $this->assertSame($error, array_shift($errors));

        $this->assertCount(0, $errorsManager->getInvalidErrors());
        $this->assertCount(0, $errorsManager->getExceptionErrors());
    }
}
