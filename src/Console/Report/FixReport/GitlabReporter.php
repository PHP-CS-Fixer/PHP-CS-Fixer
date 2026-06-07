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
use PhpCsFixer\Documentation\DocumentationLocator;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use SebastianBergmann\Diff\Parser;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Generates a report according to gitlabs subset of codeclimate json files.
 *
 * @author Hans-Christian Otto <c.otto@suora.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @see https://github.com/codeclimate/platform/blob/master/spec/analyzers/SPEC.md#data-types
 *
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class GitlabReporter implements ReporterInterface
{
    private Parser $diffParser;
    private DocumentationLocator $documentationLocator;
    private FixerFactory $fixerFactory;

    /**
     * @var array<string, FixerInterface>
     */
    private array $fixers;

    public function __construct()
    {
        $this->diffParser = new Parser();
        $this->documentationLocator = new DocumentationLocator();

        $this->fixerFactory = new FixerFactory();
        $this->fixerFactory->registerBuiltInFixers();

        $this->fixers = $this->createFixers();
    }

    public function getFormat(): string
    {
        return 'gitlab';
    }

    /**
     * Process changed files array. Returns generated report.
     */
    public function generate(ReportSummary $reportSummary): string
    {
        $about = Application::getAbout();

        $report = [];
        foreach ($reportSummary->getChanged() as $fileName => $change) {
            foreach ($change['appliedFixers'] as $fixerName) {
                $fixer = $this->fixers[$fixerName] ?? null;

                $report[] = [
                    'check_name' => 'PHP-CS-Fixer.'.$fixerName,
                    'description' => null !== $fixer
                        ? $fixer->getDefinition()->getSummary()
                        : 'PHP-CS-Fixer.'.$fixerName.' (custom rule)',
                    'content' => [
                        'body' => \sprintf(
                            "%s\n%s",
                            $about,
                            null !== $fixer
                                ? \sprintf(
                                    'Check [docs](https://cs.symfony.com/doc/rules/%s.html) for more information.',
                                    substr($this->documentationLocator->getFixerDocumentationFileRelativePath($fixer), 0, -4), // -4 to drop `.rst`
                                )
                                : 'Check performed with a custom rule.',
                        ),
                    ],
                    'categories' => ['Style'],
                    'fingerprint' => md5($fileName.$fixerName),
                    'severity' => 'minor',
                    'location' => [
                        'path' => $fileName,
                        'lines' => LineExtractor::getLines($this->diffParser->parse($change['diff'])),
                    ],
                ];
            }
        }

        $jsonString = json_encode($report, \JSON_THROW_ON_ERROR);

        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($jsonString) : $jsonString;
    }

    /**
     * @return array<string, FixerInterface>
     */
    private function createFixers(): array
    {
        $fixers = [];

        foreach ($this->fixerFactory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        ksort($fixers);

        return $fixers;
    }
}
