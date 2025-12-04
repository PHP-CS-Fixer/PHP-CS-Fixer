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

namespace PhpCsFixer\Console\Internal\Command;

use PhpCsFixer\Tokenizer\Token;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[AsCommand(name: 'decode-id', description: 'Get symbolic name of token id.')]
final class DecodeIdCommand extends Command
{
    /** @TODO PHP 8.0 - remove the property */
    protected static $defaultName = 'decode-id';

    /** @TODO PHP 8.0 - remove the property */
    protected static $defaultDescription = 'Get symbolic name of token id.';

    protected function configure(): void
    {
        $this
            ->setDefinition(
                [
                    new InputArgument('id', InputArgument::REQUIRED),
                ]
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stdErr = $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;

        $id = $input->getArgument('id');

        if (false === filter_var($id, \FILTER_VALIDATE_INT)) {
            $stdErr->writeln('<error>Non-numeric "id" value.</error>');

            return 1;
        }

        $id = \intval($id, 10);

        $name = Token::getNameForId($id);
        if (null === $name) {
            $stdErr->writeln('<error>Unknown "id".</error>');

            return 1;
        }

        $output->writeln($name);

        return 0;
    }
}
