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
use PhpCsFixer\Preg;
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
    private const GITIGNORE_FILENAME = '.gitignore';

    public function __construct()
    {
        parent::__construct('init');
        $this->setDescription('Create config file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stdErr = $output;

        if ($output instanceof ConsoleOutputInterface) {
            $stdErr = $output->getErrorOutput();
            $stdErr->writeln(Application::getAboutWithRuntime(true));
        }

        $io = new SymfonyStyle($input, $stdErr);

        $io->warning('This command is experimental');

        $this->handleConfigurationFile($io);
        $this->handleGitIgnore($io);

        return Command::SUCCESS;
    }

    private function handleConfigurationFile(SymfonyStyle $io): void
    {
        $io->title('⚙️ Configuring PHP CS Fixer');

        if (file_exists(self::FIXER_FILENAME)) {
            $io->note(\sprintf('Configuration file `%s` already exists. Skipping.', self::FIXER_FILENAME));

            return;
        }

        $configurationFileContent = $this->prepareConfigurationFileContent($io);
        $this->writeFile(self::FIXER_FILENAME, $configurationFileContent);
        $io->success(\sprintf('Configuration file created as `%s`.', self::FIXER_FILENAME));
    }

    private function handleGitIgnore(SymfonyStyle $io): void
    {
        $io->title('⚙️ Polishing GIT integration');

        $gitignoreFileExists = file_exists(self::GITIGNORE_FILENAME);
        $gitignoreFileContent = $this->prepareGitIgnoreContent($io, true === $gitignoreFileExists ? $this->readFile(self::GITIGNORE_FILENAME) : '');

        if (null !== $gitignoreFileContent) {
            $this->writeFile(self::GITIGNORE_FILENAME, $gitignoreFileContent);
        }

        $io->success(\sprintf(
            '%s file %s.',
            self::GITIGNORE_FILENAME,
            null !== $gitignoreFileContent ? ($gitignoreFileExists ? 'updated' : 'created') : 'already up to recommendations',
        ));
    }

    private function prepareConfigurationFileContent(SymfonyStyle $io): string
    {
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

        $io->note("We recommend usage of {$setAutoWithOptionalRiskySetNamesTextual} rulesets. They take insights from your existing `composer.json` to configure project the best. For your current setup, that would mean:");

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
                    self::formatReference($item->getDescription()),
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
                    static fn (string $item): string => self::formatReference($setsByName[$item]->getDescription()), // @phpstan-ignore-line offsetAccess.notFound
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

        $io->note([
            'By default, PHP CS Fixer will looks for `*.php` files excluding `./vendor/` dir.',
        ]);
        $useDefaultFinder = 'yes' === $io->choice(
            'Do you want to rely on default file finder, or do you want to customize it?',
            ['yes' => 'default', 'no' => 'customizable'],
            'yes',
        );

        $readResult = @file_get_contents(__DIR__.'/../../../resources/.php-cs-fixer.dist.php.template');
        if (false === $readResult) {
            throw new IOException('Failed to read template file.');
        }

        return str_replace(
            [
                '/*{{ IS_RISKY_ALLOWED }}*/',
                '/*{{ RULES }}*/',
                '/*{{ CUSTOMIZABLE_FINDER }}*/',
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
                $useDefaultFinder
                    ? ''
                    : "// 💡 additional files, eg bin entry file
            // ->append([__DIR__.'/bin-entry-file'])
            // 💡 folders to exclude, if any
            // ->exclude([/* ... */])
            // 💡 path patterns to exclude, if any
            // ->notPath([/* ... */])
            // 💡 extra configs
            // ->ignoreDotFiles(false) // true by default in v3, false in v4 or future mode
            // ->ignoreVCS(true) // true by default",
            ],
            $readResult,
        );
    }

    private function prepareGitIgnoreContent(SymfonyStyle $io, string $currentContent): ?string
    {
        $io->note([
            'We recommend to add following entries to your `.gitignore` files:',
        ]);

        $entries = [
            [
                'name' => '.php-cs-fixer.cache',
                'description' => 'Cache file allowing to skip unchanged files on subsequent runs',
                'exists' => true,
            ],
            [
                'name' => '.php-cs-fixer.php',
                'description' => 'The local configuration that will take precedence over ``.php-cs-fixer.dist.php`` configuration',
                'exists' => true,
            ],
        ];

        $entriesToAdd = [];

        foreach ($entries as &$entry) {
            $entry['exists'] = str_contains($currentContent, $entry['name']);

            if (false === $entry['exists']) {
                $entriesToAdd[] = '/'.$entry['name'];
            }
        }

        $io->listing(
            array_map(
                static fn (array $entry): string => \sprintf(
                    '<fg=blue>`%s`</> - %s %s',
                    $entry['name'],
                    self::formatReference($entry['description']),
                    true === $entry['exists'] ? '(already present)' : '(will be added)',
                ),
                $entries,
            ),
        );

        if ([] === $entriesToAdd) {
            return null;
        }

        return $currentContent."\n# PHP CS Fixer configuration\n".implode("\n", $entriesToAdd)."\n";
    }

    private function writeFile(string $filename, string $content): void
    {
        $result = @file_put_contents($filename, $content);
        if (false === $result) {
            throw new IOException(\sprintf('Failed to write file "%s".', $filename));
        }
    }

    private function readFile(string $filename): string
    {
        $result = @file_get_contents($filename);
        if (false === $result) {
            throw new IOException(\sprintf('Failed to read file "%s".', $filename));
        }

        return $result;
    }

    private static function formatReference(string $text): string
    {
        $text = Preg::replace(
            '/``(.+?)``/',
            '<fg=blue>$1</>',
            $text,
        );

        return Preg::replace(
            '/`(.+?) <(.+?)>`_/',
            '<href=$2;fg=bright-blue;options=underscore>$1 ($2)</>',
            $text,
        );
    }
}
