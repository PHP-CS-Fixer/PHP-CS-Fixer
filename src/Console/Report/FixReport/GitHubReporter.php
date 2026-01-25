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

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use SebastianBergmann\Diff\Parser;

/**
 * Generates a report in GitHub Actions workflow command format to create file annotations.
 *
 * @author HypeMC <hypemc@gmail.com>
 *
 * @see https://docs.github.com/en/actions/using-workflows/workflow-commands-for-github-actions#setting-an-error-message
 *
 * @readonly
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class GitHubReporter implements ReporterInterface
{
    private Parser $diffParser;

    /**
     * @var array<string, FixerInterface>
     */
    private array $fixers;

    public function __construct()
    {
        $this->diffParser = new Parser();

        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();

        $this->fixers = $this->createFixers($fixerFactory);
    }

    public function getFormat(): string
    {
        return 'github';
    }

    /**
     * Process changed files array. Returns generated report.
     */
    public function generate(ReportSummary $reportSummary): string
    {
        $report = '';

        foreach ($reportSummary->getChanged() as $fileName => $change) {
            $lines = LineExtractor::getLines($this->diffParser->parse($change['diff']));

            foreach ($change['appliedFixers'] as $fixerName) {
                $fixer = $this->fixers[$fixerName] ?? null;

                $title = 'PHP-CS-Fixer.'.$fixerName;
                $message = null !== $fixer
                    ? $fixer->getDefinition()->getSummary()
                    : $title.' (custom rule)';

                $report .= \sprintf(
                    '::error file=%s,line=%d,title=%s::%s',
                    $this->escapeProperty($fileName),
                    $lines['begin'],
                    $this->escapeProperty($title),
                    $this->escapeMessage($message),
                ).\PHP_EOL;
            }
        }

        return $report;
    }

    private function escapeProperty(string $value): string
    {
        return str_replace(
            ['%', "\r", "\n", ':', ','],
            ['%25', '%0D', '%0A', '%3A', '%2C'],
            $value,
        );
    }

    private function escapeMessage(string $value): string
    {
        return str_replace(
            ['%', "\r", "\n"],
            ['%25', '%0D', '%0A'],
            $value,
        );
    }

    /**
     * @return array<string, FixerInterface>
     */
    private function createFixers(FixerFactory $fixerFactory): array
    {
        $fixers = [];

        foreach ($fixerFactory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        ksort($fixers);

        return $fixers;
    }
}
