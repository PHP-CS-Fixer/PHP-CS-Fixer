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
use PhpCsFixer\Console\SelfUpdate\GithubClientInterface;
use PhpCsFixer\Console\SelfUpdate\NewVersionChecker;
use PhpCsFixer\Console\SelfUpdate\NewVersionCheckerInterface;
use PhpCsFixer\PharCheckerInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfoInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Console\Command\SelfUpdateCommand
 */
final class SelfUpdateCommandTest extends TestCase
{
    private ?vfsStreamDirectory $root = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = vfsStream::setup();

        file_put_contents($this->getToolPath(), 'Current PHP CS Fixer.');

        file_put_contents($this->root->url().'/'.self::getNewMinorReleaseVersion().'.phar', 'New minor version of PHP CS Fixer.');
        file_put_contents($this->root->url().'/'.self::getNewMajorReleaseVersion().'.phar', 'New major version of PHP CS Fixer.');
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
            $this->createNewVersionCheckerDouble(),
            $this->createToolInfoDouble(),
            $this->createPharCheckerDouble(),
        );

        $application = new Application();
        $application->add($command);

        self::assertSame($command, $application->find($name));
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideCommandNameCases(): iterable
    {
        yield ['self-update'];

        yield ['selfupdate'];
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
        $versionChecker = $this->createNewVersionCheckerDouble($latestVersion, $latestMinorVersion);

        $command = new SelfUpdateCommand(
            $versionChecker,
            $this->createToolInfoDouble(),
            $this->createPharCheckerDouble(),
        );

        $commandTester = $this->execute($command, $input, $decorated);

        self::assertSame($expectedFileContents, file_get_contents($this->getToolPath()));
        self::assertDisplay($expectedDisplay, $commandTester);
        self::assertSame(0, $commandTester->getStatusCode());
    }

    /**
     * @return iterable<int, array{string, null|string, array<string, bool|string>, bool, string, string}>
     */
    public static function provideExecuteCases(): iterable
    {
        $currentVersion = Application::VERSION;
        $minorRelease = self::getNewMinorReleaseVersion();
        $majorRelease = self::getNewMajorReleaseVersion();
        $major = self::getNewMajorVersion();

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

        // no new version available
        yield [Application::VERSION, Application::VERSION, [], true, $currentContents, $upToDateDisplay];

        yield [Application::VERSION, Application::VERSION, [], false, $currentContents, $upToDateDisplay];

        yield [Application::VERSION, Application::VERSION, ['--force' => true], true, $currentContents, $upToDateDisplay];

        yield [Application::VERSION, Application::VERSION, ['-f' => true], false, $currentContents, $upToDateDisplay];

        // new minor version available
        yield [$minorRelease, $minorRelease, [], true, $minorContents, $newMinorDisplay];

        yield [$minorRelease, $minorRelease, ['--force' => true], true, $minorContents, $newMinorDisplay];

        yield [$minorRelease, $minorRelease, ['-f' => true], true, $minorContents, $newMinorDisplay];

        yield [$minorRelease, $minorRelease, [], false, $minorContents, $newMinorDisplay];

        yield [$minorRelease, $minorRelease, ['--force' => true], false, $minorContents, $newMinorDisplay];

        yield [$minorRelease, $minorRelease, ['-f' => true], false, $minorContents, $newMinorDisplay];

        // new major version available
        yield [$majorRelease, Application::VERSION, [], true, $currentContents, $majorInfoNoMinorDisplay];

        yield [$majorRelease, Application::VERSION, [], false, $currentContents, $majorInfoNoMinorDisplay];

        yield [$majorRelease, Application::VERSION, ['--force' => true], true, $majorContents, $newMajorDisplay];

        yield [$majorRelease, Application::VERSION, ['-f' => true], false, $majorContents, $newMajorDisplay];

        // new minor version and new major version available
        yield [$majorRelease, $minorRelease, [], true, $minorContents, $majorInfoNewMinorDisplay];

        yield [$majorRelease, $minorRelease, [], false, $minorContents, $majorInfoNewMinorDisplay];

        yield [$majorRelease, $minorRelease, ['--force' => true], true, $majorContents, $newMajorDisplay];

        yield [$majorRelease, $minorRelease, ['-f' => true], false, $majorContents, $newMajorDisplay];

        // weird/unexpected versions
        yield ['v0.1.0', 'v0.1.0', [], true, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', 'v0.1.0', [], false, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', 'v0.1.0', ['--force' => true], true, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', 'v0.1.0', ['-f' => true], false, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', null, [], true, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', null, [], false, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', null, ['--force' => true], true, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', null, ['-f' => true], false, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', Application::VERSION, [], true, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', Application::VERSION, [], false, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', Application::VERSION, ['--force' => true], true, $currentContents, $upToDateDisplay];

        yield ['v0.1.0', Application::VERSION, ['-f' => true], false, $currentContents, $upToDateDisplay];

        yield [Application::VERSION, 'v0.1.0', [], true, $currentContents, $upToDateDisplay];

        yield [Application::VERSION, 'v0.1.0', [], false, $currentContents, $upToDateDisplay];

        yield [Application::VERSION, 'v0.1.0', ['--force' => true], true, $currentContents, $upToDateDisplay];

        yield [Application::VERSION, 'v0.1.0', ['-f' => true], false, $currentContents, $upToDateDisplay];
    }

    /**
     * @param array<string, bool|string> $input
     *
     * @dataProvider provideExecuteWhenNotAbleToGetLatestVersionsCases
     */
    public function testExecuteWhenNotAbleToGetLatestVersions(
        bool $latestMajorVersionSuccess,
        bool $latestMinorVersionSuccess,
        array $input,
        bool $decorated
    ): void {
        $versionChecker = $this->createNewVersionCheckerDouble(
            self::getNewMajorReleaseVersion(),
            self::getNewMinorReleaseVersion(),
            $latestMajorVersionSuccess,
            $latestMinorVersionSuccess,
        );

        $command = new SelfUpdateCommand(
            $versionChecker,
            $this->createToolInfoDouble(),
            $this->createPharCheckerDouble(),
        );

        $commandTester = $this->execute($command, $input, $decorated);

        self::assertDisplay(
            "\033[37;41mUnable to determine newest version: Foo.\033[39;49m\n",
            $commandTester
        );
        self::assertSame(1, $commandTester->getStatusCode());
    }

    /**
     * @return iterable<int, array{bool, bool, array<string, bool|string>, bool}>
     */
    public static function provideExecuteWhenNotAbleToGetLatestVersionsCases(): iterable
    {
        yield [false, false, [], true];

        yield [false, false, ['--force' => true], true];

        yield [false, false, ['-f' => true], true];

        yield [false, false, [], false];

        yield [false, false, ['--force' => true], false];

        yield [false, false, ['-f' => true], false];

        yield [true, false, [], true];

        yield [true, false, ['--force' => true], true];

        yield [true, false, ['-f' => true], true];

        yield [true, false, [], false];

        yield [true, false, ['--force' => true], false];

        yield [true, false, ['-f' => true], false];

        yield [false, true, [], true];

        yield [false, true, ['--force' => true], true];

        yield [false, true, ['-f' => true], true];

        yield [false, true, [], false];

        yield [false, true, ['--force' => true], false];

        yield [false, true, ['-f' => true], false];
    }

    /**
     * @param array<string, bool|string> $input
     *
     * @dataProvider provideExecuteWhenNotInstalledAsPharCases
     */
    public function testExecuteWhenNotInstalledAsPhar(array $input, bool $decorated): void
    {
        $command = new SelfUpdateCommand(
            $this->createNewVersionCheckerDouble(),
            $this->createToolInfoDouble(false),
            $this->createPharCheckerDouble(),
        );

        $commandTester = $this->execute($command, $input, $decorated);

        self::assertDisplay(
            "\033[37;41mSelf-update is available only for PHAR version.\033[39;49m\n",
            $commandTester
        );
        self::assertSame(1, $commandTester->getStatusCode());
    }

    /**
     * @return iterable<int, array{array<string, bool|string>, bool}>
     */
    public static function provideExecuteWhenNotInstalledAsPharCases(): iterable
    {
        yield [[], true];

        yield [['--force' => true], true];

        yield [['-f' => true], true];

        yield [[], false];

        yield [['--force' => true], false];

        yield [['-f' => true], false];
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

        \assert(\array_key_exists('argv', $_SERVER));
        $realPath = $_SERVER['argv'][0];
        $_SERVER['argv'][0] = $this->getToolPath();

        $commandTester->execute($input, ['decorated' => $decorated]);

        $_SERVER['argv'][0] = $realPath;

        return $commandTester;
    }

    private static function assertDisplay(string $expectedDisplay, CommandTester $commandTester): void
    {
        if (!$commandTester->getOutput()->isDecorated()) {
            $expectedDisplay = Preg::replace("/\033\\[(\\d+;)*\\d+m/", '', $expectedDisplay);
        }

        self::assertSame(
            $expectedDisplay,
            $commandTester->getDisplay(true)
        );
    }

    private function createToolInfoDouble(bool $isInstalledAsPhar = true): ToolInfoInterface
    {
        return new class($this->root, $isInstalledAsPhar) implements ToolInfoInterface {
            private vfsStreamDirectory $directory;
            private bool $isInstalledAsPhar;

            public function __construct(vfsStreamDirectory $directory, bool $isInstalledAsPhar)
            {
                $this->directory = $directory;
                $this->isInstalledAsPhar = $isInstalledAsPhar;
            }

            public function getComposerInstallationDetails(): array
            {
                throw new \LogicException('Not implemented.');
            }

            public function getComposerVersion(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function getVersion(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function isInstalledAsPhar(): bool
            {
                return $this->isInstalledAsPhar;
            }

            public function isInstalledByComposer(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRunInsideDocker(): bool
            {
                return false;
            }

            public function getPharDownloadUri(string $version): string
            {
                return \sprintf('%s/%s.phar', $this->directory->url(), $version);
            }
        };
    }

    private function getToolPath(): string
    {
        return "{$this->root->url()}/php-cs-fixer";
    }

    private static function getCurrentMajorVersion(): int
    {
        return (int) Preg::replace('/^v?(\d+).*$/', '$1', Application::VERSION);
    }

    private static function getNewMinorReleaseVersion(): string
    {
        return self::getCurrentMajorVersion().'.999.0';
    }

    private static function getNewMajorVersion(): int
    {
        return self::getCurrentMajorVersion() + 1;
    }

    private static function getNewMajorReleaseVersion(): string
    {
        return self::getNewMajorVersion().'.0.0';
    }

    private function createNewVersionCheckerDouble(
        string $latestVersion = Application::VERSION,
        ?string $latestMinorVersion = Application::VERSION,
        bool $latestMajorVersionSuccess = true,
        bool $latestMinorVersionSuccess = true
    ): NewVersionCheckerInterface {
        return new class($latestVersion, $latestMinorVersion, $latestMajorVersionSuccess, $latestMinorVersionSuccess) implements NewVersionCheckerInterface {
            private string $latestVersion;
            private ?string $latestMinorVersion;
            private bool $latestMajorVersionSuccess;
            private bool $latestMinorVersionSuccess;

            public function __construct(
                string $latestVersion,
                ?string $latestMinorVersion,
                bool $latestMajorVersionSuccess = true,
                bool $latestMinorVersionSuccess = true
            ) {
                $this->latestVersion = $latestVersion;
                $this->latestMinorVersion = $latestMinorVersion;
                $this->latestMajorVersionSuccess = $latestMajorVersionSuccess;
                $this->latestMinorVersionSuccess = $latestMinorVersionSuccess;
            }

            public function getLatestVersion(): string
            {
                if ($this->latestMajorVersionSuccess) {
                    return $this->latestVersion;
                }

                throw new \RuntimeException('Foo.');
            }

            public function getLatestVersionOfMajor(int $majorVersion): ?string
            {
                TestCase::assertSame((int) Preg::replace('/^v?(\d+).*$/', '$1', Application::VERSION), $majorVersion);

                if ($this->latestMinorVersionSuccess) {
                    return $this->latestMinorVersion;
                }

                throw new \RuntimeException('Foo.');
            }

            public function compareVersions(string $versionA, string $versionB): int
            {
                return (new NewVersionChecker(
                    new class implements GithubClientInterface {
                        public function getTags(): array
                        {
                            throw new \LogicException('Not implemented.');
                        }
                    }
                ))->compareVersions($versionA, $versionB);
            }
        };
    }

    private function createPharCheckerDouble(): PharCheckerInterface
    {
        return new class implements PharCheckerInterface {
            public function checkFileValidity(string $filename): ?string
            {
                return null;
            }
        };
    }
}
