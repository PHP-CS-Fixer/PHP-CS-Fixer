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

use Symfony\Component\Finder\Finder as SymfonyFinder;

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
     * Fixers by name.
     *
     * @var FixerInterface[] Associative array of fixers with names as keys.
     */
    private $fixersByName = array();

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

            foreach (SymfonyFinder::create()->files()->in(__DIR__.'/Fixer') as $file) {
                $relativeNamespace = $file->getRelativePath();
                $builtInFixers[] = 'PhpCsFixer\\Fixer\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
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
        $name = $fixer->getName();

        if (isset($this->fixersByName[$name])) {
            throw new \UnexpectedValueException(sprintf('Fixer named "%s" is already registered.', $name));
        }

        $this->fixers[] = $fixer;
        $this->fixersByName[$name] = $fixer;

        return $this;
    }

    /**
     * Apply RuleSet on fixers to filter out all unwanted fixers.
     *
     * @param RuleSetInterface $ruleSet
     *
     * @return $this
     */
    public function useRuleSet(RuleSetInterface $ruleSet)
    {
        $fixers = array();
        $fixersByName = array();

        foreach (array_keys($ruleSet->getRules()) as $name) {
            if (!array_key_exists($name, $this->fixersByName)) {
                throw new \UnexpectedValueException(sprintf('Rule "%s" does not exist.', $name));
            }

            $fixer = $this->fixersByName[$name];
            $fixer->configure($ruleSet->getRuleConfiguration($name));
            $fixers[] = $fixer;
            $fixersByName[$name] = $fixer;
        }

        $this->fixers = $fixers;
        $this->fixersByName = $fixersByName;

        return $this;
    }

    /**
     * Check if fixer exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasRule($name)
    {
        return isset($this->fixersByName[$name]);
    }

    /**
     * Sort fixers by their priorities.
     *
     * @return $this
     */
    private function sortFixers()
    {
        // Schwartzian transform is used to improve the efficiency and avoid
        // `usort(): Array was modified by the user comparison function` warning for mocked objects.

        $data = array_map(function (FixerInterface $fixer) {
            return array($fixer, $fixer->getPriority());
        }, $this->fixers);

        usort($data, function (array $a, array $b) {
            return Utils::cmpInt($b[1], $a[1]);
        });

        $this->fixers = array_map(function (array $item) {
            return $item[0];
        }, $data);

        return $this;
    }
}
