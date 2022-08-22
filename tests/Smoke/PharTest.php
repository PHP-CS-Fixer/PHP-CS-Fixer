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
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group covers-nothing
 *
 * @large
 */
final class PharTest extends AbstractSmokeTest
{
    /**
     * @var string
     */
    private static $pharCwd;

    /**
     * @var string
     */
    private static $pharName;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$pharCwd = __DIR__.'/../..';
        self::$pharName = 'php-cs-fixer.phar';

        if (!file_exists(self::$pharCwd.'/'.self::$pharName)) {
            static::markTestSkippedOrFail('No phar file available.');
        }
    }

    public function testVersion(): void
    {
        /** @phpstan-ignore-next-line to avoid `Ternary operator condition is always true|false.` */
        $shouldExpectCodename = Application::VERSION_CODENAME ? 1 : 0;

        static::assertMatchesRegularExpression(
            sprintf("/^PHP CS Fixer (?<version>%s)(?<git_sha> \\([a-z0-9]+\\))?(?<codename> %s){%d}(?<by> by .*)\nPHP runtime: (?<php_version>\\d\\.\\d+\\..*)$/", Application::VERSION, Application::VERSION_CODENAME, $shouldExpectCodename),
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
        ]);

        static::assertSame(
            $commandTester->getDisplay(),
            self::executePharCommand('describe header_comment')->getOutput()
        );
    }

    public function testFix(): void
    {
        static::assertSame(
            0,
            self::executePharCommand('fix src/Config.php -vvv --dry-run --diff --using-cache=no 2>&1')->getCode()
        );
    }

    public function testFixHelp(): void
    {
        static::assertSame(
            0,
            self::executePharCommand('fix --help')->getCode()
        );
    }

    private static function executePharCommand(string $params): CliResult
    {
        return CommandExecutor::create('php '.self::$pharName.' '.$params, self::$pharCwd)->getResult();
    }
}
