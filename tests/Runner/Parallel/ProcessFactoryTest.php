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

use PhpCsFixer\Console\Command\FixCommand;
use PhpCsFixer\Preg;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Runner\Parallel\Process;
use PhpCsFixer\Runner\Parallel\ProcessFactory;
use PhpCsFixer\Runner\Parallel\ProcessIdentifier;
use PhpCsFixer\Runner\RunnerConfig;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use React\EventLoop\StreamSelectLoop;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Parallel\ProcessFactory
 */
final class ProcessFactoryTest extends TestCase
{
    private InputDefinition $inputDefinition;

    protected function setUp(): void
    {
        $fixCommand = new FixCommand(new ToolInfo());
        $application = new Application();
        $application->addCommands([$fixCommand]);

        // In order to have full list of options supported by the command (e.g. `--verbose`)
        $fixCommand->mergeApplicationDefinition(false);

        $this->inputDefinition = $fixCommand->getDefinition();
    }

    /**
     * This test is not executed on Windows because process pipes are not supported there, due to their blocking nature
     * on this particular OS. The cause of this lays in `react/child-process` component, but it's related only to tests,
     * as parallel runner works properly on Windows too. Feel free to fiddle with it and add testing support for Windows.
     *
     * @requires OS Linux|Darwin
     *
     * @param array<string, string> $input
     *
     * @dataProvider provideCreateCases
     */
    public function testCreate(array $input, RunnerConfig $config, string $expectedAdditionalArgs): void
    {
        $factory = new ProcessFactory(new ArrayInput($input, $this->inputDefinition));
        $identifier = ProcessIdentifier::create();

        $process = $factory->create(new StreamSelectLoop(), $config, $identifier, 1_234);

        $command = \Closure::bind(static fn (Process $process): string => $process->command, null, Process::class)($process);

        // PHP binary and Fixer executable are not fixed, so we need to remove them from the command
        $command = Preg::replace('/^(.*php-cs-fixer[\'"]? )+(.+)/', '$2', $command);

        self::assertSame(
            trim(
                \sprintf(
                    'worker --port 1234 --identifier \'%s\' %s',
                    $identifier->toString(),
                    trim($expectedAdditionalArgs)
                )
            ),
            $command
        );

        $timeoutSeconds = \Closure::bind(static fn (Process $process): int => $process->timeoutSeconds, null, Process::class)($process);

        self::assertSame($config->getParallelConfig()->getProcessTimeout(), $timeoutSeconds);
    }

    /**
     * @return iterable<string, array{0: array<string, mixed>, 1: RunnerConfig, 2: string}>
     */
    public static function provideCreateCases(): iterable
    {
        yield 'no additional params' => [[], self::createRunnerConfig(false), ''];

        yield 'dry run' => [[], self::createRunnerConfig(true), '--dry-run'];

        yield 'diff enabled' => [['--diff' => true], self::createRunnerConfig(false), '--diff'];

        yield 'stop-on-violation enabled' => [['--stop-on-violation' => true], self::createRunnerConfig(false), '--stop-on-violation'];

        yield 'allow risky' => [['--allow-risky' => 'yes'], self::createRunnerConfig(false), '--allow-risky \'yes\''];

        yield 'config' => [['--config' => 'foo.php'], self::createRunnerConfig(false), '--config \'foo.php\''];

        yield 'rules' => [['--rules' => '@PhpCsFixer'], self::createRunnerConfig(false), '--rules \'@PhpCsFixer\''];

        yield 'using-cache' => [['--using-cache' => 'no'], self::createRunnerConfig(false), '--using-cache \'no\''];

        yield 'cache-file' => [
            ['--cache-file' => 'cache.json'],
            self::createRunnerConfig(false),
            '--cache-file \'cache.json\'',
        ];

        yield 'dry run with other options' => [
            [
                '--config' => 'conf.php',
                '--diff' => true,
                '--using-cache' => 'yes',
                '--stop-on-violation' => true,
            ],
            self::createRunnerConfig(true),
            '--dry-run --diff --stop-on-violation --config \'conf.php\' --using-cache \'yes\'',
        ];
    }

    private static function createRunnerConfig(bool $dryRun): RunnerConfig
    {
        return new RunnerConfig($dryRun, false, ParallelConfigFactory::sequential());
    }
}
