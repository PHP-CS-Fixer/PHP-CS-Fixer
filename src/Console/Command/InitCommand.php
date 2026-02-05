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

use PhpCsFixer\Console\Application;
use PhpCsFixer\RuleSet\RuleSetDefinitionInterface;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\RuleSet\Sets\AutoRiskySet;
use PhpCsFixer\RuleSet\Sets\AutoSet;
use PhpCsFixer\RuleSet\Sets\PhpCsFixerRiskySet;
use PhpCsFixer\RuleSet\Sets\PhpCsFixerSet;
use PhpCsFixer\RuleSet\Sets\SymfonyRiskySet;
use PhpCsFixer\RuleSet\Sets\SymfonySet;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[AsCommand(name: 'init', description: 'Create config file.')]
final class InitCommand extends Command
{
    private const FIXER_FILENAME = '.php-cs-fixer.dist.php';

    /** @TODO PHP 8.0 - remove the property */
    protected static $defaultName = 'init';

    /** @TODO PHP 8.0 - remove the property */
    protected static $defaultDescription = 'Create config file.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stdErr = $output;

        if ($output instanceof ConsoleOutputInterface) {
            $stdErr = $output->getErrorOutput();
            $stdErr->writeln(Application::getAboutWithRuntime(true));
        }

        $io = new SymfonyStyle($input, $stdErr);

        $io->warning('This command is experimental');

        if (file_exists(self::FIXER_FILENAME)) {
            $io->error(\sprintf('Configuration file `%s` already exists.', self::FIXER_FILENAME));

            return Command::FAILURE;
        }

        $io->note([
            'While we start, we must tell you that we put our diligence to NOT change the meaning of your codebase.',
            'Yet, some of the rules are explicitly _risky_ to apply. A rule is _risky_ if it could change code behaviour, e.g. transforming `==` into `===` or removal of trailing whitespaces within multiline strings.',
            'Such rules are improving your codebase even further, yet you shall always review changes proposed by _risky_ rules carefully.',
        ]);
        $isRiskyAllowed = 'yes' === $io->choice(
            'Do you want to enable _risky_ rules?',
            ['yes', 'no'],
            'no',
        );

        $setsByName = RuleSets::getBuiltInSetDefinitions();

        $setAuto = new AutoSet();
        $setAutoRisky = new AutoRiskySet();
        $setAutoWithOptionalRiskySetNamesTextual = $isRiskyAllowed ? '`@auto`/`@auto:risky`' : '`@auto`';

        $io->note("We recommend usage of {$setAutoWithOptionalRiskySetNamesTextual} rulesets. They take insights from your existing `composer.json` to configure project the best:");

        $generateSetsBehindAutoSet = static function () use ($setAuto, $setAutoRisky, $isRiskyAllowed): array {
            $sets = array_merge(
                array_keys($setAuto->getRulesCandidates()),
                $isRiskyAllowed ? array_keys($setAutoRisky->getRulesCandidates()) : [],
            );
            natcasesort($sets);

            return $sets;
        };
        $setsBehindAutoSet = $generateSetsBehindAutoSet();

        $io->listing(
            array_map(
                static fn (RuleSetDefinitionInterface $item): string => \sprintf(
                    '<fg=blue>`%s`</> - %s',
                    $item->getName(),
                    $item->getDescription(),
                ),
                array_map(
                    static fn (string $name): RuleSetDefinitionInterface => $setsByName[$name], // @phpstan-ignore-line offsetAccess.notFound
                    $setsBehindAutoSet,
                ),
            ),
        );

        $rules = [];

        $useAutoSet = 'yes' === $io->choice(
            "Do you want to use <fg=blue>{$setAutoWithOptionalRiskySetNamesTextual}</> ruleset?",
            ['yes', 'no'],
            'yes',
        );

        if ($useAutoSet) {
            $rules[] = $setAuto->getName();
            if ($isRiskyAllowed) {
                $rules[] = $setAutoRisky->getName();
            }
        }

        $generateExtraSets = static function () use ($isRiskyAllowed): array {
            $setSymfony = new SymfonySet();
            $setPhpCsFixer = new PhpCsFixerSet();

            $extraSets = [
                $setSymfony->getName(),
                $setPhpCsFixer->getName(),
            ];

            if ($isRiskyAllowed) {
                $setSymfonyRisky = new SymfonyRiskySet();
                $setPhpCsFixerRisky = new PhpCsFixerRiskySet();

                $extraSets[] = $setSymfonyRisky->getName();
                $extraSets[] = $setPhpCsFixerRisky->getName();
            }

            return $extraSets;
        };

        $extraSets = array_merge(
            false === $useAutoSet ? $setsBehindAutoSet : [],
            $generateExtraSets(),
        );
        natcasesort($extraSets);

        $sets = $io->choice(
            'Do you want to use any of other recommended ruleset? (multi-choice)',
            array_combine(
                $extraSets,
                array_map(
                    static fn (string $item): string => $setsByName[$item]->getDescription(), // @phpstan-ignore-line offsetAccess.notFound
                    $extraSets,
                ),
            ) + ['none' => 'none'],
            'none',
            true,
        );

        // older Symfony version can return single string instead of array with single string, let's unify
        if (!\is_array($sets)) {
            $sets = [$sets];
        }

        $rules = array_merge(
            $rules,
            array_unique(array_filter($sets, static fn ($item) => 'none' !== $item)),
        );

        $readResult = @file_get_contents(__DIR__.'/../../../resources/.php-cs-fixer.dist.php.template');
        if (false === $readResult) {
            throw new IOException('Failed to read template file.');
        }

        $content = str_replace(
            [
                '/*{{ IS_RISKY_ALLOWED }}*/',
                '/*{{ RULES }}*/',
            ],
            [
                $isRiskyAllowed ? 'true' : 'false',
                "[\n".implode(
                    ",\n",
                    array_map(
                        static fn ($item) => "        '{$item}' => true",
                        $rules,
                    ),
                )."\n    ]",
            ],
            $readResult,
        );

        $writeResult = @file_put_contents(self::FIXER_FILENAME, $content);
        if (false === $writeResult) {
            throw new IOException(\sprintf('Failed to write file "%s".', self::FIXER_FILENAME));
        }

        $io->success(\sprintf('Configuration file created successfully as `%s`.', self::FIXER_FILENAME));

        return Command::SUCCESS;
    }
}
