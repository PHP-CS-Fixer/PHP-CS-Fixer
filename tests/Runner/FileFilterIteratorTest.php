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

namespace PhpCsFixer\Tests\Runner;

use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Runner\FileFilterIterator;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\FileFilterIterator
 */
final class FileFilterIteratorTest extends TestCase
{
    /**
     * @dataProvider provideAcceptCases
     */
    public function testAccept(int $repeat): void
    {
        $file = __FILE__;
        $content = file_get_contents($file);
        $events = [];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            FixerFileProcessedEvent::NAME,
            static function (FixerFileProcessedEvent $event) use (&$events): void {
                $events[] = $event;
            }
        );

        $fileInfo = new \SplFileInfo($file);

        $filter = new FileFilterIterator(
            new \ArrayIterator(array_fill(0, $repeat, $fileInfo)),
            $eventDispatcher,
            $this->createCacheManagerDouble(true)
        );

        self::assertCount(0, $events);

        $files = iterator_to_array($filter);

        self::assertCount(1, $files);
        self::assertSame($fileInfo, reset($files));
    }

    /**
     * @return iterable<array{int}>
     */
    public static function provideAcceptCases(): iterable
    {
        yield [1];

        yield [2];

        yield [3];
    }

    public function testEmitSkipEventWhenCacheNeedFixingFalse(): void
    {
        $file = __FILE__;
        $content = file_get_contents($file);
        $events = [];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            FixerFileProcessedEvent::NAME,
            static function (FixerFileProcessedEvent $event) use (&$events): void {
                $events[] = $event;
            }
        );

        $filter = new FileFilterIterator(
            new \ArrayIterator([new \SplFileInfo($file)]),
            $eventDispatcher,
            $this->createCacheManagerDouble(false)
        );

        self::assertCount(0, $filter);
        self::assertCount(1, $events);

        /** @var FixerFileProcessedEvent $event */
        $event = reset($events);

        self::assertInstanceOf(FixerFileProcessedEvent::class, $event);
        self::assertSame(FixerFileProcessedEvent::STATUS_SKIPPED, $event->getStatus());
    }

    public function testIgnoreEmptyFile(): void
    {
        $file = __DIR__.'/../Fixtures/empty.php';
        $content = file_get_contents($file);
        $events = [];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            FixerFileProcessedEvent::NAME,
            static function (FixerFileProcessedEvent $event) use (&$events): void {
                $events[] = $event;
            }
        );

        $filter = new FileFilterIterator(
            new \ArrayIterator([new \SplFileInfo($file)]),
            $eventDispatcher,
            $this->createCacheManagerDouble(true)
        );

        self::assertCount(0, $filter);
        self::assertCount(1, $events);

        /** @var FixerFileProcessedEvent $event */
        $event = reset($events);

        self::assertInstanceOf(FixerFileProcessedEvent::class, $event);
        self::assertSame(FixerFileProcessedEvent::STATUS_SKIPPED, $event->getStatus());
    }

    public function testIgnore(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(
            FixerFileProcessedEvent::NAME,
            static function (): void {
                throw new \Exception('No event expected.');
            }
        );

        $filter = new FileFilterIterator(
            new \ArrayIterator([
                new \SplFileInfo(__DIR__),
                new \SplFileInfo('__INVALID__'),
            ]),
            $eventDispatcher,
            $this->createCacheManagerDouble(true)
        );

        self::assertCount(0, $filter);
    }

    public function testWithoutDispatcher(): void
    {
        $file = __FILE__;
        $content = file_get_contents($file);

        $filter = new FileFilterIterator(
            new \ArrayIterator([new \SplFileInfo($file)]),
            null,
            $this->createCacheManagerDouble(false)
        );

        self::assertCount(0, $filter);
    }

    public function testInvalidIterator(): void
    {
        $filter = new FileFilterIterator(
            new \ArrayIterator([__FILE__]), // @phpstan-ignore-line we want this check for contexts without static analysis
            null,
            $this->createCacheManagerDouble(true)
        );

        $this->expectException(
            \RuntimeException::class
        );
        $this->expectExceptionMessageMatches(
            '#^Expected instance of "\\\SplFileInfo", got "string"\.$#'
        );

        iterator_to_array($filter);
    }

    /**
     * @requires OS Linux|Darwin
     */
    public function testFileIsAcceptedAfterFilteredAsSymlink(): void
    {
        $link = __DIR__.'/../Fixtures/Test/FileFilterIteratorTest/FileFilterIteratorTest.php.link';

        self::assertTrue(is_link($link), 'Fixture data is no longer correct for this test.');
        self::assertSame(__FILE__, realpath($link), 'Fixture data is no longer correct for this test.');

        $file = new \SplFileInfo(__FILE__);
        $link = new \SplFileInfo($link);

        $filter = new FileFilterIterator(
            new \ArrayIterator([$link, $file]),
            null,
            $this->createCacheManagerDouble(true)
        );

        $files = iterator_to_array($filter);
        self::assertCount(1, $files);
        self::assertSame($file, reset($files));
    }

    private function createCacheManagerDouble(bool $needFixing): CacheManagerInterface
    {
        return new class($needFixing) implements CacheManagerInterface {
            private bool $needFixing;

            public function __construct(bool $needFixing)
            {
                $this->needFixing = $needFixing;
            }

            public function needFixing(string $file, string $fileContent): bool
            {
                return $this->needFixing;
            }

            public function setFile(string $file, string $fileContent): void
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
