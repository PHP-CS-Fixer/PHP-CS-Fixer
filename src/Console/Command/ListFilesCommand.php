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

use PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\ToolInfoInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Path;

/**
 * @author Markus Staab <markus.staab@redaxo.org>
 *
 * @internal
 */
#[AsCommand(name: 'list-files')]
final class ListFilesCommand extends Command
{
    protected static $defaultName = 'list-files';

    private ConfigInterface $defaultConfig;

    private ToolInfoInterface $toolInfo;

    public function __construct(ToolInfoInterface $toolInfo)
    {
        parent::__construct();

        $this->defaultConfig = new Config();
        $this->toolInfo = $toolInfo;
    }

    protected function configure(): void
    {
        $this
            ->setDefinition(
                [
                    new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The path to a .php-cs-fixer.php file.'),
                ]
            )
            ->setDescription('List all files being fixed by the given config.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $passedConfig = $input->getOption('config');
        $cwd = getcwd();

        $resolver = new ConfigurationResolver(
            $this->defaultConfig,
            [
                'config' => $passedConfig,
            ],
            $cwd,
            $this->toolInfo
        );

        $finder = $resolver->getFinder();

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            if ($file->isFile()) {
                $relativePath = './'.Path::makeRelative($file->getRealPath(), $cwd);
                // unify directory separators across operating system
                $relativePath = str_replace('/', \DIRECTORY_SEPARATOR, $relativePath);

                $output->writeln(escapeshellarg($relativePath));
            }
        }

        return 0;
    }
}
