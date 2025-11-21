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

namespace PhpCsFixer\Tests\Smoke;

use Keradus\CliExecutor\CliResult;
use Keradus\CliExecutor\CommandExecutor;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\Console\ConfigurationResolver;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group covers-nothing
 * @group legacy
 *
 * @large
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PharTest extends AbstractSmokeTestCase
{
    private static string $pharCwd;

    private static string $pharName;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$pharCwd = __DIR__.'/../..';
        self::$pharName = 'php-cs-fixer.phar';

        if (!file_exists(self::$pharCwd.'/'.self::$pharName)) {
            self::fail('No phar file available.');
        }
    }

    public function testVersion(): void
    {
        /** @phpstan-ignore-next-line to avoid `Ternary operator condition is always true|false.` */
        $shouldExpectCodename = Application::VERSION_CODENAME ? 1 : 0;

        self::assertMatchesRegularExpression(
            \sprintf("/^PHP CS Fixer (?<version>%s)(?<git_sha> \\([a-z0-9]+\\))?(?<codename> %s){%d}(?<by> by .*)\nPHP runtime: (?<php_version>\\d\\.\\d+\\..*)$/", Application::VERSION, Application::VERSION_CODENAME, $shouldExpectCodename),
            self::executePharCommand('--version')->getOutput()
        );
    }

    public function testDescribe(): void
    {
        $command = new DescribeCommand();

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'header_comment',
            '--config' => ConfigurationResolver::IGNORE_CONFIG_FILE,
        ]);

        self::assertSame(
            $commandTester->getDisplay(),
            self::executePharCommand('describe header_comment --config=-')->getOutput()
        );
    }

    public function testFixSequential(): void
    {
        // `--congig=-`, as sequential is default in current MAJOR
        $command = self::executePharCommand('fix src/Config.php -vvv --dry-run --diff --using-cache=no --config=- --sequential 2>&1');

        self::assertSame(0, $command->getCode());
        self::assertMatchesRegularExpression(
            '/Running analysis on 1 core sequentially/',
            $command->getOutput()
        );
    }

    public function testFixParallel(): void
    {
        $command = self::executePharCommand('fix src/Config.php -vvv --dry-run --diff --using-cache=no --config='.__DIR__.'/../Fixtures/.php-cs-fixer.parallel.php'.' 2>&1');

        self::assertSame(0, $command->getCode());
        self::assertMatchesRegularExpression(
            '/Running analysis on [0-9]+ cores with [0-9]+ files per process/',
            $command->getOutput()
        );
    }

    public function testFixHelp(): void
    {
        self::assertSame(
            0,
            self::executePharCommand('fix --help')->getCode()
        );
    }

    /**
     * @dataProvider provideReportCases
     */
    public function testReport(string $usingCache): void
    {
        try {
            $json = self::executePharCommand(\sprintf(
                'fix %s --dry-run --sequential --format=json --rules=\'%s\' --using-cache=%s --config=-',
                __FILE__,
                json_encode(['concat_space' => ['spacing' => 'one']], \JSON_THROW_ON_ERROR),
                $usingCache,
            ))->getOutput();

            self::assertJson($json);

            $report = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
            self::assertIsArray($report);
            self::assertArrayHasKey('files', $report);
            self::assertCount(1, $report['files']);
            self::assertArrayHasKey(0, $report['files']);

            self::assertSame(
                'tests/Smoke/PharTest.php',
                $report['files'][0]['name'],
            );
        } catch (\Throwable $exception) {
            throw $exception;
        } finally {
            $cacheFile = __DIR__.'/../../.php-cs-fixer.cache';
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
        }
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideReportCases(): iterable
    {
        yield ['no'];

        yield ['yes'];
    }

    private static function executePharCommand(string $params): CliResult
    {
        return CommandExecutor::create('php '.self::$pharName.' '.$params, self::$pharCwd)->getResult(false);
    }
}
