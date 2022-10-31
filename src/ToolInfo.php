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

namespace PhpCsFixer;

use PhpCsFixer\Console\Application;

/**
 * Obtain information about using version of tool.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ToolInfo implements ToolInfoInterface
{
    public const COMPOSER_PACKAGE_NAME = 'friendsofphp/php-cs-fixer';

    public const COMPOSER_LEGACY_PACKAGE_NAME = 'fabpot/php-cs-fixer';

    /**
     * @var null|array{name: string, version: string, dist: array{reference?: string}}
     */
    private $composerInstallationDetails;

    /**
     * @var null|bool
     */
    private $isInstalledByComposer;

    public function getComposerInstallationDetails(): array
    {
        if (!$this->isInstalledByComposer()) {
            throw new \LogicException('Cannot get composer version for tool not installed by composer.');
        }

        if (null === $this->composerInstallationDetails) {
            $composerInstalled = json_decode(file_get_contents($this->getComposerInstalledFile()), true);

            $packages = $composerInstalled['packages'] ?? $composerInstalled;

            foreach ($packages as $package) {
                if (\in_array($package['name'], [self::COMPOSER_PACKAGE_NAME, self::COMPOSER_LEGACY_PACKAGE_NAME], true)) {
                    $this->composerInstallationDetails = $package;

                    break;
                }
            }
        }

        return $this->composerInstallationDetails;
    }

    public function getComposerVersion(): string
    {
        $package = $this->getComposerInstallationDetails();

        $versionSuffix = '';

        if (isset($package['dist']['reference'])) {
            $versionSuffix = '#'.$package['dist']['reference'];
        }

        return $package['version'].$versionSuffix;
    }

    public function getVersion(): string
    {
        if ($this->isInstalledByComposer()) {
            return Application::VERSION.':'.$this->getComposerVersion();
        }

        return Application::VERSION;
    }

    public function isInstalledAsPhar(): bool
    {
        return str_starts_with(__DIR__, 'phar://');
    }

    public function isInstalledByComposer(): bool
    {
        if (null === $this->isInstalledByComposer) {
            $this->isInstalledByComposer = !$this->isInstalledAsPhar() && file_exists($this->getComposerInstalledFile());
        }

        return $this->isInstalledByComposer;
    }

    public function getPharDownloadUri(string $version): string
    {
        return sprintf(
            'https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/%s/php-cs-fixer.phar',
            $version
        );
    }

    private function getComposerInstalledFile(): string
    {
        return __DIR__.'/../../../composer/installed.json';
    }
}
