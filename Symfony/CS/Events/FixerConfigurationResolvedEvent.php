<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Events;

use Symfony\Component\EventDispatcher\Event;
use Symfony\CS\ConfigInterface;

/**
 * Event that is fired when the configuration of a Fixer has been resolved.
 */
final class FixerConfigurationResolvedEvent extends Event
{
    const NAME = 'fixer.configuration_resolved';

    private $config;

    private $configFile;

    /**
     * @param ConfigInterface $config
     * @param string|null     $configFile Path to the configuration file.
     */
    public function __construct(ConfigInterface $config, $configFile = null)
    {
        $this->config = $config;
        $this->configFile = $configFile;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getConfigFile()
    {
        return $this->configFile;
    }
}
