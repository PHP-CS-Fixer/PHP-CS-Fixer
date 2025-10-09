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
     * @requires PHP >= 8.0
     */
    public function testDetectHigherPhpVersionWithHigherVersion(): void
    {
        // This test assumes the composer.json requires PHP ^7.4 || ^8.0
        // and that we're running on PHP >= 8.0
        $currentMajorMinor = \sprintf('%d.%d', \PHP_MAJOR_VERSION, \PHP_MINOR_VERSION);

        // Only run this test if we're actually running on a version higher than 7.4
        if (version_compare($currentMajorMinor, '7.4', '<=')) {
            self::markTestSkipped('This test requires running on PHP > 7.4');
        }

        $toolInfo = $this->createToolInfoDouble(false, 'not-installed-by-composer');

        $warningsDetector = new WarningsDetector($toolInfo);
        $warningsDetector->detectHigherPhpVersion();

        $warnings = $warningsDetector->getWarnings();

        self::assertNotEmpty($warnings);
        self::assertStringContainsString('You are running PHP CS Fixer on PHP', $warnings[0]);
        self::assertStringContainsString('but the minimum required version in composer.json is PHP', $warnings[0]);
        self::assertStringContainsString('This may introduce syntax or features not available in PHP', $warnings[0]);
    }

    public function testDetectHigherPhpVersionDoesNotThrowWhenNoWarnings(): void
    {
        // This test verifies that the method handles errors gracefully
        // Even if there are issues reading composer.json, it should not throw
        $toolInfo = $this->createToolInfoDouble(false, 'not-installed-by-composer');

        $warningsDetector = new WarningsDetector($toolInfo);

        // This should not throw an exception
        $warningsDetector->detectHigherPhpVersion();

        // The method either adds a warning or doesn't, but shouldn't crash
        self::assertIsArray($warningsDetector->getWarnings());
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
