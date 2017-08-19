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

namespace Symfony\CS\Console;

use Symfony\CS\ToolInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class WarningsDetector
{
    /**
     * @var string[]
     */
    private $warnings = array();

    public function detectOldMajor()
    {
        // @TODO to be removed at v2
        $this->warnings[] = 'You are running PHP CS Fixer v1, which is not maintained anymore. Please update to v2.';

        // @TODO to be activated at v3
        // $this->warnings[] = 'You are running PHP CS Fixer v2, which is not maintained anymore. Please update to v3.';
    }

    public function detectOldVendor()
    {
        if (ToolInfo::isInstalledByComposer()) {
            $details = ToolInfo::getComposerInstallationDetails();
            if (ToolInfo::COMPOSER_LEGACY_PACKAGE_NAME === $details['name']) {
                $this->warnings[] = sprintf(
                    'You are running PHP CS Fixer installed with old vendor `%s`. Please update to `%s`.',
                    ToolInfo::COMPOSER_LEGACY_PACKAGE_NAME,
                    ToolInfo::COMPOSER_PACKAGE_NAME
                );
            }
        }
    }

    public function detectXdebug()
    {
        if (extension_loaded('xdebug')) {
            $this->warnings[] = 'You are running PHP CS Fixer with xdebug enabled. This has a major impact on runtime performance.';
        }
    }

    /**
     * @return string[]
     */
    public function getWarnings()
    {
        if (!count($this->warnings)) {
            return array();
        }

        return array_unique(array_merge(
            $this->warnings,
            array('If you need help while solving warnings, ask at https://gitter.im/FriendsOfPHP/PHP-CS-Fixer, we will help you!')
        ));
    }
}
