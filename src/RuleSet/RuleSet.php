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

namespace PhpCsFixer\RuleSet;

/**
 * Set of rules to be used by fixer.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 * @final
 *
 * TODO on 3.0 make final after PhpCsFixer\RuleSet has been removed
 */
class RuleSet implements RuleSetInterface
{
    /**
     * Group of rules generated from input set.
     *
     * The key is name of rule, value is bool if the rule/set should be used.
     * The key must not point to any set.
     *
     * @var array
     */
    private $rules;

    public function __construct(array $set = [])
    {
        foreach ($set as $key => $value) {
            if (\is_int($key)) {
                throw new \InvalidArgumentException(sprintf('Missing value for "%s" rule/set.', $value));
            }
        }

        $this->resolveSet($set);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule($rule)
    {
        return \array_key_exists($rule, $this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleConfiguration($rule)
    {
        if (!$this->hasRule($rule)) {
            throw new \InvalidArgumentException(sprintf('Rule "%s" is not in the set.', $rule));
        }

        if (true === $this->rules[$rule]) {
            return null;
        }

        return $this->rules[$rule];
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @deprecated will be removed in 3.0 Use the constructor.
     */
    public static function create(array $set = [])
    {
        @trigger_error(__METHOD__.' is deprecated and will be removed in 3.0, use the constructor.', E_USER_DEPRECATED);

        return new self($set);
    }

    /**
     * @deprecated will be removed in 3.0 Use PhpCsFixer\RuleSet\RuleSets::getSetDefinitionNames
     */
    public function getSetDefinitionNames()
    {
        @trigger_error(__METHOD__.' is deprecated and will be removed in 3.0, use PhpCsFixer\RuleSet\RuleSets::getSetDefinitionNames.', E_USER_DEPRECATED);

        return RuleSets::getSetDefinitionNames();
    }

    /**
     * Resolve input set into group of rules.
     *
     * @return $this
     */
    private function resolveSet(array $rules)
    {
        $resolvedRules = [];

        // expand sets
        foreach ($rules as $name => $value) {
            if ('@' === $name[0]) {
                if (!\is_bool($value)) {
                    throw new \UnexpectedValueException(sprintf('Nested rule set "%s" configuration must be a boolean.', $name));
                }

                $set = $this->resolveSubset($name, $value);
                $resolvedRules = array_merge($resolvedRules, $set);
            } else {
                $resolvedRules[$name] = $value;
            }
        }

        // filter out all resolvedRules that are off
        $resolvedRules = array_filter($resolvedRules);

        $this->rules = $resolvedRules;

        return $this;
    }

    /**
     * Resolve set rules as part of another set.
     *
     * If set value is false then disable all fixers in set,
     * if not then get value from set item.
     *
     * @param string $setName
     * @param bool   $setValue
     *
     * @return array
     */
    private function resolveSubset($setName, $setValue)
    {
        $rules = RuleSets::getSetDefinition($setName)->getRules();

        foreach ($rules as $name => $value) {
            if ('@' === $name[0]) {
                $set = $this->resolveSubset($name, $setValue);
                unset($rules[$name]);
                $rules = array_merge($rules, $set);
            } elseif (!$setValue) {
                $rules[$name] = false;
            } else {
                $rules[$name] = $value;
            }
        }

        return $rules;
    }
}
