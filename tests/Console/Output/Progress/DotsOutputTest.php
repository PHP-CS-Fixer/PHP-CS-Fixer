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
use PhpCsFixer\Console\Output\Progress\DotsOutput;
use PhpCsFixer\Runner\Event\FileProcessed;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\Progress\DotsOutput
 */
final class DotsOutputTest extends TestCase
{
    /**
     * @param list<array{0: FileProcessed::STATUS_*, 1?: int}> $statuses
     *
     * @dataProvider provideDotsProgressOutputCases
     */
    public function testDotsProgressOutput(array $statuses, string $expectedOutput, int $width): void
    {
        $nbFiles = 0;
        $this->foreachStatus($statuses, static function () use (&$nbFiles): void {
            ++$nbFiles;
        });

        $output = new BufferedOutput();

        $processOutput = new DotsOutput(new OutputContext($output, $width, $nbFiles));

        $this->foreachStatus($statuses, static function (int $status) use ($processOutput): void {
            $processOutput->onFixerFileProcessed(new FileProcessed($status));
        });

        self::assertSame($expectedOutput, $output->fetch());
    }

    /**
     * @return iterable<int, array{list<array{0: FileProcessed::STATUS_*, 1?: int}>, string, int}>
     */
    public static function provideDotsProgressOutputCases(): iterable
    {
        yield [
            [
                [FileProcessed::STATUS_NO_CHANGES, 4],
            ],
            '....                                                                4 / 4 (100%)',
            80,
        ];

        yield [
            [
                [FileProcessed::STATUS_NO_CHANGES],
                [FileProcessed::STATUS_FIXED],
                [FileProcessed::STATUS_NO_CHANGES, 4],
            ],
            '.F....                                                              6 / 6 (100%)',
            80,
        ];

        yield [
            [
                [FileProcessed::STATUS_NO_CHANGES, 65],
            ],
            '................................................................. 65 / 65 (100%)',
            80,
        ];

        yield [
            [
                [FileProcessed::STATUS_NO_CHANGES, 66],
            ],
            '................................................................. 65 / 66 ( 98%)'.\PHP_EOL
            .'.                                                                 66 / 66 (100%)',
            80,
        ];

        yield [
            [
                [FileProcessed::STATUS_NO_CHANGES, 66],
            ],
            '......................... 25 / 66 ( 38%)'.\PHP_EOL
            .'......................... 50 / 66 ( 76%)'.\PHP_EOL
            .'................          66 / 66 (100%)',
            40,
        ];

        yield [
            [
                [FileProcessed::STATUS_NO_CHANGES, 66],
            ],
            '..................................................................                    66 / 66 (100%)',
            100,
        ];

        yield [
            [
                [FileProcessed::STATUS_NO_CHANGES, 19],
                [FileProcessed::STATUS_EXCEPTION],
                [FileProcessed::STATUS_NO_CHANGES, 6],
                [FileProcessed::STATUS_LINT],
                [FileProcessed::STATUS_FIXED, 3],
                [FileProcessed::STATUS_NO_CHANGES, 50],
                [FileProcessed::STATUS_SKIPPED],
                [FileProcessed::STATUS_NO_CHANGES, 49],
                [FileProcessed::STATUS_INVALID],
                [FileProcessed::STATUS_NO_CHANGES],
                [FileProcessed::STATUS_INVALID],
                [FileProcessed::STATUS_NO_CHANGES, 40],
                [FileProcessed::STATUS_INVALID],
                [FileProcessed::STATUS_NO_CHANGES, 15],
                [FileProcessed::STATUS_NON_MONOLITHIC],
            ],
            '...................E......EFFF.................................  63 / 189 ( 33%)'.\PHP_EOL
            .'.................S............................................. 126 / 189 ( 67%)'.\PHP_EOL
            .'....I.I........................................I..............M 189 / 189 (100%)',
            80,
        ];

        yield [
            [
                [FileProcessed::STATUS_NO_CHANGES, 19],
                [FileProcessed::STATUS_EXCEPTION],
                [FileProcessed::STATUS_NO_CHANGES, 6],
                [FileProcessed::STATUS_LINT],
                [FileProcessed::STATUS_FIXED, 3],
                [FileProcessed::STATUS_NO_CHANGES, 50],
                [FileProcessed::STATUS_SKIPPED],
                [FileProcessed::STATUS_NO_CHANGES, 49],
                [FileProcessed::STATUS_INVALID],
                [FileProcessed::STATUS_NO_CHANGES],
                [FileProcessed::STATUS_INVALID],
                [FileProcessed::STATUS_NO_CHANGES, 40],
                [FileProcessed::STATUS_INVALID],
                [FileProcessed::STATUS_NO_CHANGES, 15],
            ],
            '...................E......EFFF..................................................S...................... 103 / 189 ( 54%)'.\PHP_EOL
            .'...........................I.I........................................I...............                  189 / 189 (100%)',
            120,
        ];
    }

    public function testSleep(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot serialize '.DotsOutput::class);

        $processOutput = new DotsOutput(new OutputContext(new BufferedOutput(), 1, 1));
        $processOutput->__sleep();
    }

    public function testWakeup(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot unserialize '.DotsOutput::class);

        $processOutput = new DotsOutput(new OutputContext(new BufferedOutput(), 1, 1));
        $processOutput->__wakeup();
    }

    /**
     * @param list<array{0: FileProcessed::STATUS_*, 1?: int}> $statuses
     * @param \Closure(FileProcessed::STATUS_*): void          $action
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
