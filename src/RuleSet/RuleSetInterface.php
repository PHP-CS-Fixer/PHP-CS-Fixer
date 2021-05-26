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

/**
 * Set of rules to be used by fixer.
 *
 * Example of set: ["@PSR2" => true, "@PSR1" => false, "strict" => true].
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface RuleSetInterface
{
    public function __construct(array $set = []);

    /**
     * Get configuration for given rule.
     *
     * @param string $rule name of rule
     */
    public function getRuleConfiguration(string $rule): ?array;

    /**
     * Get all rules from rules set.
     */
    public function getRules(): array;

    /**
     * Check given rule is in rules set.
     *
     * @param string $rule name of rule
     */
    public function hasRule(string $rule): bool;
}
