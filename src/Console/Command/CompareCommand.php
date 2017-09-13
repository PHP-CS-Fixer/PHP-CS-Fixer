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

namespace PhpCsFixer\Console\Command;

use PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 */
final class CompareCommand extends Command
{
    const COMMAND_NAME = 'compare';
    const YES = "<fg=green;>\xE2\x9C\x94</>";
    const NO = "<fg=red;>\xE2\x9C\x96</>";

    /** @var ConfigInterface $defaultConfig */
    private $defaultConfig;

    /** @var FixerFactory $fixerFactory */
    private $fixerFactory;

    /** @var bool $hideConfigured */
    private $hideConfigured;

    /** @var bool $hideEnabled */
    private $hideEnabled;

    /** @var bool $hideRisky */
    private $hideRisky;

    /** @var array $builtInFixers */
    private $builtInFixers;

    /** @var array $configuredFixers */
    private $configuredFixers;

    /** @var array $enabledFixers */
    private $enabledFixers;

    /** @var array $riskyFixers */
    private $riskyFixers;

    /** @var array $list */
    private $list = [];

    /** @var array $dump */
    private $dump;

    /**
     * @param null|FixerFactory $fixerFactory
     */
    public function __construct(FixerFactory $fixerFactory = null)
    {
        parent::__construct();

        if (null === $fixerFactory) {
            $fixerFactory = new FixerFactory();
            $fixerFactory->registerBuiltInFixers();
        }

        $this->fixerFactory = $fixerFactory;
        $this->defaultConfig = new Config();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDefinition(
                [
                    new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The path to a .php_cs file.'),
                    new InputOption('hide-configured', '', InputOption::VALUE_NONE, 'Hide fixers that are configured (in the config file or because of inheritance).'),
                    new InputOption('hide-enabled', '', InputOption::VALUE_NONE, 'Hide fixers that are currently enabled (the ones that are not disabled with [\'fixer_name\' => false]).'),
                    new InputOption('hide-risky', '', InputOption::VALUE_NONE, 'Hide fixers that are marked as risky.'),
                    new InputOption('dump', '', InputOption::VALUE_NONE, 'Dumps the comparing result in a copy-and-pastable format ready for the .php_cs file.'),
                ]
            )
            ->setDescription('Compares existent features with the ones actually configured.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initialize($input, $output);

        /** @var FixerInterface $fixer */
        foreach ($this->builtInFixers as $fixer) {
            $this->list[$fixer->getName()] = [
                'name' => $fixer->getName(),
                'is_configured' => $this->isFixerConfigured($fixer),
                'is_enabled' => $this->isFixerEnabled($fixer),
                'is_risky' => $this->isFixerRisky($fixer),
            ];

            if ($this->isFixerRisky($fixer)) {
                $this->riskyFixers[] = $fixer->getName();
            }
        }

        // Render the table
        $this->buildTable($output)->render();

        if ($input->getOption('dump')) {
            $this->dump($output);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $resolver = new ConfigurationResolver(
            $this->defaultConfig,
            [
                'allow-risky' => true,
                'config' => $input->getOption('config'),
            ],
            getcwd()
        );

        $this->builtInFixers = $this->fixerFactory->getFixers();
        $this->configuredFixers = $resolver->getConfig()->getRules();
        $this->enabledFixers = $resolver->getRules();

        // Order alphabetically
        usort($this->builtInFixers, function (FixerInterface $a, FixerInterface $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $this->hideConfigured = $input->getOption('hide-configured');
        $this->hideEnabled = $input->getOption('hide-enabled');
        $this->hideRisky = $input->getOption('hide-risky');
    }

    /**
     * @param OutputInterface $output
     *
     * @return Table
     */
    private function buildTable(OutputInterface $output)
    {
        $table = new Table($output);

        $columns = ['Fixer', 'Is Configured', 'Is Enabled', 'Is Risky'];

        // Displays the totals about found Fixers
        $totalsLine = sprintf('Found <fg=yellow;>%s built-in</> fixers. Of those, <fg=yellow;>%s are configured</>, <fg=yellow;>%s are enabled</> and <fg=yellow;>%s are risky</>.', count($this->builtInFixers), count($this->configuredFixers), count($this->enabledFixers), count($this->riskyFixers));

        // Displays the visibility set to filter the shown Fixers
        $visibilityLine = sprintf(
            'Hide Configured: %s | Hide Enabled: %s | Hide Risky: %s',
            $this->hideConfigured ? self::YES : self::NO,
            $this->hideEnabled ? self::YES : self::NO,
            $this->hideRisky ? self::YES : self::NO
        );

        $table->setHeaders([
            [new TableCell($totalsLine, ['colspan' => 4])],
            [new TableCell($visibilityLine, ['colspan' => count($columns)])],
            $columns,
        ]);

        $table->setRows($this->filterRows());

        return $table;
    }

    /**
     * @return array
     */
    private function filterRows()
    {
        $hideConfigured = $this->hideConfigured;
        $hideEnabled = $this->hideEnabled;
        $hideRisky = $this->hideRisky;

        $rows = array_filter($this->list, function (array $fixer) use ($hideConfigured, $hideEnabled, $hideRisky) {
            if ($hideConfigured && $fixer['is_configured']) {
                return false;
            }

            if ($hideEnabled && $fixer['is_enabled']) {
                return false;
            }

            if ($hideRisky && $fixer['is_risky']) {
                return false;
            }

            return true;
        });

        $this->dump = $rows;

        return array_map(function (array $fixer) {
            return [
                'name' => $fixer['name'],
                'is_configured' => $fixer['is_configured'] ? self::YES : self::NO,
                'is_enabled' => $fixer['is_enabled'] ? self::YES : self::NO,
                'is_risky' => $fixer['is_risky'] ? self::YES : self::NO,
            ];
        }, $rows);
    }

    /**
     * @param OutputInterface $output
     */
    private function dump(OutputInterface $output)
    {
        $line = empty($this->dump)
            ? 'You are aware of all exsisting rules! Yeah!'
            : var_export(
                array_map(function () {return false; }, $this->dump),
                true
            );

        $output->writeln(
            empty($this->dump)
                ? $line
                : "\nCopy and paste the following rules in your .php_cs file:\n\n"
                .'\\\\ Below the rules I don\'t want to use'."\n"
                .'\''.$line."\n"
                .'\\\\ END Rules to never use'."\n"
        );
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerConfigured(FixerInterface $fixer)
    {
        return array_key_exists($fixer->getName(), $this->configuredFixers);
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerRisky(FixerInterface $fixer)
    {
        return $fixer->isRisky();
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerEnabled(FixerInterface $fixer)
    {
        return array_key_exists($fixer->getName(), $this->enabledFixers);
    }
}
