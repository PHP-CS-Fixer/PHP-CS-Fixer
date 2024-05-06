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

namespace PhpCsFixer\Tests\Runner\Parallel;

use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\Runner\Parallel\ReadonlyCacheManager;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Parallel\ReadonlyCacheManager
 */
final class ReadonlyCacheManagerTest extends TestCase
{
    /**
     * @dataProvider provideNeedFixingCases
     */
    public function testNeedFixing(bool $needsFixing): void
    {
        $cacheManager = new ReadonlyCacheManager($this->getInnerCacheManager($needsFixing));

        self::assertSame($needsFixing, $cacheManager->needFixing('foo.php', '<?php # Nothing'));
    }

    /**
     * This test ensures that the inner cache manager's `setFile()` is not called.
     *
     * @doesNotPerformAssertions
     */
    public function testSetFile(): void
    {
        try {
            $cacheManager = new ReadonlyCacheManager($this->getInnerCacheManager(false));
            $cacheManager->setFile('foo.php', '<?php # Nothing');
        } catch (\Throwable $e) {
            self::fail('Inner cache manager should not be called');
        }
    }

    /**
     * @return iterable<array{0: bool}>
     */
    public static function provideNeedFixingCases(): iterable
    {
        yield [true];

        yield [false];
    }

    private function getInnerCacheManager(bool $needsFixing): CacheManagerInterface
    {
        return new class($needsFixing) implements CacheManagerInterface {
            private bool $needsFixing;

            public function __construct(bool $needsFixing)
            {
                $this->needsFixing = $needsFixing;
            }

            public function needFixing(string $file, string $fileContent): bool
            {
                return $this->needsFixing;
            }

            public function setFile(string $file, string $fileContent): void
            {
                throw new \LogicException('Should not be called.');
            }

            public function setFileHash(string $file, string $hash): void
            {
                throw new \LogicException('Should not be called.');
            }
        };
    }
}
