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

use Symfony\CS\Error;

class InternalTest extends \PHPUnit_Framework_TestCase
{
    public function testConstants()
    {
        $this->assertSame(1, Error\Internal::ERROR_TYPE_EXCEPTION);
        $this->assertSame(2, Error\Internal::ERROR_TYPE_LINT);
    }

    public function testConstructorSetsValues()
    {
        $type = 'foo';
        $filePath = 'foo.php';
        $message = 'Can not unfoo';

        $internalError = new Error\Internal(
            $type,
            $filePath,
            $message
        );

        $this->assertSame($type, $internalError->getType());
        $this->assertSame($filePath, $internalError->getFilePath());
        $this->assertSame($message, $internalError->getMessage());
    }
}
