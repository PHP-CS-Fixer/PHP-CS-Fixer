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

use PhpCsFixer\NullFileCacheManager;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class NullFileCacheManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFinal()
    {
        $reflection = new \ReflectionClass('PhpCsFixer\NullFileCacheManager');

        $this->assertTrue($reflection->isFinal());
    }

    public function testImplementsFileCacheManagerInterface()
    {
        $reflection = new \ReflectionClass('PhpCsFixer\NullFileCacheManager');

        $this->assertTrue($reflection->implementsInterface('PhpCsFixer\FileCacheManagerInterface'));
    }

    public function testNeedFixingReturnsTrue()
    {
        $manager = new NullFileCacheManager();

        $this->assertTrue($manager->needFixing('foo.php', 'bar'));
    }
}
