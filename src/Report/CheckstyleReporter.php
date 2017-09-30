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

namespace PhpCsFixer\Report;

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
    public function getFormat()
    {
        return 'checkstyle';
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ReportSummary $reportSummary)
    {
        if (!extension_loaded('dom')) {
            throw new \RuntimeException('Cannot generate report! `ext-dom` is not available!');
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $checkstyles = $dom->appendChild($dom->createElement('checkstyle'));

        foreach ($reportSummary->getChanged() as $filePath => $fixResult) {
            /** @var \DOMElement $file */
            $file = $checkstyles->appendChild($dom->createElement('file'));
            $file->setAttribute('name', $filePath);

            $this->addErrors(
                $dom,
                $file,
                $fixResult,
                $reportSummary->shouldAddAppliedFixers()
            );
        }

        $dom->formatOutput = true;

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($dom->saveXML()) : $dom->saveXML();
    }

    /**
     * @param \DOMDocument $dom
     * @param \DOMElement  $file
     * @param array        $fixResult
     * @param bool         $shouldAddAppliedFixers
     *
     * @return \DOMElement
     */
    private function addErrors(\DOMDocument $dom, \DOMElement $file, array $fixResult, $shouldAddAppliedFixers)
    {
        foreach ($fixResult['appliedFixers'] as $appliedFixer) {
            $error = $this->createError($dom, $appliedFixer, $shouldAddAppliedFixers);

            $file->appendChild($error);
        }
    }

    /**
     * @param \DOMDocument $dom
     * @param string       $appliedFixer
     * @param bool         $shouldAddAppliedFixers
     *
     * @return \DOMElement
     */
    private function createError(\DOMDocument $dom, $appliedFixer, $shouldAddAppliedFixers)
    {
        $error = $dom->createElement('error');
        $error->setAttribute('severity', 'warning');

        if ($shouldAddAppliedFixers) {
            $error->setAttribute('source', 'PHP-CS-Fixer.'.$appliedFixer);
        }

        return $error;
    }
}
