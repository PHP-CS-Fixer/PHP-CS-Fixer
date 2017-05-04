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

namespace PhpCsFixer\Tests;

use PhpCsFixer\ToolInfo;
use PHPUnit\Framework\TestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\ToolInfo
 */
final class ToolInfoTest extends TestCase
{
    public function testGetVersion()
    {
        $this->assertInternalType('string', ToolInfo::getVersion());
    }

    public function testIsInstallAsPhar()
    {
        $this->assertFalse(ToolInfo::isInstalledAsPhar());
    }

    public function testIsInstalledByComposer()
    {
        $this->assertFalse(ToolInfo::isInstalledByComposer());
    }

    public function testGetComposerVersionThrowsExceptionIfOutsideComposerScope()
    {
        $this->setExpectedException(\LogicException::class);

        ToolInfo::getComposerVersion();
    }
}
