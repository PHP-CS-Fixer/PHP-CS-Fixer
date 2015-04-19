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

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstants()
    {
        $this->assertNotSame(Error::ERROR_TYPE_EXTERNAL, Error::ERROR_TYPE_INTERNAL);
    }

    public function testConstructorSetsValues()
    {
        $type = 'foo';
        $filePath = 'foo.php';
        $message = 'Can not unfoo';

        $error = new Error(
            $type,
            $filePath,
            $message
        );

        $this->assertSame($type, $error->getType());
        $this->assertSame($filePath, $error->getFilePath());
        $this->assertSame($message, $error->getMessage());
    }
}
