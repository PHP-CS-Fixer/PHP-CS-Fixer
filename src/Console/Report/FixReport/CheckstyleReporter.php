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

use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 *
 * @internal
 */
final class CheckstyleReporter implements ReporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormat(): string
    {
        return 'checkstyle';
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ReportSummary $reportSummary): string
    {
        if (!\extension_loaded('dom')) {
            throw new \RuntimeException('Cannot generate report! `ext-dom` is not available!');
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $checkstyles = $dom->appendChild($dom->createElement('checkstyle'));

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

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($dom->saveXML()) : $dom->saveXML();
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
