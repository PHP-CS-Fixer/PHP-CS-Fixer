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
    const COMPOSER_PACKAGE_NAME = 'friendsofphp/php-cs-fixer';

    const COMPOSER_LEGACY_PACKAGE_NAME = 'fabpot/php-cs-fixer';

    /**
     * @var null|array
     */
    private $composerInstallationDetails;

    /**
     * @var null|bool
     */
    private $isInstalledByComposer;

    public function getComposerInstallationDetails()
    {
        if (!$this->isInstalledByComposer()) {
            throw new \LogicException('Cannot get composer version for tool not installed by composer.');
        }

        if (null === $this->composerInstallationDetails) {
            $composerInstalled = json_decode(file_get_contents($this->getComposerInstalledFile()), true);

            foreach ($composerInstalled as $package) {
                if (in_array($package['name'], array(self::COMPOSER_PACKAGE_NAME, self::COMPOSER_LEGACY_PACKAGE_NAME), true)) {
                    $this->composerInstallationDetails = $package;

                    break;
                }
            }
        }

        return $this->composerInstallationDetails;
    }

    public function getComposerVersion()
    {
        $package = $this->getComposerInstallationDetails();

        return $package['version'].'#'.$package['dist']['reference'];
    }

    public function getVersion()
    {
        if ($this->isInstalledByComposer()) {
            return Application::VERSION.':'.$this->getComposerVersion();
        }

        return Application::VERSION;
    }

    public function isInstalledAsPhar()
    {
        return 'phar://' === substr(__DIR__, 0, 7);
    }

    public function isInstalledByComposer()
    {
        if (null === $this->isInstalledByComposer) {
            $this->isInstalledByComposer = !$this->isInstalledAsPhar() && file_exists($this->getComposerInstalledFile());
        }

        return $this->isInstalledByComposer;
    }

    private function getComposerInstalledFile()
    {
        return __DIR__.'/../../../composer/installed.json';
    }
}
