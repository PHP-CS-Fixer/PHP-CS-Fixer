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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\CS\Config\Config;
use Symfony\CS\ConfigInterface;
use Symfony\CS\Fixer;
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
    private $allFixers;
    private $config;
    private $configFile;
    private $cwd;
    private $defaultConfig;
    private $isStdIn;
    private $isDryRun;
    private $fixer;
    private $fixers = array();
    private $options = array(
        'config' => null,
        'config-file' => null,
        'dry-run' => null,
        'fixers' => null,
        'level' => null,
        'path' => null,
        'progress' => null,
    );
    private $path;
    private $progress;

    /**
     * Returns config instance.
     *
     * @return ConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns config file path.
     *
     * @return string
     */
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

    /**
     * Returns path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns progress flag.
     *
     * @return bool
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Returns dry-run flag.
     *
     * @return bool
     */
    public function isDryRun()
    {
        return $this->isDryRun;
    }

    /**
     * Resolve configuration.
     *
     * @return ConfigurationResolver
     */
    public function resolve()
    {
        $this->resolvePath();
        $this->resolveIsStdIn();
        $this->resolveIsDryRun();

        $this->resolveConfig();
        $this->resolveConfigPath();

        $this->resolveFixersByLevel();
        $this->resolveFixersByNames();

        $this->resolveProgress();

        $this->config->fixers($this->getFixers());

        return $this;
    }

    /**
     * Set current working directory.
     *
     * @param string $cwd
     *
     * @return FixersResolver
     */
    public function setCwd($cwd)
    {
        $this->cwd = $cwd;

        return $this;
    }

    /**
     * Set default config instance.
     *
     * @param ConfigInterface $config
     *
     * @return FixersResolver
     */
    public function setDefaultConfig(ConfigInterface $config)
    {
        $this->defaultConfig = $config;

        return $this;
    }

    /**
     * Set fixer instance.
     *
     * @param Fixer $config
     *
     * @return FixersResolver
     */
    public function setFixer(Fixer $fixer)
    {
        $this->fixer     = $fixer;
        $this->allFixers = $fixer->getFixers();

        return $this;
    }

    /**
     * Set option that will be resolved.
     *
     * @param string $name
     * @param misc   $value1
     *
     * @return FixersResolver
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \OutOfBoundsException('Unknown option name: '.$name);
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Set options that will be resolved.
     *
     * @param array $options
     *
     * @return FixersResolver
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }

        return $this;
    }

    /**
     * Compute file candidates for config file.
     *
     * @return string[]
     */
    private function computeConfigFiles()
    {
        $configFile = $this->options['config-file'];
        $path = $this->path;

        if (null !== $configFile) {
            return array($configFile);
        }

        if (is_file($path) && $dirName = pathinfo($path, PATHINFO_DIRNAME)) {
            $configDir = $dirName;
        } elseif ($this->isStdIn || null === $path) {
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

    /**
     * Compute fixers.
     *
     * @return string[]|null
     */
    private function parseFixers()
    {
        if (null !== $this->options['fixers']) {
            return array_map('trim', explode(',', $this->options['fixers']));
        }

        if (null === $this->options['level']) {
            return $this->config->getFixers();
        }

        return;
    }

    /**
     * Compute level.
     *
     * @return string|null
     */
    private function parseLevel()
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

    /**
     * Resolve config based on options: config, config-file.
     */
    private function resolveConfig()
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

    /**
     * Apply path on config instance.
     */
    private function resolveConfigPath()
    {
        if (is_file($this->path)) {
            $this->config->finder(new \ArrayIterator(array(new \SplFileInfo($this->path))));
        } elseif ($this->isStdIn) {
            $this->config->finder(new \ArrayIterator(array(new StdinFileInfo())));
        } elseif (null !== $this->path) {
            $this->config->setDir($this->path);
        }
    }

    /**
     * Resolve fixers to run based on level.
     */
    private function resolveFixersByLevel()
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

    /**
     * Resolve fixers to run based on names.
     */
    private function resolveFixersByNames()
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

    /**
     * Resolve isDryRun based on isStdIn property and dry-run option.
     */
    private function resolveIsDryRun()
    {
        // Can't write to STDIN
        if ($this->isStdIn) {
            $this->isDryRun = true;

            return;
        }

        $this->isDryRun = $this->options['dry-run'];
    }

    /**
     * Resolve isStdIn based on path option.
     */
    private function resolveIsStdIn()
    {
        $this->isStdIn = '-' === $this->options['path'];
    }

    /**
     * Resolve path based on path option.
     */
    private function resolvePath()
    {
        $path = $this->options['path'];

        if (null !== $path) {
            $filesystem = new Filesystem();
            if (!$filesystem->isAbsolutePath($path)) {
                $path = $this->cwd.DIRECTORY_SEPARATOR.$path;
            }
        }

        $this->path = $path;
    }

    /**
     * Resolve progress based on progress option and config instance.
     */
    private function resolveProgress()
    {
        $this->progress = $this->options['progress'] && !$this->config->getHideProgress();
    }
}
