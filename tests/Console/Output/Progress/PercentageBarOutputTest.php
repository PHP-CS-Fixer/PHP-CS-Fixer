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
use PhpCsFixer\Console\Output\Progress\PercentageBarOutput;
use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\Progress\PercentageBarOutput
 */
final class PercentageBarOutputTest extends TestCase
{
    /**
     * @param list<array{0: FixerFileProcessedEvent::STATUS_*, 1?: int}> $statuses
     *
     * @dataProvider providePercentageBarProgressOutputCases
     */
    public function testPercentageBarProgressOutput(array $statuses, string $expectedOutput, int $width): void
    {
        $nbFiles = 0;
        $this->foreachStatus($statuses, static function () use (&$nbFiles): void {
            ++$nbFiles;
        });

        $output = new BufferedOutput();

        $processOutput = new PercentageBarOutput(new OutputContext($output, $width, $nbFiles));

        $this->foreachStatus($statuses, static function (int $status) use ($processOutput): void {
            $processOutput->onFixerFileProcessed(new FixerFileProcessedEvent($status));
        });

        self::assertSame($expectedOutput, rtrim($output->fetch()));
    }

    /**
     * @return iterable<int|string, array{0: list<array{0: FixerFileProcessedEvent::STATUS_*, 1?: int}>, 1: string, 2: int}>
     */
    public static function providePercentageBarProgressOutputCases(): iterable
    {
        yield [
            [
                [FixerFileProcessedEvent::STATUS_NO_CHANGES, 100],
            ],
            '   0/100 [░░░░░░░░░░░░░░░░░░░░░░░░░░░░]   0%'.PHP_EOL.
            ' 100/100 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%',
            80,
        ];
    }

    /**
     * @param list<array{0: FixerFileProcessedEvent::STATUS_*, 1?: int}> $statuses
     * @param \Closure(FixerFileProcessedEvent::STATUS_*): void          $action
     */
    private function foreachStatus(array $statuses, \Closure $action): void
    {
        foreach ($statuses as $status) {
            $multiplier = $status[1] ?? 1;
            $status = $status[0];

            for ($i = 0; $i < $multiplier; ++$i) {
                $action($status);
            }
        }
    }
}
