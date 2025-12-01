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

use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[AsCommand(name: 'parse', description: 'Parse file into tokens.')]
final class ParseCommand extends Command
{
    public const MODE_NATIVE = 'native';
    public const MODE_FIXER = 'fixer';

    public const FORMAT_DUMP = 'dump';
    public const FORMAT_JSON = 'json';

    /** @TODO PHP 8.0 - remove the property */
    protected static $defaultName = 'parse';

    /** @TODO PHP 8.0 - remove the property */
    protected static $defaultDescription = 'Parse file into tokens.';

    protected function configure(): void
    {
        $this
            ->setDefinition(
                [
                    new InputArgument('path', InputArgument::REQUIRED),
                    new InputOption('mode', null, InputOption::VALUE_REQUIRED, 'Parsing mode: `fixer` or `native`.', self::MODE_FIXER),
                    new InputOption('format', null, InputOption::VALUE_REQUIRED, 'Output format: `json` or `dump`.', self::FORMAT_JSON),
                ]
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stdErr = $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;

        $path = $input->getArgument('path');
        $mode = $input->getOption('mode');
        $format = $input->getOption('format');

        if (!\in_array($mode, [self::MODE_FIXER, self::MODE_NATIVE], true)) {
            $stdErr->writeln('<error>Invalid "mode" option.</error>');

            return 1;
        }
        if (!\in_array($format, [self::FORMAT_DUMP, self::FORMAT_JSON], true)) {
            $stdErr->writeln('<error>Invalid "format" option.</error>');

            return 1;
        }

        $code = @file_get_contents($path);

        if (false === $code) {
            $stdErr->writeln('<error>Cannot read file.</error>');

            return 1;
        }

        if (self::MODE_FIXER === $mode) {
            $tokens = Tokens::fromCode($code);
            $tokensJson = $tokens->toJson();
        } else {
            $tokens = \defined('TOKEN_PARSE')
                ? token_get_all($code, \TOKEN_PARSE)
                : token_get_all($code);

            $options = Utils::calculateBitmask(['JSON_PRETTY_PRINT', 'JSON_NUMERIC_CHECK']);
            $tokensJson = json_encode(\SplFixedArray::fromArray($tokens), $options);
        }

        if (self::FORMAT_DUMP === $format) {
            $output->writeln(var_dump($tokens));
        } else {
            $output->writeln($tokensJson);
        }

        return 0;
    }
}
