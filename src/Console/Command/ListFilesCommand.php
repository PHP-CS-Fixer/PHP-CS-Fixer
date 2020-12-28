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
use PhpCsFixer\ToolInfoInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ListFilesCommand extends Command
{
    protected static $defaultName = 'list-files';


    /**
     * @var ConfigInterface
     */
    private $defaultConfig;

    /**
     * @var ToolInfoInterface
     */
    private $toolInfo;

    public function __construct(ToolInfoInterface $toolInfo)
    {
        parent::__construct();

        $this->defaultConfig = new Config();
        $this->toolInfo = $toolInfo;
    }

    /**
     * {@inheritdoc}
     *
     * Override here to only generate the help copy when used.
     */
    public function getHelp()
    {
        return HelpCommand::getHelpCopy();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                [
                    new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The path to a .php_cs file.'),
                ]
            )
            ->setDescription('List all files beeing fixed by the given config.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $passedConfig = $input->getOption('config');

        $resolver = new ConfigurationResolver(
            $this->defaultConfig,
            [
                'config' => $passedConfig,
            ],
            getcwd(),
            $this->toolInfo
        );

        $finder = $resolver->getFinder();

        foreach ($finder as $file) {
            $output->writeln(escapeshellarg($file->getRealPath()));
        }

        return 0;
    }
}
