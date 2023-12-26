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

namespace PhpCsFixer\Tests\Cache;

use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\NullCacheManager
 */
final class NullCacheManagerTest extends TestCase
{
    public function testIsFinal(): void
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\NullCacheManager::class);

        self::assertTrue($reflection->isFinal());
    }

    public function testImplementsCacheManagerInterface(): void
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\NullCacheManager::class);

        self::assertTrue($reflection->implementsInterface(\PhpCsFixer\Cache\CacheManagerInterface::class));
    }

    public function testNeedFixingReturnsTrue(): void
    {
        $manager = new NullCacheManager();

        self::assertTrue($manager->needFixing('foo.php', 'bar'));

        $manager->setFile(__FILE__, 'XXX'); // no-op, should not raise an exception
    }
}
