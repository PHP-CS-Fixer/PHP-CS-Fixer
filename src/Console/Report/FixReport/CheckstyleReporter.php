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

use PhpCsFixer\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
final class CheckstyleReporter implements ReporterInterface
{
    public function getFormat(): string
    {
        return 'checkstyle';
    }

    public function generate(ReportSummary $reportSummary): string
    {
        if (!\extension_loaded('dom')) {
            throw new \RuntimeException('Cannot generate report! `ext-dom` is not available!');
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');

        /** @var \DOMElement $checkstyles */
        $checkstyles = $dom->appendChild($dom->createElement('checkstyle'));
        $checkstyles->setAttribute('version', Application::getAbout());

        foreach ($reportSummary->getChanged() as $filePath => $fixResult) {
            /** @var \DOMElement $file */
            $file = $checkstyles->appendChild($dom->createElement('file'));
            $file->setAttribute('name', $filePath);

            foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                $error = $this->createError($dom, $appliedFixer);
                $file->appendChild($error);
            }
        }

        $dom->formatOutput = true;

        $result = $dom->saveXML();
        if (false === $result) {
            throw new \RuntimeException('Failed to generate XML output');
        }

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($result) : $result;
    }

    private function createError(\DOMDocument $dom, string $appliedFixer): \DOMElement
    {
        $error = $dom->createElement('error');
        $error->setAttribute('severity', 'warning');
        $error->setAttribute('source', 'PHP-CS-Fixer.'.$appliedFixer);
        $error->setAttribute('message', 'Found violation(s) of type: '.$appliedFixer);

        return $error;
    }
}
