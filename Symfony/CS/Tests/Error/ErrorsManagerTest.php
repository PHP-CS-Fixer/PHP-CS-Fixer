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
    public function testDefaults()
    {
        $errorsManager = new ErrorsManager();

        $this->assertTrue($errorsManager->isEmpty());
        $this->assertEmpty($errorsManager->getInvalidFileErrors());
        $this->assertEmpty($errorsManager->getUnableToFixFileErrors());
    }

    public function testThatCanReportAndRetrieveInvalidFileErrors()
    {
        $error = new Error(
            Error::TYPE_INVALID,
            'bar'
        );

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getInvalidFileErrors();

        $this->assertInternalType('array', $errors);
        $this->assertCount(1, $errors);
        $this->assertSame($error, array_shift($errors));

        $this->assertCount(0, $errorsManager->getUnableToFixFileErrors());
    }

    /**
     * @dataProvider providerThatCanReportAndRetrieveUnableToFixFileErrors
     *
     * @param Error $error
     */
    public function testThatCanReportAndRetrieveUnableToFixFileErrors(Error $error)
    {
        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $errors = $errorsManager->getUnableToFixFileErrors();

        $this->assertInternalType('array', $errors);
        $this->assertCount(1, $errors);
        $this->assertSame($error, array_shift($errors));

        $this->assertCount(0, $errorsManager->getInvalidFileErrors());
    }

    public function providerThatCanReportAndRetrieveUnableToFixFileErrors()
    {
        return array(
            array(
                new Error(Error::TYPE_EXCEPTION, 'bar'),
            ),
            array(
                new Error(Error::TYPE_LINT, 'bar'),
            ),
        );
    }
}
