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

namespace PhpCsFixer\Tests\Smoke;

use Keradus\CliExecutor\CommandExecutor;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\Console\Command\HelpCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group covers-nothing
 * @large
 */
final class PharTest extends TestCase
{
    private static $pharCwd;
    private static $pharName;
    private static $pharPath;

    public static function setUpBeforeClass()
    {
        self::$pharCwd = __DIR__.'/../..';
        self::$pharName = 'php-cs-fixer.phar';
        self::$pharPath = self::$pharCwd.'/'.self::$pharName;

        if (!file_exists(self::$pharPath)) {
            static::markTestSkipped('No phar file available.');
        }
    }

    public function testVersion()
    {
        $this->assertRegExp(
            '/^.* '.Application::VERSION.' by .*$/',
            self::executePharCommand('--version')->getOutput()
        );
    }

    public function testReadme()
    {
        $this->assertSame(
            str_replace(
                HelpCOmmand::getLatestReleaseVersionFromChangeLog(),
                Application::VERSION,
                file_get_contents(__DIR__.'/../../README.rst')
            ),
            self::executePharCommand('readme')->getOutput()
        );
    }

    public function testDescribe()
    {
        $command = new DescribeCommand();

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => 'header_comment',
        ));

        $this->assertSame(
            $commandTester->getDisplay(),
            self::executePharCommand('describe header_comment')->getOutput()
        );
    }

    public function testFix()
    {
        $this->assertSame(
            0,
            self::executePharCommand('fix src/Config.php -vvv --dry-run --diff --using-cache=no 2>&1')->getCode()
        );
    }

    /**
     * @param string $params
     *
     * @return CliResult
     */
    private static function executePharCommand($params)
    {
        return CommandExecutor::create('php '.self::$pharName.' '.$params, self::$pharCwd)->getResult();
    }
}
