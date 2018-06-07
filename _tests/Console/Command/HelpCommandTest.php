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

namespace PhpCsFixer\Tests\Console\Command;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\HelpCommand
 */
final class HelpCommandTest extends TestCase
{
    public function testGetLatestReleaseVersionFromChangeLog()
    {
        $helpVersion = HelpCommand::getLatestReleaseVersionFromChangeLog();
        $appVersion = Application::VERSION;
        $this->assertTrue(
            version_compare($helpVersion, $appVersion, '<='),
            sprintf(
                'Expected version from change log "%s" <= as application version "%s".',
                $helpVersion,
                $appVersion
            )
        );
    }
}
