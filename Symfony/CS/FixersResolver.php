<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * The resolver that resolves fixers to use by command line options and config.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class FixersResolver
{
    protected $fixers = array();
    protected $allFixers;
    protected $config;

    public function __construct(array $allFixers, ConfigInterface $config)
    {
        $this->allFixers = $allFixers;
        $this->config = $config;
    }

    /**
     * Resolves fixers.
     *
     * @param string $levelOption
     * @param string $fixerOption
     *
     * @return array An array of FixerInterface
     */
    public function resolve($levelOption, $fixerOption)
    {
        $this->resolveByLevel($levelOption, $fixerOption);
        $this->resolveByNames($fixerOption);

        return $this->getFixers();
    }

    /**
     * Returns fixers.
     *
     * @return array An array of FixerInterface
     */
    public function getFixers()
    {
        return $this->fixers;
    }

    protected function resolveByLevel($levelOption, $fixerOption)
    {
        $level = $this->parseLevelOption($levelOption, $fixerOption);

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

    protected function resolveByNames($fixerOption)
    {
        $names = $this->parseFixerOption($fixerOption);

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

    protected function parseLevelOption($levelOption, $fixerOption)
    {
        static $levelMap = array(
            'psr0'    => FixerInterface::PSR0_LEVEL,
            'psr1'    => FixerInterface::PSR1_LEVEL,
            'psr2'    => FixerInterface::PSR2_LEVEL,
            'symfony' => FixerInterface::SYMFONY_LEVEL,
        );

        if (null !== $levelOption) {
            if (!isset($levelMap[$levelOption])) {
                throw new \InvalidArgumentException(sprintf('The level "%s" is not defined.', $levelOption));
            }

            return $levelMap[$levelOption];
        }

        $names = $this->parseFixerOption($fixerOption);

        if (empty($names)) {
            return $this->config->getLevel();
        }

        foreach ($names as $name) {
            if (0 === strpos($name, '-')) {
                return $this->config->getLevel();
            }
        }

        return null;
    }

    protected function parseFixerOption($fixerOption)
    {
        if (null === $fixerOption) {
            return $this->config->getFixers();
        }

        return array_map('trim', explode(',', $fixerOption));
    }
}
