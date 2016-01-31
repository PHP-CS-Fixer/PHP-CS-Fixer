<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

use PhpCsFixer\Console\Application;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testApplication()
    {
        $app = new Application();
        $this->assertStringMatchesFormat('<info>PHP CS Fixer</info> version <comment>%d.%d%s</comment> by <comment>Fabien Potencier</comment>', $app->getLongVersion());
    }
}
