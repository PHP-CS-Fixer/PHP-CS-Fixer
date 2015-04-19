<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Error;
use Symfony\CS\ErrorsManager;

class ErrorsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testThatCanReportAndRetrieveExternalErrors()
    {
        $error = new Error\External('foo', 'bar', 'baz');

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $externalErrors = $errorsManager->getExternalErrors();

        $this->assertInternalType('array', $externalErrors);
        $this->assertCount(1, $externalErrors);
        $this->assertSame($error, array_shift($externalErrors));

        $this->assertCount(0, $errorsManager->getInternalErrors());
    }

    public function testThatCanReportAndRetrieveInternalErrors()
    {
        $error = new Error\Internal('foo', 'bar', 'baz');

        $errorsManager = new ErrorsManager();

        $errorsManager->report($error);

        $this->assertFalse($errorsManager->isEmpty());

        $internalErrors = $errorsManager->getInternalErrors();

        $this->assertInternalType('array', $internalErrors);
        $this->assertCount(1, $internalErrors);
        $this->assertSame($error, array_shift($internalErrors));

        $this->assertCount(0, $errorsManager->getExternalErrors());
    }
}
