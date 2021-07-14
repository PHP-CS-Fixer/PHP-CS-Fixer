<?php

declare(strict_types=1);

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

use PhpCsFixer\RuleSetNameValidator;
use Symfony\Component\Finder\Finder;

/**
 * Set of rule sets to be used by fixer.
 *
 * @author Krystian Marcisz <simivar@gmail.com>
 *
 * @internal
 */
final class RuleSetsFactory
{
    /** @var RuleSetNameValidator */
    private $nameValidator;

    private $ruleSets = [];

    public function __construct()
    {
        $this->nameValidator = new RuleSetNameValidator();
    }

    /**
     * @return array<string, RuleSetDescriptionInterface>
     */
    public function getRuleSets(): array
    {
        return $this->ruleSets;
    }

    public function registerBuiltInRuleSets(): self
    {
        static $ruleSets = null;

        if (null === $ruleSets) {
            $ruleSets = [];

            foreach (Finder::create()->files()->in(__DIR__.'/Sets') as $file) {
                $class = 'PhpCsFixer\RuleSet\Sets\\'.$file->getBasename('.php');

                $ruleSets[] = new $class();
            }
        }

        foreach ($ruleSets as $class) {
            $this->registerRuleSet($class, false);
        }

        return $this;
    }

    /**
     * @param AbstractRuleSetDescription[] $ruleSets
     *
     * @return $this
     */
    public function registerCustomRuleSets(iterable $ruleSets): self
    {
        foreach ($ruleSets as $ruleSet) {
            $this->registerRuleSet($ruleSet, true);
        }

        return $this;
    }

    public function registerRuleSet(AbstractRuleSetDescription $ruleSet, bool $isCustom): void
    {
        $name = $ruleSet->getName();
        if (isset($this->ruleSets[$name])) {
            throw new \UnexpectedValueException(sprintf('Rule Set named "%s" is already registered.', $name));
        }

        if (!$this->nameValidator->isValid($name, $isCustom)) {
            throw new \UnexpectedValueException(sprintf('Rule Set named "%s" has invalid name.', $name));
        }

        $this->ruleSets[$name] = $ruleSet;

        ksort($this->ruleSets);
    }

    /**
     * @return string[]
     */
    public function getRuleSetsNames(): array
    {
        return array_keys($this->getRuleSets());
    }

    public function getRuleSet(string $name): RuleSetDescriptionInterface
    {
        $ruleSets = $this->getRuleSets();

        if (!isset($ruleSets[$name])) {
            throw new \InvalidArgumentException(sprintf('Rule Set "%s" does not exist.', $name));
        }

        return $ruleSets[$name];
    }
}
