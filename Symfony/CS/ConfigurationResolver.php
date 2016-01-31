<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\CS\ConfigurationException\InvalidConfigurationException;

/**
 * The resolver that resolves configuration to use by command line options and config.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ConfigurationResolver
{
    /**
     * @var FixerInterface[]
     */
    protected $allFixers;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var FixerInterface[]
     */
    protected $fixers = array();

    /**
     * @var array
     */
    protected $options = array(
        'fixers' => null,
        'level' => null,
        'progress' => null,
    );

    /**
     * @var string
     */
    private $format;

    public function setAllFixers(array $allFixers)
    {
        $this->allFixers = $allFixers;

        return $this;
    }

    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

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
     * Resolves fixers.
     *
     * @return ConfigurationResolver
     */
    public function resolve()
    {
        $this->resolveByLevel();
        $this->resolveByNames();
        $this->resolveFormat();

        return $this;
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

    public function getProgress()
    {
        // TODO: following condition should be removed on 2.0 line
        // and method should be added to ConfigInterface
        if (!method_exists($this->config, 'getHideProgress')) {
            return $this->options['progress'];
        }

        return $this->options['progress'] && !$this->config->getHideProgress();
    }

    protected function resolveByLevel()
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

    protected function resolveByNames()
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

    protected function resolveFormat()
    {
        if (array_key_exists('format', $this->options)) {
            $format = $this->options['format'];
        } elseif (method_exists($this->config, 'getFormat')) {
            $format = $this->config->getFormat();
        } else {
            $format = 'txt'; // default
        }

        static $formats = array('txt', 'xml', 'json');
        if (!in_array($format, $formats, true)) {
            throw new InvalidConfigurationException(sprintf('The format "%s" is not defined, supported are %s.', $format, implode(', ', $formats)));
        }

        $this->format = $format;
    }

    protected function parseLevel()
    {
        static $levelMap = array(
            'none' => FixerInterface::NONE_LEVEL,
            'psr0' => FixerInterface::PSR0_LEVEL,
            'psr1' => FixerInterface::PSR1_LEVEL,
            'psr2' => FixerInterface::PSR2_LEVEL,
            'symfony' => FixerInterface::SYMFONY_LEVEL,
        );

        $levelOption = $this->options['level'];

        if (null !== $levelOption) {
            if (!isset($levelMap[$levelOption])) {
                throw new InvalidConfigurationException(sprintf('The level "%s" is not defined.', $levelOption));
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
    }

    protected function parseFixers()
    {
        if (null !== $this->options['fixers']) {
            return array_map('trim', explode(',', $this->options['fixers']));
        }

        if (null === $this->options['level']) {
            return $this->config->getFixers();
        }
    }
}
