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
     * @var FixerInterface[] Associative array of fixers with names as keys
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

    public function setWhitespacesConfig(WhitespacesFixerConfig $config)
    {
        foreach ($this->fixers as $fixer) {
            if ($fixer instanceof WhitespacesFixerConfigAwareInterface) {
                $fixer->setWhitespacesConfig($config);
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

            foreach (SymfonyFinder::create()->files()->in(__DIR__.'/Fixer') as $file) {
                $relativeNamespace = $file->getRelativePath();
                $fixerClass = 'PhpCsFixer\\Fixer\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
                if ('Fixer' === substr($fixerClass, -5)) {
                    $builtInFixers[] = $fixerClass;
                }
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
        $fixerConflicts = array();

        $fixerNames = array_keys($ruleSet->getRules());
        foreach ($fixerNames as $name) {
            if (!array_key_exists($name, $this->fixersByName)) {
                throw new \UnexpectedValueException(sprintf('Rule "%s" does not exist.', $name));
            }

            $fixer = $this->fixersByName[$name];

            $config = $ruleSet->getRuleConfiguration($name);
            if (null !== $config) {
                $fixer->configure($config);
            }

            $fixers[] = $fixer;
            $fixersByName[$name] = $fixer;

            $conflicts = array_intersect($this->getFixersConflicts($fixer), $fixerNames);
            if (count($conflicts) > 0) {
                $fixerConflicts[$name] = $conflicts;
            }
        }

        if (count($fixerConflicts) > 0) {
            throw new \UnexpectedValueException($this->generateConflictMessage($fixerConflicts));
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

    /**
     * @param FixerInterface $fixer
     *
     * @return string[]|null
     */
    private function getFixersConflicts(FixerInterface $fixer)
    {
        static $conflictMap = array(
            'concat_with_spaces' => array('concat_without_spaces'),
            'echo_to_print' => array('print_to_echo'),
            'no_blank_lines_before_namespace' => array('single_blank_line_before_namespace'),
            'phpdoc_type_to_var' => array('phpdoc_var_to_type'),
        );

        $fixerName = $fixer->getName();

        return array_key_exists($fixerName, $conflictMap) ? $conflictMap[$fixerName] : array();
    }

    /**
     * @param array<string, string[]> $fixerConflicts
     *
     * @return string
     */
    private function generateConflictMessage(array $fixerConflicts)
    {
        $message = 'Rule contains conflicting fixers:';
        $report = array();
        foreach ($fixerConflicts as $fixer => $fixers) {
            // filter mutual conflicts
            $report[$fixer] = array_filter(
                $fixers,
                function ($candidate) use ($report, $fixer) {
                    return !array_key_exists($candidate, $report) || !in_array($fixer, $report[$candidate], true);
                }
            );

            if (count($report[$fixer]) > 0) {
                $message .= sprintf("\n- \"%s\" with \"%s\"", $fixer, implode('", "', $report[$fixer]));
            }
        }

        return $message;
    }
}
