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
final class TextReporter implements ReporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormat(): string
    {
        return 'txt';
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ReportSummary $reportSummary): string
    {
        $sets = $reportSummary->getSets();

        usort($sets, function (RuleSetDescriptionInterface $a, RuleSetDescriptionInterface $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $output = '';

        foreach ($sets as $i => $set) {
            $output .= sprintf('%2d) %s', $i + 1, $set->getName()).PHP_EOL.'      '.$set->getDescription().PHP_EOL;

            if ($set->isRisky()) {
                $output .= '      Set contains risky rules.'.PHP_EOL;
            }
        }

        return $output;
    }
}
