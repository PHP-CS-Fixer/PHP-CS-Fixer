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

final class ErrorTest extends \PHPUnit_Framework_TestCase
{
    public function testThatErrorTypeConstantValuesAreDifferent()
    {
        $this->assertNotSame(Error::TYPE_INVALID, Error::TYPE_EXCEPTION);
        $this->assertNotSame(Error::TYPE_EXCEPTION, Error::TYPE_LINT);
    }

    public function testConstructorSetsValues()
    {
        $type = 'foo';
        $filePath = 'foo.php';

        $error = new Error(
            $type,
            $filePath
        );

        $this->assertSame($type, $error->getType());
        $this->assertSame($filePath, $error->getFilePath());
    }
}
