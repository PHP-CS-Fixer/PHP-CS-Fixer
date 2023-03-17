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

namespace PhpCsFixer\Tests\Console\Output\Progress;

use PhpCsFixer\Console\Output\OutputContext;
use PhpCsFixer\Console\Output\Progress\OnlyMeaningfulOutput;
use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\Progress\OnlyMeaningfulOutput
 */
final class OnlyMeaningfulOutputTest extends FileInfoOutputTestCase
{
    /**
     * @param list<array{0: string, 1: FixerFileProcessedEvent::STATUS_*, 2?: list<string>}> $fileStatuses
     *
     * @dataProvider provideDotsProgressOutputCases
     */
    public function testDotsProgressOutput(array $fileStatuses, string $expectedOutput): void
    {
        $output = new BufferedOutput();

        $processOutput = new OnlyMeaningfulOutput(new OutputContext($output, 120, \count($fileStatuses)));

        $this->foreachStatus(
            $fileStatuses,
            static function (int $status, string $relativePath, array $appliedFixers = []) use ($processOutput): void {
                $processOutput->onFixerFileProcessed(new FixerFileProcessedEvent($status, $relativePath, $appliedFixers));
            }
        );

        self::assertSame($expectedOutput, $output->fetch());
    }

    /**
     * @return iterable<string, array{list<array{string, FixerFileProcessedEvent::STATUS_*}>, string}>
     */
    public static function provideDotsProgressOutputCases(): iterable
    {
        yield 'no meaningful statuses' => [[], ''];

        yield 'all meaningful statuses' => [
            [
                ['foo.php', FixerFileProcessedEvent::STATUS_FIXED],
                ['foo/bar.php', FixerFileProcessedEvent::STATUS_INVALID],
                ['baz.php', FixerFileProcessedEvent::STATUS_EXCEPTION],
                ['majin/boo.php', FixerFileProcessedEvent::STATUS_LINT],
            ],
            '[F] foo.php'.PHP_EOL.
            '[I] foo/bar.php'.PHP_EOL.
            '[E] baz.php'.PHP_EOL.
            '[E] majin/boo.php'.PHP_EOL,
        ];

        yield 'meaningful status with fixers applied' => [
            [
                ['foo.php', FixerFileProcessedEvent::STATUS_FIXED, ['a', 'b', 'c']],
            ],
            '[F] foo.php (a, b, c)'.PHP_EOL,
        ];

        yield 'non-meaningful statuses' => [
            [
                ['foo.php', FixerFileProcessedEvent::STATUS_NO_CHANGES],
                ['foo/bar.php', FixerFileProcessedEvent::STATUS_INVALID],
                ['baz.php', FixerFileProcessedEvent::STATUS_SKIPPED],
                ['majin/boo.php', FixerFileProcessedEvent::STATUS_LINT],
            ],
            '[I] foo/bar.php'.PHP_EOL.
            '[E] majin/boo.php'.PHP_EOL,
        ];
    }

    public function testSleep(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot serialize PhpCsFixer\Console\Output\Progress\OnlyMeaningfulOutput');

        $processOutput = new OnlyMeaningfulOutput(new OutputContext(new BufferedOutput(), 1, 1));
        $processOutput->__sleep();
    }

    public function testWakeup(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot unserialize PhpCsFixer\Console\Output\Progress\OnlyMeaningfulOutput');

        $processOutput = new OnlyMeaningfulOutput(new OutputContext(new BufferedOutput(), 1, 1));
        $processOutput->__wakeup();
    }
}
