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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 */
#[AsCommand(name: 'worker', description: 'Internal command for running fixers in parallel', hidden: true)]
final class WorkerCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'worker';

    /** @var string */
    protected static $defaultDescription = 'Internal command for running fixers in parallel';

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->setHidden(true);
    }

    protected function configure(): void
    {
        $this->setDefinition(
            [
                new InputArgument(
                    'paths',
                    InputArgument::IS_ARRAY,
                    'The path(s) that rules will be run against (each path can be a file or directory).'
                ),
                new InputOption(
                    'allow-risky',
                    '',
                    InputOption::VALUE_REQUIRED,
                    'Are risky fixers allowed (can be `yes` or `no`).'
                ),
                new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The path to a config file.'),
                new InputOption(
                    'rules',
                    '',
                    InputOption::VALUE_REQUIRED,
                    'List of rules that should be run against configured paths.'
                ),
                new InputOption(
                    'using-cache',
                    '',
                    InputOption::VALUE_REQUIRED,
                    'Does cache should be used (can be `yes` or `no`).'
                ),
                new InputOption('cache-file', '', InputOption::VALUE_REQUIRED, 'The path to the cache file.'),
            ]
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
