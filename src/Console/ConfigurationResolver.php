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

namespace PhpCsFixer\Console;

use PhpCsFixer\ConfigInterface;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Fixer;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\FixerInterface;
use PhpCsFixer\RuleSet;
use PhpCsFixer\StdinFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The resolver that resolves configuration to use by command line options and config.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ConfigurationResolver
{
    /**
     * @var bool
     */
    private $allowRisky;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var string
     */
    private $configFile;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @var ConfigInterface
     */
    private $defaultConfig;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    /**
     * @var string
     */
    private $format;

    /**
     * @var bool
     */
    private $isStdIn;

    /**
     * @var bool
     */
    private $isDryRun;

    /**
     * @var FixerInterface[]
     */
    private $fixers = array();

    /**
     * @var array
     */
    private $options = array(
        'allow-risky' => null,
        'config' => null,
        'dry-run' => null,
        'format' => 'txt',
        'path' => null,
        'progress' => null,
        'using-cache' => null,
        'cache-file' => null,
        'rules' => null,
    );
    private $path;
    private $progress;
    private $usingCache;
    private $cacheFile;
    private $ruleSet;

    public function __construct()
    {
        $this->fixerFactory = new FixerFactory();
        $this->fixerFactory->registerBuiltInFixers();
    }

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
     * Returns fixer factory.
     *
     * @return FixerFactory
     */
    public function getFixerFactory()
    {
        return $this->fixerFactory;
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
     * Returns output format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
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
     * Returns rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->ruleSet->getRules();
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
        $this->resolveFormat();

        $this->resolveConfig();
        $this->resolveConfigPath();
        $this->resolveRiskyAllowed();

        $this->fixerFactory->registerCustomFixers($this->getConfig()->getCustomFixers());
        $this->fixerFactory->attachConfig($this->getConfig());

        $this->resolveRules();
        $this->resolveFixers();

        $this->resolveProgress();
        $this->resolveUsingCache();
        $this->resolveCacheFile();

        $this->config->fixers($this->getFixers());
        $this->config->setRules($this->getRules());
        $this->config->setUsingCache($this->usingCache);
        $this->config->setCacheFile($this->cacheFile);
        $this->config->setRiskyAllowed($this->allowRisky);

        return $this;
    }

    /**
     * Set current working directory.
     *
     * @param string $cwd
     *
     * @return ConfigurationResolver
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
     * @return ConfigurationResolver
     */
    public function setDefaultConfig(ConfigInterface $config)
    {
        $this->defaultConfig = $config;

        return $this;
    }

    /**
     * Set option that will be resolved.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return ConfigurationResolver
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidConfigurationException(sprintf('Unknown option name: "%s".', $name));
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Set options that will be resolved.
     *
     * @param array $options
     *
     * @return ConfigurationResolver
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
        $configFile = $this->options['config'];

        if (null !== $configFile) {
            if (false === file_exists($configFile) || false === is_readable($configFile)) {
                throw new InvalidConfigurationException(sprintf('Cannot read config file "%s".', $configFile));
            }

            return array($configFile);
        }

        $path = $this->path;
        if (is_file($path) && $dirName = pathinfo($path, PATHINFO_DIRNAME)) {
            $configDir = $dirName;
        } elseif ($this->isStdIn || null === $path) {
            $configDir = $this->cwd;
            // path is directory
        } else {
            $configDir = $path;
        }

        $candidates = array(
            $configDir.DIRECTORY_SEPARATOR.'.php_cs',
            $configDir.DIRECTORY_SEPARATOR.'.php_cs.dist',
        );

        if ($configDir !== $this->cwd) {
            $candidates[] = $this->cwd.DIRECTORY_SEPARATOR.'.php_cs';
            $candidates[] = $this->cwd.DIRECTORY_SEPARATOR.'.php_cs.dist';
        }

        return $candidates;
    }

    /**
     * Compute rules.
     *
     * @return array
     */
    private function parseRules()
    {
        if (null === $this->options['rules']) {
            return $this->config->getRules();
        }

        $rules = array();

        foreach (array_map('trim', explode(',', $this->options['rules'])) as $rule) {
            if ('-' === $rule[0]) {
                $rules[ltrim($rule, '-')] = false;
            } else {
                $rules[$rule] = true;
            }
        }

        return $rules;
    }

    /**
     * Resolve config.
     */
    private function resolveConfig()
    {
        foreach ($this->computeConfigFiles() as $configFile) {
            if (!file_exists($configFile)) {
                continue;
            }

            $config = include $configFile;

            // verify that the config has an instance of Config
            if (!$config instanceof ConfigInterface) {
                throw new InvalidConfigurationException(sprintf('The config file: "%s" does not return a "PhpCsFixer\ConfigInterface" instance. Got: "%s".', $configFile, is_object($config) ? get_class($config) : gettype($config)));
            }

            $this->config = $config;
            $this->configFile = $configFile;

            return;
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
     * Resolve fixers to run based on rules.
     */
    private function resolveFixers()
    {
        $this->fixers = $this->fixerFactory->useRuleSet($this->ruleSet)->getFixers();

        if (true === $this->allowRisky) {
            return;
        }

        $riskyFixers = array_map(
            function (FixerInterface $fixer) {
                return $fixer->getName();
            },
            array_filter(
                $this->fixers,
                function (FixerInterface $fixer) {
                    return $fixer->isRisky();
                }
            )
        );

        if (!empty($riskyFixers)) {
            throw new InvalidConfigurationException(sprintf('The rules contain risky fixers (%s), but they are not allowed to run. Perhaps you forget to use --allow-risky option?', implode(', ', $riskyFixers)));
        }
    }

    protected function resolveFormat()
    {
        static $formats = array('txt', 'xml', 'json');

        if (array_key_exists('format', $this->options)) {
            $format = $this->options['format'];
        } elseif (method_exists($this->config, 'getFormat')) {
            $format = $this->config->getFormat();
        } else {
            $format = 'txt'; // default
        }

        if (!in_array($format, $formats, true)) {
            throw new InvalidConfigurationException(sprintf('The format "%s" is not defined, supported are %s.', $format, implode(', ', $formats)));
        }

        $this->format = $format;
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

    /**
     * Resolve rules.
     */
    private function resolveRules()
    {
        $this->ruleSet = new RuleSet($this->parseRules());
    }

    /**
     * Resolve using cache.
     */
    private function resolveUsingCache()
    {
        if (null !== $this->options['using-cache']) {
            $this->usingCache = 'yes' === $this->options['using-cache'];

            return;
        }

        $this->usingCache = $this->config->usingCache();
    }

    /**
     * Resolves cache file.
     */
    private function resolveCacheFile()
    {
        if (null !== $this->options['cache-file']) {
            $this->cacheFile = $this->options['cache-file'];

            return;
        }

        $this->cacheFile = $this->config->getCacheFile();
    }

    /**
     * Resolves risky allowed flag.
     */
    private function resolveRiskyAllowed()
    {
        if (null !== $this->options['allow-risky']) {
            $this->allowRisky = 'yes' === $this->options['allow-risky'];

            return;
        }

        $this->allowRisky = $this->config->getRiskyAllowed();
    }
}
