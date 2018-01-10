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
        $toolInfo = new ToolInfo();
        $this->assertInternalType('string', $toolInfo->getVersion());
    }

    public function testIsInstallAsPhar()
    {
        $toolInfo = new ToolInfo();
        $this->assertFalse($toolInfo->isInstalledAsPhar());
    }

    public function testIsInstalledByComposer()
    {
        $toolInfo = new ToolInfo();
        $this->assertFalse($toolInfo->isInstalledByComposer());
    }

    public function testGetComposerVersionThrowsExceptionIfOutsideComposerScope()
    {
        $toolInfo = new ToolInfo();

        $this->setExpectedException('LogicException');

        $toolInfo->getComposerVersion();
    }

    public function testGetPharDownloadUri()
    {
        $toolInfo = new ToolInfo();
        $this->assertSame(
            'https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/foo/php-cs-fixer.phar',
            $toolInfo->getPharDownloadUri('foo')
        );
    }
}
