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

class ErrorsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testThatCanReportAndRetrieveLintingErrors()
    {
        $error = new Error(
            Error::TYPE_LINTING,
            'bar'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getLintingErrors();

        $this->assertInternalType('array', $errors);
        $this->assertCount(1, $errors);
        $this->assertSame($error, array_shift($errors));

        $this->assertCount(0, $errorsManager->getFixingErrors());
    }

    public function testThatCanReportAndRetrieveInternalErrors()
    {
        $error = new Error(
            Error::TYPE_FIXING,
            'bar'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getFixingErrors();

        $this->assertInternalType('array', $errors);
        $this->assertCount(1, $errors);
        $this->assertSame($error, array_shift($errors));

        $this->assertCount(0, $errorsManager->getLintingErrors());
    }
}
