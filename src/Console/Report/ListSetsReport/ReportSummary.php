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

namespace PhpCsFixer\Console\Report\ListSetsReport;

use PhpCsFixer\RuleSet\RuleSetDescriptionInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @readonly
 *
 * @internal
 */
final class ReportSummary
{
    /**
     * @var list<RuleSetDescriptionInterface>
     */
    private array $sets;

    /**
     * @param list<RuleSetDescriptionInterface> $sets
     */
    public function __construct(array $sets)
    {
        $this->sets = $sets;
    }

    /**
     * @return list<RuleSetDescriptionInterface>
     */
    public function getSets(): array
    {
        return $this->sets;
    }
}
