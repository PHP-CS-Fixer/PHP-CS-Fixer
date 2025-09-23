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

namespace PhpCsFixer\RuleSet\Sets;

use PhpCsFixer\RuleSet\AbstractRuleSetDescription;
use PhpCsFixer\RuleSet\AutomaticMigrationSetTrait;
use PhpCsFixer\RuleSet\AutomaticRuleSetDescriptionInterface;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AutoPHPUnitMigrationRiskySet extends AbstractRuleSetDescription implements AutomaticRuleSetDescriptionInterface
{
    use AutomaticMigrationSetTrait;

    public function getName(): string
    {
        return '@'.lcfirst(ltrim(parent::getName(), '@'));
    }

    public function getRules(): array
    {
        return [
            $this->calculateTargetSet($this->getName(), 'PHPUnit', $this->isRisky()) => true,
        ];
    }

    public function getDescription(): string
    {
        return 'Rules to improve test code for PHPUnit. Target version is automatically detected from project\'s "composer.json" file.';
    }

    public function getRulesCandidates(): array
    {
        $sets = array_values(self::calculateCandidateSets('PHP', $this->isRisky()));

        ksort($sets);

        return array_combine($sets, array_fill(0, \count($sets), true));
    }
}
