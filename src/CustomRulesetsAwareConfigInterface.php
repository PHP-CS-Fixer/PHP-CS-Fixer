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

namespace PhpCsFixer;

use PhpCsFixer\RuleSet\RuleSetDescriptionInterface;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @TODO 4.0 Include support for custom rulesets in main ConfigInterface
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
interface CustomRulesetsAwareConfigInterface extends ConfigInterface
{
    /**
     * Registers custom rule sets to be used the same way as built-in rule sets.
     *
     * @param list<class-string<RuleSetDescriptionInterface>> $ruleSets
     *
     * @todo v4 Introduce it in main ConfigInterface
     */
    public function registerCustomRuleSets(array $ruleSets): ConfigInterface;

    /**
     * @return list<class-string<RuleSetDescriptionInterface>>
     */
    public function getCustomRuleSets(): array;
}
