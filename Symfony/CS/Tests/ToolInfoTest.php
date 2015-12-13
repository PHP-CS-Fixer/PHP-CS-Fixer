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

use Symfony\CS\ToolInfo;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class ToolInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetVersion()
    {
        $this->assertInternalType('string', ToolInfo::getVersion());
    }

    public function testIsInstallAsPhar()
    {
        $this->assertFalse(ToolInfo::isInstalledAsPhar());
    }
}
