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

        $resolver = new ConfigurationResolver(
            $this->defaultConfig,
            [
                'allow-risky' => true,
                'config' => $passedConfig,
            ],
            getcwd()
        );

        $configured = $resolver->getFixers();
        $builtIn = $this->fixerFactory->getFixers();

        $table = new Table($output);
        $table->setHeaders([
            [new TableCell(sprintf('Found <fg=yellow;>%s built-in</> fixers. Of those, <fg=yellow;>%s are configured</> to actually be used.', count($builtIn), count($configured)), ['colspan' => 3])],
            ['Fixer', 'In use', 'Is risky'],
        ]);

        $body = [];
        foreach ($this->fixerFactory->getFixers() as $fixer) {
            $body[] = [
                $fixer->getName(),
                in_array($fixer, $configured, true) ? "<fg=green;>\xE2\x9C\x94</>" : "<fg=red;>\xE2\x9C\x96</>",
                $fixer->isRisky() ? "<fg=green;>\xE2\x9C\x94</>" : "<fg=red;>\xE2\x9C\x96</>",
            ];
        }

        $table->setRows($body);

        $table->render();
    }
}
