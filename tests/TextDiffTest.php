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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Console\Report\FixReport\ReporterFactory;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 *
 * @group covers-nothing
 */
final class TextDiffTest extends TestCase
{
    /**
     * @dataProvider provideDiffReportingDecoratedCases
     */
    public function testDiffReportingDecorated(string $expected, string $format, bool $isDecorated): void
    {
        $command = new FixCommand(new ToolInfo());
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'path' => [__DIR__.'/Fixtures/FixCommand/TextDiffTestInput.php'],
                '--diff' => true,
                '--dry-run' => true,
                '--format' => $format,
                '--rules' => 'cast_spaces',
                '--using-cache' => 'no',
            ],
            [
                'decorated' => $isDecorated,
                'verbosity' => OutputInterface::VERBOSITY_NORMAL,
            ]
        );

        if ($isDecorated !== $commandTester->getOutput()->isDecorated()) {
            self::markTestSkipped(\sprintf('Output should %sbe decorated.', $isDecorated ? '' : 'not '));
        }

        if ($isDecorated !== $commandTester->getOutput()->getFormatter()->isDecorated()) {
            self::markTestSkipped(\sprintf('Formatter should %sbe decorated.', $isDecorated ? '' : 'not '));
        }

        self::assertStringMatchesFormat($expected, $commandTester->getDisplay(false));
    }

    /**
     * @return iterable<int, array{string, string, bool}>
     */
    public static function provideDiffReportingDecoratedCases(): iterable
    {
        $expected = <<<'TEST'
            %A$output->writeln('<error>'.(int)$output.'</error>');%A
            %A$output->writeln('<error>'.(int) $output.'</error>');%A
            %A$output->writeln('<error> TEST </error>');%A
            %A$output->writeln('<error>'.(int)$output.'</error>');%A
            %A$output->writeln('<error>'.(int) $output.'</error>');%A
            TEST;

        foreach (['txt', 'xml', 'junit'] as $format) {
            yield [$expected, $format, true];

            yield [$expected, $format, false];
        }

        $expected = substr(json_encode($expected, \JSON_THROW_ON_ERROR), 1, -1);

        yield [$expected, 'json', true];

        yield [$expected, 'json', false];
    }

    /**
     * Test to make sure @see TextDiffTest::provideDiffReportingCases covers all formats.
     */
    public function testAllFormatsCovered(): void
    {
        $factory = new ReporterFactory();
        $formats = $factory->registerBuiltInReporters()->getFormats();
        sort($formats);

        self::assertSame(
            ['checkstyle', 'gitlab', 'json', 'junit', 'txt', 'xml'],
            $formats
        );
    }
}
