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

    /**
     * @var ConfigInterface
     */
    private $defaultConfig;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

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
                    new InputOption('show-risky', '', InputOption::VALUE_REQUIRED, 'The path to a .php_cs file.', false),
                    new InputOption('hide-configured', '', InputOption::VALUE_OPTIONAL, 'Hides all the rules currently configured to highlight only the ones not already in use.', false),
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
        $passedConfig = $input->getOption('config');
        $hideConfigured = null === $input->getOption('hide-configured') ? true : false;

        $resolver = new ConfigurationResolver(
            $this->defaultConfig,
            [
                'allow-risky' => true,
                'config' => $passedConfig,
            ],
            getcwd()
        );

        $configured = $resolver->getFixers();

        $configuredNames = [];
        foreach ($configured as $configuredFixer) {
            $configuredNames[] = $configuredFixer->getName();
        }

        $builtIn = $this->fixerFactory->getFixers();

        usort($builtIn, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $rows = [];
        $builtInCount = 0;
        foreach ($builtIn as $fixer) {
            ++$builtInCount;

            if ($fixer->isRisky() && false === $input->getOption('show-risky')) {
                // Don't show risky fixers if required
                continue;
            }

            $isConfigured = in_array($fixer->getName(), $configuredNames, true);

            if ($isConfigured && $hideConfigured) {
                continue;
            }

            $row = [
                $fixer->getName(),
                $isConfigured ? "<fg=green;>\xE2\x9C\x94</>" : "<fg=red;>\xE2\x9C\x96</>",
            ];

            if ($input->getOption('show-risky')) {
                $row[] = $fixer->isRisky() ? "<fg=green;>\xE2\x9C\x94</>" : "<fg=red;>\xE2\x9C\x96</>";
            }

            $rows[] = $row;
        }

        $table = new Table($output);

        $columns = ['Fixer', 'In use'];

        if ($input->getOption('show-risky')) {
            $columns[] = 'Is Risky';
        }

        $table->setHeaders([
            [new TableCell(sprintf('Found <fg=yellow;>%s built-in</> fixers. Of those, <fg=yellow;>%s are configured</> to actually be used.', $builtInCount, count($configured)), ['colspan' => count($columns)])],
            [new TableCell(sprintf(
                'Show risky: <fg=yellow;>%s</>; Hide configured: <fg=yellow;>%s</>',
                $input->getOption('show-risky') ? "<fg=green;>\xE2\x9C\x94</>" : "<fg=red;>\xE2\x9C\x96</>",
                $hideConfigured ? "<fg=green;>\xE2\x9C\x94</>" : "<fg=red;>\xE2\x9C\x96</>"
            ), ['colspan' => count($columns)])],
            $columns,
        ]);

        $table->setRows($rows);

        $table->render();
    }
}
