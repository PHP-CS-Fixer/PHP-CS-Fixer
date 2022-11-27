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

namespace PhpCsFixer\Tests\Console\Command;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamException;
use org\bovigo\vfs\vfsStreamWrapper;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\SelfUpdateCommand;
use PhpCsFixer\Console\SelfUpdate\NewVersionChecker;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfoInterface;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\SelfUpdateCommand
 */
final class SelfUpdateCommandTest extends TestCase
{
    /**
     * @var null|vfsStreamDirectory
     */
    private $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = vfsStream::setup();

        file_put_contents($this->getToolPath(), 'Current PHP CS Fixer.');

        file_put_contents("{$this->root->url()}/{$this->getNewMinorReleaseVersion()}.phar", 'New minor version of PHP CS Fixer.');
        file_put_contents("{$this->root->url()}/{$this->getNewMajorReleaseVersion()}.phar", 'New major version of PHP CS Fixer.');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->root = null;

        try {
            vfsStreamWrapper::unregister();
        } catch (vfsStreamException $exception) {
            // ignored
        }
    }

    /**
     * @dataProvider provideCommandNameCases
     */
    public function testCommandName(string $name): void
    {
        $command = new SelfUpdateCommand(
            $this->prophesize(\PhpCsFixer\Console\SelfUpdate\NewVersionCheckerInterface::class)->reveal(),
            $this->createToolInfo(),
            $this->prophesize(\PhpCsFixer\PharCheckerInterface::class)->reveal()
        );

        $application = new Application();
        $application->add($command);

        static::assertSame($command, $application->find($name));
    }

    public static function provideCommandNameCases(): array
    {
        return [
            ['self-update'],
            ['selfupdate'],
        ];
    }

    /**
     * @param array<string, bool|string> $input
     *
     * @dataProvider provideExecuteCases
     */
    public function testExecute(
        string $latestVersion,
        ?string $latestMinorVersion,
        array $input,
        bool $decorated,
        string $expectedFileContents,
        string $expectedDisplay
    ): void {
        $versionChecker = $this->prophesize(\PhpCsFixer\Console\SelfUpdate\NewVersionCheckerInterface::class);

        $versionChecker->getLatestVersion()->willReturn($latestVersion);

        $versionChecker
            ->getLatestVersionOfMajor($this->getCurrentMajorVersion())
            ->willReturn($latestMinorVersion)
        ;

        $actualVersionCheck = new NewVersionChecker(
            $this->prophesize(\PhpCsFixer\Console\SelfUpdate\GithubClientInterface::class)->reveal()
        );

        $versionChecker
            ->compareVersions(Argument::type('string'), Argument::type('string'))
            ->will(function (array $arguments) use ($actualVersionCheck): int {
                return $actualVersionCheck->compareVersions($arguments[0], $arguments[1]);
            })
        ;

        $command = new SelfUpdateCommand(
            $versionChecker->reveal(),
            $this->createToolInfo(),
            $this->prophesize(\PhpCsFixer\PharCheckerInterface::class)->reveal()
        );

        $commandTester = $this->execute($command, $input, $decorated);

        static::assertSame($expectedFileContents, file_get_contents($this->getToolPath()));
        static::assertDisplay($expectedDisplay, $commandTester);
        static::assertSame(0, $commandTester->getStatusCode());
    }

    public function provideExecuteCases(): array
    {
        $currentVersion = Application::VERSION;
        $minorRelease = $this->getNewMinorReleaseVersion();
        $majorRelease = $this->getNewMajorReleaseVersion();
        $major = $this->getNewMajorVersion();

        $currentContents = 'Current PHP CS Fixer.';
        $minorContents = 'New minor version of PHP CS Fixer.';
        $majorContents = 'New major version of PHP CS Fixer.';

        $upToDateDisplay = "\033[32mPHP CS Fixer is already up-to-date.\033[39m\n";
        $newMinorDisplay = "\033[32mPHP CS Fixer updated\033[39m (\033[33m{$currentVersion}\033[39m -> \033[33m{$minorRelease}\033[39m)\n";
        $newMajorDisplay = "\033[32mPHP CS Fixer updated\033[39m (\033[33m{$currentVersion}\033[39m -> \033[33m{$majorRelease}\033[39m)\n";
        $majorInfoNoMinorDisplay = <<<OUTPUT
\033[32mA new major version of PHP CS Fixer is available\033[39m (\033[33m{$majorRelease}\033[39m)
\033[32mBefore upgrading please read\033[39m https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/{$majorRelease}/UPGRADE-v{$major}.md
\033[32mIf you are ready to upgrade run this command with\033[39m \033[33m-f\033[39m
\033[32mChecking for new minor/patch version...\033[39m
\033[32mNo minor update for PHP CS Fixer.\033[39m

OUTPUT;
        $majorInfoNewMinorDisplay = <<<OUTPUT
\033[32mA new major version of PHP CS Fixer is available\033[39m (\033[33m{$majorRelease}\033[39m)
\033[32mBefore upgrading please read\033[39m https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/{$majorRelease}/UPGRADE-v{$major}.md
\033[32mIf you are ready to upgrade run this command with\033[39m \033[33m-f\033[39m
\033[32mChecking for new minor/patch version...\033[39m
\033[32mPHP CS Fixer updated\033[39m (\033[33m{$currentVersion}\033[39m -> \033[33m{$minorRelease}\033[39m)

OUTPUT;

        return [
            // no new version available
            [Application::VERSION, Application::VERSION, [], true, $currentContents, $upToDateDisplay],
            [Application::VERSION, Application::VERSION, [], false, $currentContents, $upToDateDisplay],
            [Application::VERSION, Application::VERSION, ['--force' => true], true, $currentContents, $upToDateDisplay],
            [Application::VERSION, Application::VERSION, ['-f' => true], false, $currentContents, $upToDateDisplay],
            [Application::VERSION, Application::VERSION, ['--force' => true], true, $currentContents, $upToDateDisplay],
            [Application::VERSION, Application::VERSION, ['-f' => true], false, $currentContents, $upToDateDisplay],

            // new minor version available
            [$minorRelease, $minorRelease, [], true, $minorContents, $newMinorDisplay],
            [$minorRelease, $minorRelease, ['--force' => true], true, $minorContents, $newMinorDisplay],
            [$minorRelease, $minorRelease, ['-f' => true], true, $minorContents, $newMinorDisplay],
            [$minorRelease, $minorRelease, [], false, $minorContents, $newMinorDisplay],
            [$minorRelease, $minorRelease, ['--force' => true], false, $minorContents, $newMinorDisplay],
            [$minorRelease, $minorRelease, ['-f' => true], false, $minorContents, $newMinorDisplay],

            // new major version available
            [$majorRelease, Application::VERSION, [], true, $currentContents, $majorInfoNoMinorDisplay],
            [$majorRelease, Application::VERSION, [], false, $currentContents, $majorInfoNoMinorDisplay],
            [$majorRelease, Application::VERSION, ['--force' => true], true, $majorContents, $newMajorDisplay],
            [$majorRelease, Application::VERSION, ['-f' => true], false, $majorContents, $newMajorDisplay],
            [$majorRelease, Application::VERSION, ['--force' => true], true, $majorContents, $newMajorDisplay],
            [$majorRelease, Application::VERSION, ['-f' => true], false, $majorContents, $newMajorDisplay],

            // new minor version and new major version available
            [$majorRelease, $minorRelease, [], true, $minorContents, $majorInfoNewMinorDisplay],
            [$majorRelease, $minorRelease, [], false, $minorContents, $majorInfoNewMinorDisplay],
            [$majorRelease, $minorRelease, ['--force' => true], true, $majorContents, $newMajorDisplay],
            [$majorRelease, $minorRelease, ['-f' => true], false, $majorContents, $newMajorDisplay],
            [$majorRelease, $minorRelease, ['--force' => true], true, $majorContents, $newMajorDisplay],
            [$majorRelease, $minorRelease, ['-f' => true], false, $majorContents, $newMajorDisplay],

            // weird/unexpected versions
            ['v0.1.0', 'v0.1.0', [], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', 'v0.1.0', [], false, $currentContents, $upToDateDisplay],
            ['v0.1.0', 'v0.1.0', ['--force' => true], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', 'v0.1.0', ['-f' => true], false, $currentContents, $upToDateDisplay],
            ['v0.1.0', 'v0.1.0', ['--force' => true], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', 'v0.1.0', ['-f' => true], false, $currentContents, $upToDateDisplay],
            ['v0.1.0', null, [], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', null, [], false, $currentContents, $upToDateDisplay],
            ['v0.1.0', null, ['--force' => true], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', null, ['-f' => true], false, $currentContents, $upToDateDisplay],
            ['v0.1.0', null, ['--force' => true], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', null, ['-f' => true], false, $currentContents, $upToDateDisplay],
            ['v0.1.0', Application::VERSION, [], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', Application::VERSION, [], false, $currentContents, $upToDateDisplay],
            ['v0.1.0', Application::VERSION, ['--force' => true], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', Application::VERSION, ['-f' => true], false, $currentContents, $upToDateDisplay],
            ['v0.1.0', Application::VERSION, ['--force' => true], true, $currentContents, $upToDateDisplay],
            ['v0.1.0', Application::VERSION, ['-f' => true], false, $currentContents, $upToDateDisplay],
            [Application::VERSION, 'v0.1.0', [], true, $currentContents, $upToDateDisplay],
            [Application::VERSION, 'v0.1.0', [], false, $currentContents, $upToDateDisplay],
            [Application::VERSION, 'v0.1.0', ['--force' => true], true, $currentContents, $upToDateDisplay],
            [Application::VERSION, 'v0.1.0', ['-f' => true], false, $currentContents, $upToDateDisplay],
            [Application::VERSION, 'v0.1.0', ['--force' => true], true, $currentContents, $upToDateDisplay],
            [Application::VERSION, 'v0.1.0', ['-f' => true], false, $currentContents, $upToDateDisplay],
        ];
    }

    /**
     * @param array<string, bool|string> $input
     *
     * @dataProvider provideExecuteWhenNotAbleToGetLatestVersionsCases
     */
    public function testExecuteWhenNotAbleToGetLatestVersions(
        bool $latestVersionSuccess,
        bool $latestMinorVersionSuccess,
        array $input,
        bool $decorated
    ): void {
        $versionChecker = $this->prophesize(\PhpCsFixer\Console\SelfUpdate\NewVersionCheckerInterface::class);

        $newMajorVersion = $this->getNewMajorReleaseVersion();
        $versionChecker->getLatestVersion()->will(function () use ($latestVersionSuccess, $newMajorVersion): string {
            if ($latestVersionSuccess) {
                return $newMajorVersion;
            }

            throw new \RuntimeException('Foo.');
        });

        $newMinorVersion = $this->getNewMinorReleaseVersion();
        $versionChecker
            ->getLatestVersionOfMajor($this->getCurrentMajorVersion())
            ->will(function () use ($latestMinorVersionSuccess, $newMinorVersion): string {
                if ($latestMinorVersionSuccess) {
                    return $newMinorVersion;
                }

                throw new \RuntimeException('Foo.');
            })
        ;

        $command = new SelfUpdateCommand(
            $versionChecker->reveal(),
            $this->createToolInfo(),
            $this->prophesize(\PhpCsFixer\PharCheckerInterface::class)->reveal()
        );

        $commandTester = $this->execute($command, $input, $decorated);

        static::assertDisplay(
            "\033[37;41mUnable to determine newest version: Foo.\033[39;49m\n",
            $commandTester
        );
        static::assertSame(1, $commandTester->getStatusCode());
    }

    public static function provideExecuteWhenNotAbleToGetLatestVersionsCases(): array
    {
        return [
            [false, false, [], true],
            [false, false, ['--force' => true], true],
            [false, false, ['-f' => true], true],
            [false, false, [], false],
            [false, false, ['--force' => true], false],
            [false, false, ['-f' => true], false],
            [true, false, [], true],
            [true, false, ['--force' => true], true],
            [true, false, ['-f' => true], true],
            [true, false, [], false],
            [true, false, ['--force' => true], false],
            [true, false, ['-f' => true], false],
            [false, true, [], true],
            [false, true, ['--force' => true], true],
            [false, true, ['-f' => true], true],
            [false, true, [], false],
            [false, true, ['--force' => true], false],
            [false, true, ['-f' => true], false],
        ];
    }

    /**
     * @param array<string, bool|string> $input
     *
     * @dataProvider provideExecuteWhenNotInstalledAsPharCases
     */
    public function testExecuteWhenNotInstalledAsPhar(array $input, bool $decorated): void
    {
        $command = new SelfUpdateCommand(
            $this->prophesize(\PhpCsFixer\Console\SelfUpdate\NewVersionCheckerInterface::class)->reveal(),
            $this->createToolInfo(false),
            $this->prophesize(\PhpCsFixer\PharCheckerInterface::class)->reveal()
        );

        $commandTester = $this->execute($command, $input, $decorated);

        static::assertDisplay(
            "\033[37;41mSelf-update is available only for PHAR version.\033[39;49m\n",
            $commandTester
        );
        static::assertSame(1, $commandTester->getStatusCode());
    }

    public static function provideExecuteWhenNotInstalledAsPharCases(): array
    {
        return [
            [[], true],
            [['--force' => true], true],
            [['-f' => true], true],
            [[], false],
            [['--force' => true], false],
            [['-f' => true], false],
        ];
    }

    /**
     * @param array<string, bool|string> $input
     */
    private function execute(Command $command, array $input, bool $decorated): CommandTester
    {
        $application = new Application();
        $application->add($command);

        $input = ['command' => $command->getName()] + $input;

        $commandTester = new CommandTester($command);

        $realPath = $_SERVER['argv'][0];
        $_SERVER['argv'][0] = $this->getToolPath();

        $commandTester->execute($input, ['decorated' => $decorated]);

        $_SERVER['argv'][0] = $realPath;

        return $commandTester;
    }

    private static function assertDisplay(string $expectedDisplay, CommandTester $commandTester): void
    {
        if (!$commandTester->getOutput()->isDecorated()) {
            $expectedDisplay = preg_replace("/\033\\[(\\d+;)*\\d+m/", '', $expectedDisplay);
        }

        static::assertSame(
            $expectedDisplay,
            $commandTester->getDisplay(true)
        );
    }

    private function createToolInfo(bool $isInstalledAsPhar = true): ToolInfoInterface
    {
        $root = $this->root;

        $toolInfo = $this->prophesize(ToolInfoInterface::class);
        $toolInfo->isInstalledAsPhar()->willReturn($isInstalledAsPhar);
        $toolInfo
            ->getPharDownloadUri(Argument::type('string'))
            ->will(function (array $arguments) use ($root): string {
                return "{$root->url()}/{$arguments[0]}.phar";
            })
        ;

        return $toolInfo->reveal();
    }

    private function getToolPath(): string
    {
        return "{$this->root->url()}/php-cs-fixer";
    }

    private function getCurrentMajorVersion(): int
    {
        return (int) preg_replace('/^v?(\d+).*$/', '$1', Application::VERSION);
    }

    private function getNewMinorReleaseVersion(): string
    {
        return "{$this->getCurrentMajorVersion()}.999.0";
    }

    private function getNewMajorVersion(): int
    {
        return $this->getCurrentMajorVersion() + 1;
    }

    private function getNewMajorReleaseVersion(): string
    {
        return $this->getNewMajorVersion().'.0.0';
    }
}
