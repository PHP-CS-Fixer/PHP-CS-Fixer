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

use Symfony\Component\Finder\Finder;

/**
 * Class provides a way to create a group of fixers.
 *
 * Fixers may be registered (made the factory aware of them) by
 * registering a custom fixer and default, built in fixers.
 * Then, one can attach Config instance to fixer instances.
 *
 * Finally factory creates a ready to use group of fixers.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FixerFactory
{
    /**
     * Fixers.
     *
     * @var FixerInterface[]
     */
    private $fixers = array();

    /**
     * Create instance.
     *
     * @return FixerFactory
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Attach config into all fixers that are aware of it.
     *
     * @param ConfigInterface $config
     *
     * @return $this
     */
    public function attachConfig(ConfigInterface $config)
    {
        foreach ($this->fixers as $fixer) {
            if ($fixer instanceof ConfigAwareInterface) {
                $fixer->setConfig($config);
            }
        }

        return $this;
    }

    /**
     * Get fixers.
     *
     * @return FixerInterface[]
     */
    public function getFixers()
    {
        $this->sortFixers();

        return $this->fixers;
    }

    /**
     * Register all built in fixers.
     *
     * @return $this
     */
    public function registerBuiltInFixers()
    {
        static $builtInFixers = null;

        if (null === $builtInFixers) {
            $builtInFixers = array();

            foreach (Finder::create()->files()->in(__DIR__.'/Fixer') as $file) {
                $relativeNamespace = $file->getRelativePath();
                $builtInFixers[] = 'Symfony\\CS\\Fixer\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
            }
        }

        foreach ($builtInFixers as $class) {
            $this->registerFixer(new $class());
        }

        return $this;
    }

    /**
     * Register fixers.
     *
     * @param FixerInterface[] $fixers
     *
     * @return $this
     */
    public function registerCustomFixers(array $fixers)
    {
        foreach ($fixers as $fixer) {
            $this->registerFixer($fixer);
        }

        return $this;
    }

    /**
     * Register fixer.
     *
     * @param FixerInterface $fixer
     *
     * @return $this
     */
    public function registerFixer(FixerInterface $fixer)
    {
        $this->fixers[] = $fixer;

        return $this;
    }

    /**
     * Apply RuleSet on fixers to filter out all unwanted fixers.
     *
     * @param RuleSetInteface $ruleSet
     *
     * @return $this
     */
    public function useRuleSet(RuleSetInterface $ruleSet)
    {
        $fixersByName = array();

        foreach ($this->fixers as $fixer) {
            $fixersByName[$fixer->getName()] = $fixer;
        }

        $fixers = array();

        foreach (array_keys($ruleSet->getRules()) as $name) {
            if (!array_key_exists($name, $fixersByName)) {
                throw new \UnexpectedValueException(sprintf('Rule "%s" does not exist.', $name));
            }

            $fixer = $fixersByName[$name];
            $fixer->configure($ruleSet->getRuleConfiguration($name));
            $fixers[] = $fixer;
        }

        $this->fixers = $fixers;

        return $this;
    }

    /**
     * Sort fixers by their priorities.
     *
     * @return $this
     */
    private function sortFixers()
    {
        usort($this->fixers, function (FixerInterface $a, FixerInterface $b) {
            return Utils::cmpInt($b->getPriority(), $a->getPriority());
        });

        return $this;
    }
}
