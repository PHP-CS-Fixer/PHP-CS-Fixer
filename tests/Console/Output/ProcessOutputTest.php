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

namespace PhpCsFixer\Tests\Console\Output;

use PhpCsFixer\Console\Output\ProcessOutput;
use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @internal
 */
final class ProcessOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array  $statuses
     * @param string $expectedOutput
     *
     * @dataProvider getProcessProgressOutputCases
     */
    public function testProcessProgressOutput(array $statuses, $expectedOutput)
    {
        $processOutput = new ProcessOutput(
            $output = new BufferedOutput(),
            $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher')->reveal(),
            null
        );

        $this->foreachStatus($statuses, function ($status) use ($processOutput) {
            $processOutput->onFixerFileProcessed(new FixerFileProcessedEvent($status));
        });

        $this->assertSame($expectedOutput, $output->fetch());
    }

    public function getProcessProgressOutputCases()
    {
        return array(
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 4),
                ),
                '....',
            ),
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES),
                    array(FixerFileProcessedEvent::STATUS_FIXED),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 4),
                ),
                '.F....',
            ),
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 65),
                ),
                '.................................................................',
            ),
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 81),
                ),
                '.................................................................................',
            ),
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 19),
                    array(FixerFileProcessedEvent::STATUS_EXCEPTION),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 6),
                    array(FixerFileProcessedEvent::STATUS_LINT),
                    array(FixerFileProcessedEvent::STATUS_FIXED, 3),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 67),
                    array(FixerFileProcessedEvent::STATUS_SKIPPED),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 66),
                    array(FixerFileProcessedEvent::STATUS_INVALID),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES),
                    array(FixerFileProcessedEvent::STATUS_INVALID),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 40),
                    array(FixerFileProcessedEvent::STATUS_UNKNOWN),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 32),
                ),
                '...................E......EFFF...................................................................S..................................................................I.I........................................?................................',
            ),
        );
    }

    /**
     * @param array  $statuses
     * @param string $expectedOutput
     *
     * @dataProvider getProcessProgressOutputWithNumbersCases
     */
    public function testProcessProgressOutputWithNumbers(array $statuses, $expectedOutput)
    {
        $nbFiles = 0;
        $this->foreachStatus($statuses, function ($status) use (&$nbFiles) {
            ++$nbFiles;
        });

        $processOutput = new ProcessOutput(
            $output = new BufferedOutput(),
            $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher')->reveal(),
            $nbFiles
        );

        $this->foreachStatus($statuses, function ($status) use ($processOutput) {
            $processOutput->onFixerFileProcessed(new FixerFileProcessedEvent($status));
        });

        $this->assertSame($expectedOutput, $output->fetch());
    }

    public function getProcessProgressOutputWithNumbersCases()
    {
        return array(
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 4),
                ),
                '....                                                                4 / 4 (100%)',
            ),
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES),
                    array(FixerFileProcessedEvent::STATUS_FIXED),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 4),
                ),
                '.F....                                                              6 / 6 (100%)',
            ),
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 65),
                ),
                '................................................................. 65 / 65 (100%)',
            ),
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 66),
                ),
                '................................................................. 65 / 66 ( 98%)'.PHP_EOL.
                '.                                                                 66 / 66 (100%)',
            ),
            array(
                array(
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 19),
                    array(FixerFileProcessedEvent::STATUS_EXCEPTION),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 6),
                    array(FixerFileProcessedEvent::STATUS_LINT),
                    array(FixerFileProcessedEvent::STATUS_FIXED, 3),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 50),
                    array(FixerFileProcessedEvent::STATUS_SKIPPED),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 49),
                    array(FixerFileProcessedEvent::STATUS_INVALID),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES),
                    array(FixerFileProcessedEvent::STATUS_INVALID),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 40),
                    array(FixerFileProcessedEvent::STATUS_UNKNOWN),
                    array(FixerFileProcessedEvent::STATUS_NO_CHANGES, 15),
                ),
                '...................E......EFFF.................................  63 / 189 ( 33%)'.PHP_EOL.
                '.................S............................................. 126 / 189 ( 67%)'.PHP_EOL.
                '....I.I........................................?............... 189 / 189 (100%)',
            ),
        );
    }

    private function foreachStatus(array $statuses, \Closure $action)
    {
        foreach ($statuses as $status) {
            $multiplier = isset($status[1]) ? $status[1] : 1;
            $status = $status[0];

            for ($i = 0; $i < $multiplier; ++$i) {
                $action($status);
            }
        }
    }
}
