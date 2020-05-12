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
 * @internal
 */
final class JsonReporter implements ReporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormat(): string
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ReportSummary $reportSummary): string
    {
        $sets = $reportSummary->getSets();

        usort($sets, static function (RuleSetDescriptionInterface $a, RuleSetDescriptionInterface $b): int {
            return strcmp($a->getName(), $b->getName());
        });

        $json = ['sets' => []];

        foreach ($sets as $set) {
            $setName = $set->getName();
            $json['sets'][$setName] = [
                'description' => $set->getDescription(),
                'isRisky' => $set->isRisky(),
                'name' => $setName,
            ];
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }
}
