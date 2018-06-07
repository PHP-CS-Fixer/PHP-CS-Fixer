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

namespace PhpCsFixer\Tests\Console;

use PhpCsFixer\Console\WarningsDetector;
use PhpCsFixer\Tests\TestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\WarningsDetector
 */
final class WarningsDetectorTest extends TestCase
{
    public function testDetectOldVendorNotInstalledByComposer()
    {
        $toolInfo = $this->prophesize('PhpCsFixer\ToolInfoInterface');
        $toolInfo->isInstalledByComposer()->willReturn(false);

        $warningsDetector = new WarningsDetector($toolInfo->reveal());
        $warningsDetector->detectOldVendor();

        $this->assertSame([], $warningsDetector->getWarnings());
    }

    public function testDetectOldVendorNotLegacyPackage()
    {
        $toolInfo = $this->prophesize('PhpCsFixer\ToolInfoInterface');
        $toolInfo->isInstalledByComposer()->willReturn(false);
        $toolInfo->getComposerInstallationDetails()->willReturn([
            'name' => 'friendsofphp/php-cs-fixer',
        ]);

        $warningsDetector = new WarningsDetector($toolInfo->reveal());
        $warningsDetector->detectOldVendor();

        $this->assertSame([], $warningsDetector->getWarnings());
    }

    public function testDetectOldVendorLegacyPackage()
    {
        $toolInfo = $this->prophesize('PhpCsFixer\ToolInfoInterface');
        $toolInfo->isInstalledByComposer()->willReturn(true);
        $toolInfo->getComposerInstallationDetails()->willReturn([
            'name' => 'fabpot/php-cs-fixer',
        ]);

        $warningsDetector = new WarningsDetector($toolInfo->reveal());
        $warningsDetector->detectOldVendor();

        $this->assertSame([
            'You are running PHP CS Fixer installed with old vendor `fabpot/php-cs-fixer`. Please update to `friendsofphp/php-cs-fixer`.',
            'If you need help while solving warnings, ask at https://gitter.im/PHP-CS-Fixer, we will help you!',
        ], $warningsDetector->getWarnings());
    }
}
