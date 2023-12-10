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

            public function getPharDownloadUri(string $version): string
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
