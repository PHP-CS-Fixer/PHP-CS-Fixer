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
    public function getFormat()
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ReportSummary $reportSummary)
    {
        $json = ['sets' => []];

        $sets = $reportSummary->getSets();
        usort($sets, function (RuleSetDescriptionInterface $a, RuleSetDescriptionInterface $b) {
            return $a->getName() > $b->getName() ? 1 : -1;
        });

        foreach ($sets as $set) {
            $json['sets'][$set->getName()] = [
                'description' => $set->getDescription(),
                'isRisky' => $set->isRisky(),
                'name' => $set->getName(),
            ];
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }
}
