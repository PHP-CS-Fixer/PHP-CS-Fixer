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

use PhpCsFixer\PharChecker;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\PharChecker
 */
final class PharCheckerTest extends TestCase
{
    public function testPharChecker()
    {
        $checker = new PharChecker();
        $this->assertNull($checker->checkFileValidity(__DIR__.'/Fixtures/empty.phar'));
    }

    public function testPharCheckerInvalidFile()
    {
        $checker = new PharChecker();
        $this->assertStringStartsWith('Failed to create Phar instance.', $checker->checkFileValidity(__FILE__));
    }
}
