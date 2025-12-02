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

namespace PhpCsFixer\Tests\Console;

use PhpCsFixer\ComposerJsonReader;
use PhpCsFixer\Console\WarningsDetector;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfoInterface;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\WarningsDetector
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class WarningsDetectorTest extends TestCase
{
    public function testDetectOldVendorNotInstalledByComposer(): void
    {
        $toolInfo = $this->createToolInfoDouble(false, 'not-installed-by-composer');

        $warningsDetector = new WarningsDetector($toolInfo);
        $warningsDetector->detectOldVendor();

        self::assertSame([], $warningsDetector->getWarnings());
    }

    public function testDetectOldVendorNotLegacyPackage(): void
    {
        $toolInfo = $this->createToolInfoDouble(false, 'friendsofphp/php-cs-fixer');

        $warningsDetector = new WarningsDetector($toolInfo);
        $warningsDetector->detectOldVendor();

        self::assertSame([], $warningsDetector->getWarnings());
    }

    public function testDetectOldVendorLegacyPackage(): void
    {
        $toolInfo = $this->createToolInfoDouble(true, 'fabpot/php-cs-fixer');

        $warningsDetector = new WarningsDetector($toolInfo);
        $warningsDetector->detectOldVendor();

        self::assertSame([
            'You are running PHP CS Fixer installed with old vendor `fabpot/php-cs-fixer`. Please update to `friendsofphp/php-cs-fixer`.',
            'If you need help while solving warnings, ask at https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/discussions/, we will help you!',
        ], $warningsDetector->getWarnings());
    }

    /**
     * This test verifies that a warning is shown when running on a PHP version higher than the minimum required in composer.json.
     */
    public function testDetectHigherPhpVersionWithHigherVersion(): void
    {
        // Extract the minimum PHP version from composer.json
        $composerJsonReader = ComposerJsonReader::createSingleton();
        $minPhpVersion = $composerJsonReader->getPhp();
        self::assertSame('7.4', $minPhpVersion, 'Expected minimum PHP version to be 7.4');

        // Only run this test if we're actually running on a version higher than the minimum required
        $currentMajorMinor = \sprintf('%d.%d', \PHP_MAJOR_VERSION, \PHP_MINOR_VERSION);
        if (version_compare($currentMajorMinor, $minPhpVersion, '<=')) {
            self::markTestSkipped(\sprintf('This test requires running on PHP > %s', $minPhpVersion));
        }

        $toolInfo = $this->createToolInfoDouble(false, 'not-installed-by-composer');

        $warningsDetector = new WarningsDetector($toolInfo);
        $warningsDetector->detectHigherPhpVersion();

        $warnings = $warningsDetector->getWarnings();

        self::assertNotEmpty($warnings);
        self::assertStringStartsWith(
            \sprintf(
                'You are running PHP CS Fixer on PHP %s, but the minimum PHP version supported by your project in composer.json is PHP %s',
                \PHP_VERSION,
                $minPhpVersion
            ),
            $warnings[0]
        );
    }

    /**
     * This test verifies that no warning is shown when running on a PHP version equal to or lower than the minimum required.
     *
     * @runInSeparateProcess
     */
    public function testDetectHigherPhpVersionWithEqualOrLowerVersion(): void
    {
        $originalDir = getcwd();
        if (false === $originalDir) {
            throw new \RuntimeException('Unable to determine current working directory');
        }

        $tempDir = sys_get_temp_dir().\DIRECTORY_SEPARATOR.'phpcsfixer_test_'.uniqid('', true);
        mkdir($tempDir);

        try {
            // Change to temp directory so ComposerJsonReader looks for composer.json there.
            // ComposerJsonReader reads from the current working directory, not a configurable path.
            chdir($tempDir);

            // Create a composer.json with PHP requirement equal to or higher than current version
            $currentMajorMinor = \sprintf('%d.%d', \PHP_MAJOR_VERSION, \PHP_MINOR_VERSION);
            file_put_contents('composer.json', json_encode([
                'name' => 'test/test',
                'require' => [
                    'php' => '^'.$currentMajorMinor,
                ],
            ]));

            $toolInfo = $this->createToolInfoDouble(false, 'not-installed-by-composer');
            $warningsDetector = new WarningsDetector($toolInfo);
            $warningsDetector->detectHigherPhpVersion();

            $warnings = $warningsDetector->getWarnings();

            // No warning should be shown when running on the minimum required version or lower
            self::assertSame([], $warnings);
        } finally {
            chdir($originalDir);
            if (file_exists($tempDir.'/composer.json')) {
                unlink($tempDir.'/composer.json');
            }
            rmdir($tempDir);
        }
    }

    /**
     * This test verifies that a warning is shown when composer.json cannot be read.
     *
     * @runInSeparateProcess
     */
    public function testDetectHigherPhpVersionWithMissingComposerJson(): void
    {
        $originalDir = getcwd();
        if (false === $originalDir) {
            throw new \RuntimeException('Unable to determine current working directory');
        }

        $tempDir = sys_get_temp_dir().\DIRECTORY_SEPARATOR.'phpcsfixer_test_'.uniqid('', true);
        mkdir($tempDir);

        try {
            // Change to temp directory so ComposerJsonReader looks for composer.json there.
            // ComposerJsonReader reads from the current working directory, not a configurable path.
            chdir($tempDir);

            $toolInfo = $this->createToolInfoDouble(false, 'not-installed-by-composer');
            $warningsDetector = new WarningsDetector($toolInfo);
            $warningsDetector->detectHigherPhpVersion();

            $warnings = $warningsDetector->getWarnings();

            self::assertNotEmpty($warnings);
            self::assertStringContainsString('Unable to determine minimum PHP version supported by your project from composer.json:', $warnings[0]);
        } finally {
            chdir($originalDir);
            rmdir($tempDir);
        }
    }

    /**
     * This test verifies that a warning is shown when composer.json has no PHP requirement.
     *
     * @runInSeparateProcess
     */
    public function testDetectHigherPhpVersionWithNoPhpRequirement(): void
    {
        $originalDir = getcwd();
        if (false === $originalDir) {
            throw new \RuntimeException('Unable to determine current working directory');
        }

        $tempDir = sys_get_temp_dir().\DIRECTORY_SEPARATOR.'phpcsfixer_test_'.uniqid('', true);
        mkdir($tempDir);

        try {
            // Change to temp directory so ComposerJsonReader looks for composer.json there.
            // ComposerJsonReader reads from the current working directory, not a configurable path.
            chdir($tempDir);

            // Create a composer.json without PHP requirement
            file_put_contents('composer.json', json_encode([
                'name' => 'test/test',
                'require' => [],
            ]));

            $toolInfo = $this->createToolInfoDouble(false, 'not-installed-by-composer');
            $warningsDetector = new WarningsDetector($toolInfo);
            $warningsDetector->detectHigherPhpVersion();

            $warnings = $warningsDetector->getWarnings();

            self::assertNotEmpty($warnings);
            self::assertSame(
                'No PHP version requirement found in composer.json. It is recommended to specify a minimum PHP version supported by your project.',
                $warnings[0]
            );
        } finally {
            chdir($originalDir);
            if (file_exists($tempDir.'/composer.json')) {
                unlink($tempDir.'/composer.json');
            }
            rmdir($tempDir);
        }
    }

    private function createToolInfoDouble(bool $isInstalledByComposer, string $packageName): ToolInfoInterface
    {
        $composerInstallationDetails = [
            'name' => $packageName,
            'version' => '1.0.0',
            'dist' => [],
        ];

        return new class($isInstalledByComposer, $composerInstallationDetails) implements ToolInfoInterface {
            private bool $isInstalledByComposer;

            /** @var array{name: string, version: string, dist: array{reference?: string}} */
            private array $composerInstallationDetails;

            /**
             * @param array{name: string, version: string, dist: array{reference?: string}} $composerInstallationDetails
             */
            public function __construct(bool $isInstalledByComposer, array $composerInstallationDetails)
            {
                $this->isInstalledByComposer = $isInstalledByComposer;
                $this->composerInstallationDetails = $composerInstallationDetails;
            }

            public function getComposerInstallationDetails(): array
            {
                return $this->composerInstallationDetails;
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
                throw new \LogicException('Not implemented.');
            }

            public function isInstalledByComposer(): bool
            {
                return $this->isInstalledByComposer;
            }

            public function isRunInsideDocker(): bool
            {
                return false;
            }

            public function getPharDownloadUri(string $version): string
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
