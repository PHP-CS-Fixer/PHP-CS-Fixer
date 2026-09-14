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
    /**
     * Path under which the file read from STDIN is reported.
     */
    private const STDIN_PATH = 'php://stdin';

    public function getFormat(): string
    {
        return 'raw';
    }

    public function generate(ReportSummary $reportSummary): string
    {
        $changed = $reportSummary->getChanged();
        $unexpectedPaths = array_diff(array_keys($changed), [self::STDIN_PATH]);

        if ([] !== $unexpectedPaths) {
            throw new \LogicException(\sprintf(
                'Format "raw" is available for STDIN only, got a fix result for "%s".',
                implode('", "', $unexpectedPaths),
            ));
        }

        $fixResult = $changed[self::STDIN_PATH] ?? null;

        if (null === $fixResult) {
            // Nothing was fixed, so there is no fix result and the input is passed through as it was provided.
            return FileReader::createSingleton()->read(self::STDIN_PATH);
        }

        if (!isset($fixResult['newContent'])) {
            throw new \LogicException('Fix result for STDIN does not carry the fixed content.');
        }

        return $fixResult['newContent'];
    }
}
