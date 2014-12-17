<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console;

use Symfony\CS\Config\Config;
use Symfony\CS\FixerInterface;
use Symfony\CS\StdinFileInfo;

/**
 * The resolver that resolves configuration to use by command line options and config.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
class ConfigurationResolver
{
    protected $allFixers;
    protected $config;
    protected $configFile;
    protected $cwd;
    protected $defaultConfig;
    protected $fixer;
    protected $fixers = array();
    protected $options = array(
        'config' => null,
        'config-file' => null,
        'isStdIn' => null,
        'fixers' => null,
        'level' => null,
        'path' => null,
        'progress' => null,
    );

    public function setCwd($cwd)
    {
        $this->cwd = $cwd;

        return $this;
    }

    public function setDefaultConfig($config)
    {
        $this->defaultConfig = $config;

        return $this;
    }

    public function setFixer($fixer)
    {
        $this->fixer     = $fixer;
        $this->allFixers = $fixer->getFixers();

        return $this;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }

        return $this;
    }

    /**
     * Resolve configuration.
     *
     * @return ConfigurationResolver
     */
    public function resolve()
    {
        $this->resolveConfig();
        $this->resolveConfigPath();

        $this->resolveFixersByLevel();
        $this->resolveFixersByNames();

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getConfigFile()
    {
        return $this->configFile;
    }

    /**
     * Returns fixers.
     *
     * @return FixerInterface[] An array of FixerInterface
     */
    public function getFixers()
    {
        return $this->fixers;
    }

    public function getProgress()
    {
        return $this->options['progress'] && !$this->config->getHideProgress();
    }

    protected function computeConfigFiles()
    {
        $configFile = $this->options['config-file'];
        $path = $this->options['path'];

        if (null !== $configFile) {
            return array($configFile);
        }

        if (is_file($path) && $dirName = pathinfo($path, PATHINFO_DIRNAME)) {
            $configDir = $dirName;
        } elseif ($this->options['isStdIn'] || null === $path) {
            $configDir = $this->cwd;
            // path is directory
        } else {
            $configDir = $path;
        }

        return array(
            $configDir.DIRECTORY_SEPARATOR.'.php_cs',
            $configDir.DIRECTORY_SEPARATOR.'.php_cs.dist',
        );
    }

    protected function resolveConfig()
    {
        $configOption = $this->options['config'];

        if ($configOption) {
            foreach ($this->fixer->getConfigs() as $c) {
                if ($c->getName() === $configOption) {
                    $this->config = $c;

                    return;
                }
            }

            if (null === $this->config) {
                throw new \InvalidArgumentException(sprintf('The configuration "%s" is not defined', $configOption));
            }
        }

        foreach ($this->computeConfigFiles() as $configFile) {
            if (file_exists($configFile)) {
                $config = include $configFile;

                // verify that the config has an instance of Config
                if (!$config instanceof Config) {
                    throw new \UnexpectedValueException(sprintf('The config file "%s" does not return an instance of Symfony\CS\Config\Config', $configFile));
                }

                $this->config     = $config;
                $this->configFile = $configFile;

                return;
            }
        }

        $this->config = $this->defaultConfig;
    }

    protected function resolveConfigPath()
    {
        $path = $this->options['path'];
        $isStdIn = $this->options['isStdIn'];

        if (is_file($path)) {
            $this->config->finder(new \ArrayIterator(array(new \SplFileInfo($path))));
        } elseif ($isStdIn) {
            $this->config->finder(new \ArrayIterator(array(new StdinFileInfo())));
        } elseif (null !== $path) {
            $this->config->setDir($path);
        }
    }

    protected function resolveFixersByLevel()
    {
        $level = $this->parseLevel();

        if (null === $level) {
            return;
        }

        $fixers = array();

        foreach ($this->allFixers as $fixer) {
            if ($fixer->getLevel() === ($fixer->getLevel() & $level)) {
                $fixers[] = $fixer;
            }
        }

        $this->fixers = $fixers;
    }

    protected function resolveFixersByNames()
    {
        $names = $this->parseFixers();

        if (null === $names) {
            return;
        }

        $addNames = array();
        $removeNames = array();
        foreach ($names as $name) {
            if (0 === strpos($name, '-')) {
                $removeNames[ltrim($name, '-')] = true;
            } else {
                $addNames[$name] = true;
            }
        }

        foreach ($this->fixers as $key => $fixer) {
            if (isset($removeNames[$fixer->getName()])) {
                unset($this->fixers[$key]);
            }
        }

        foreach ($this->allFixers as $fixer) {
            if (isset($addNames[$fixer->getName()]) && !in_array($fixer, $this->fixers, true)) {
                $this->fixers[] = $fixer;
            }
        }
    }

    protected function parseLevel()
    {
        static $levelMap = array(
            'none'    => FixerInterface::NONE_LEVEL,
            'psr0'    => FixerInterface::PSR0_LEVEL,
            'psr1'    => FixerInterface::PSR1_LEVEL,
            'psr2'    => FixerInterface::PSR2_LEVEL,
            'symfony' => FixerInterface::SYMFONY_LEVEL,
        );

        $levelOption = $this->options['level'];

        if (null !== $levelOption) {
            if (!isset($levelMap[$levelOption])) {
                throw new \InvalidArgumentException(sprintf('The level "%s" is not defined.', $levelOption));
            }

            return $levelMap[$levelOption];
        }

        if (null === $this->options['fixers']) {
            return $this->config->getLevel();
        }

        foreach ($this->parseFixers() as $fixer) {
            if (0 === strpos($fixer, '-')) {
                return $this->config->getLevel();
            }
        }

        return;
    }

    protected function parseFixers()
    {
        if (null !== $this->options['fixers']) {
            return array_map('trim', explode(',', $this->options['fixers']));
        }

        if (null === $this->options['level']) {
            return $this->config->getFixers();
        }

        return;
    }
}
