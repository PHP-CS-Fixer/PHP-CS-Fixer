<?php

declare(strict_types=1);

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

use PhpCsFixer\Console\Application;
use PhpCsFixer\ToolInfo;

/**
 * @internal
 *
 * @covers \PhpCsFixer\ToolInfo
 */
final class ToolInfoTest extends TestCase
{
    public function testGetVersion(): void
    {
        $toolInfo = new ToolInfo();
        self::assertStringStartsWith(Application::VERSION, $toolInfo->getVersion());
    }

    public function testIsInstallAsPhar(): void
    {
        $toolInfo = new ToolInfo();
        self::assertFalse($toolInfo->isInstalledAsPhar());
    }

    public function testIsInstalledByComposer(): void
    {
        $toolInfo = new ToolInfo();
        self::assertFalse($toolInfo->isInstalledByComposer());
    }

    public function testGetComposerVersionThrowsExceptionIfOutsideComposerScope(): void
    {
        $toolInfo = new ToolInfo();

        $this->expectException(\LogicException::class);

        $toolInfo->getComposerVersion();
    }

    public function testGetPharDownloadUri(): void
    {
        $toolInfo = new ToolInfo();
        self::assertSame(
            'https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/foo/php-cs-fixer.phar',
            $toolInfo->getPharDownloadUri('foo')
        );
    }
}
