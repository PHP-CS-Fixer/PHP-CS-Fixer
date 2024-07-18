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

namespace PhpCsFixer\Console\Command;

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\Report\ListSetsReport\ReporterFactory;
use PhpCsFixer\Console\Report\ListSetsReport\ReporterInterface;
use PhpCsFixer\Console\Report\ListSetsReport\ReportSummary;
use PhpCsFixer\Console\Report\ListSetsReport\TextReporter;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Utils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
#[AsCommand(name: 'list-sets')]
final class ListSetsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'list-sets';

    protected function configure(): void
    {
        $this
            ->setDefinition(
                [
                    new InputOption('format', '', InputOption::VALUE_REQUIRED, 'To output results in other formats.', (new TextReporter())->getFormat()),
                ]
            )
            ->setDescription('List all available RuleSets.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reporter = $this->resolveReporterWithFactory(
            $input->getOption('format'),
            new ReporterFactory()
        );

        $reportSummary = new ReportSummary(
            array_values(RuleSets::getSetDefinitions())
        );

        $report = $reporter->generate($reportSummary);

        $output->isDecorated()
            ? $output->write(OutputFormatter::escape($report))
            : $output->write($report, false, OutputInterface::OUTPUT_RAW);

        return 0;
    }

    private function resolveReporterWithFactory(string $format, ReporterFactory $factory): ReporterInterface
    {
        try {
            $factory->registerBuiltInReporters();
            $reporter = $factory->getReporter($format);
        } catch (\UnexpectedValueException $e) {
            $formats = $factory->getFormats();
            sort($formats);

            throw new InvalidConfigurationException(\sprintf('The format "%s" is not defined, supported are %s.', $format, Utils::naturalLanguageJoin($formats)));
        }

        return $reporter;
    }
}
