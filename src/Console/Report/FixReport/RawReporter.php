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

namespace PhpCsFixer\Console\Report\FixReport;

use PhpCsFixer\FileReader;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Reporter that prints the resulting file instead of a report about it.
 *
 * It is meant for STDIN, so the tool can be used as a step of a formatting pipeline.
 *
 * @author Dylan Pulver <dylanpulver@users.noreply.github.com>
 *
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RawReporter implements ReporterInterface
{
    public function getFormat(): string
    {
        return 'raw';
    }

    public function generate(ReportSummary $reportSummary): string
    {
        $content = null;

        foreach ($reportSummary->getChanged() as $fixResult) {
            if (isset($fixResult['newContent'])) {
                $content = $fixResult['newContent'];

                break;
            }
        }

        // Nothing was fixed, so the input already follows the rules and has to be passed through as it was provided.
        $content ??= FileReader::createSingleton()->read('php://stdin');

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($content) : $content;
    }
}
