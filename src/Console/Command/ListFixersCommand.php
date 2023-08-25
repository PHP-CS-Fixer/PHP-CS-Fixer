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
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\FixerNameValidator;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSetInterface;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\ToolInfoInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Patrick Landolt <landolt@gmail.com>
 */
#[AsCommand(name: self::NAME)]
final class ListFixersCommand extends Command
{
    /** @var string */
    public const NAME = 'list-fixers';
    protected static $defaultName = self::NAME;

    /**
     * @var string
     */
    private const THICK = "\xE2\x9C\x94";

    /**
     * @var string
     */
    private const CROSS = "\xE2\x9C\x96";

    /**
     * @var string
     */
    private const PLUS = "\xe2\x9c\x9a";

    private ToolInfoInterface $toolInfo;

    private FixerFactory $fixerFactory;

    private FixerNameValidator $fixerNameValidator;

    private string $configName;

    private string $configFile;

    /** @var array<int, FixerInterface> */
    private array $availableFixers;

    /** @var array<int, FixerInterface> */
    private array $configuredFixers;

    /** @var array<int, FixerInterface> */
    private array $enabledFixers;

    /** @var array<int, FixerInterface> */
    private array $enabledFixersThroughInheritance;

    /** @var array<int, FixerInterface> */
    private array $undefinedFixers;

    /**
     * @var array<string, array{
     *   name: string,
     *   in_set?: array<string>,
     *   is_configured: bool,
     *   is_enabled: bool,
     *   is_inherited?: bool,
     *   is_enabled_through_inheritance: bool,
     *   is_risky: bool,
     *   is_inherited: bool,
     *   is_deprecated: bool,
     *   is_custom: bool
     * }>
     */
    private array $fixerList = [];

    private bool $hideConfigured;
    private bool $hideEnabled;
    private bool $hideRisky;
    private bool $hideInherited;
    private bool $hideDeprecated;
    private bool $hideCustom;
    private bool $hideInheritance;
    private int $countConfiguredFixers = 0;
    private int $countRiskyFixers = 0;
    private int $countEnabledFixers = 0;
    private int $countInheritedFixers = 0;
    private int $countDeprecatedFixers = 0;
    private int $countCustomFixers = 0;

    public function __construct(ToolInfoInterface $toolInfo, FixerFactory $fixerFactory = null)
    {
        parent::__construct(self::NAME);

        $this->toolInfo = $toolInfo;

        if (null === $fixerFactory) {
            $fixerFactory = new FixerFactory();
            $fixerFactory->registerBuiltInFixers();
        }

        $this->fixerFactory = $fixerFactory;

        $this->fixerNameValidator = new FixerNameValidator();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDefinition(
                [
                    new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The path to a config file.'),
                    new InputOption('hide-configured', '', InputOption::VALUE_NONE, 'Hide fixers that are configured (in the config file or because of inheritance).'),
                    new InputOption('hide-enabled', '', InputOption::VALUE_NONE, 'Hide fixers that are currently enabled (the ones that are not disabled with [\'fixer_name\' => false]).'),
                    new InputOption('hide-risky', '', InputOption::VALUE_NONE, 'Hide fixers that are marked as risky.'),
                    new InputOption('hide-inherited', '', InputOption::VALUE_NONE, 'Hide fixers that inherited from RuleSets.'),
                    new InputOption('hide-deprecated', '', InputOption::VALUE_NONE, 'Hide fixers that are deprecated.'),
                    new InputOption('hide-custom', '', InputOption::VALUE_NONE, 'Hide fixers that are custom.'),
                    new InputOption('hide-inheritance', '', InputOption::VALUE_NONE, 'Hide the addition inheritance information.'),
                    new InputOption('compare', '', InputOption::VALUE_NONE, 'Dumps the comparing result between your config and all available fixers in a copy-and-pastable format ready for the .php_cs file.'),
                ]
            )
            ->setDescription('Lists all available fixers and shows which ones are enabled, inherited or disabled.')
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> command lists all available fixers and shows which ones are enabled, inherited or disabled.

By default, the command searches for one of the default config files of PHP CS Fixer:

- <comment>.php_cs.php</comment>
- <comment>.php-cs-fixer.php</comment>
- <comment>.php_cs.dist.php</comment>
- <comment>.php-cs-fixer.dist.php</comment>

To use a custom config file, use the <comment>--config</comment> options, passing its pathname:

 <info>%command.name% --config path/to/.custom_phpcs</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->hideConfigured = $input->getOption('hide-configured');
        $this->hideEnabled = $input->getOption('hide-enabled');
        $this->hideRisky = $input->getOption('hide-risky');
        $this->hideInherited = $input->getOption('hide-inherited');
        $this->hideDeprecated = $input->getOption('hide-deprecated');
        $this->hideCustom = $input->getOption('hide-custom');
        $this->hideInheritance = $input->getOption('hide-inheritance');

        $resolver = $this->createResolver($input);

        $output->writeln('');
        $output->writeln(sprintf('  // Loaded config <comment>%s</comment> from <comment>%s</comment>.', $this->configName, $this->configFile));
        $output->writeln('');

        $this->availableFixers = array_merge($this->fixerFactory->getFixers(), $resolver->getConfig()->getCustomFixers());
        $this->configuredFixers = $resolver->getConfig()->getRules();
        $this->enabledFixers = $resolver->getRules();

        // fixers that are in enabled, but not in configured!
        $this->enabledFixersThroughInheritance = array_diff_key($this->enabledFixers, $this->configuredFixers);

        // Get the RuleSets and their Fixers
        $ruleSets = [];
        foreach (RuleSets::getSetDefinitions() as $setName => $set) {
            $ruleSets[$setName] = new RuleSet([$setName => true]);
        }

        // Order fixers alphabetically
        usort($this->availableFixers, static fn (FixerInterface $a, FixerInterface $b) => strcmp($a->getName(), $b->getName()));

        foreach ($this->availableFixers as $fixer) {
            $this->processFixer($fixer);
        }

        foreach ($ruleSets as $name => $set) {
            $this->processRuleSet($name, $set);
        }

        // Render the table
        $this->buildTable($output)->render();

        if ($input->getOption('compare')) {
            $this->calculateComparison();
            $this->dumpComparison($output);
        }

        return self::SUCCESS;
    }

    private function createResolver(InputInterface $input): ConfigurationResolver {
        $configFilePathname = $input->getOption('config');

        $resolver = new ConfigurationResolver(
            new Config(),
            [
                'config' => $configFilePathname,
            ],
            getcwd(),
            $this->toolInfo
        );

        $this->configName = $resolver->getConfig()->getName();
        $this->configFile = $resolver->getConfigFile();

        return $resolver;
    }

    private function processRuleSet(string $name, RuleSetInterface $set): void
    {
        /** @var bool $value */
        foreach ($set->getRules() as $rule => $value) {
            $this->fixerList[$rule]['in_set'][] = $name;
            $this->fixerList[$rule]['is_inherited'] = true;
        }
    }

    private function processFixer(FixerInterface $fixer): void
    {
        $this->fixerList[$fixer->getName()]['name'] = $fixer->getName();
        $this->fixerList[$fixer->getName()]['is_configured'] = $this->isFixerConfigured($fixer);
        $this->fixerList[$fixer->getName()]['is_enabled'] = $this->isFixerEnabled($fixer);
        $this->fixerList[$fixer->getName()]['is_enabled_through_inheritance'] = $this->isFixerEnabledThroughInheritance($fixer);
        $this->fixerList[$fixer->getName()]['is_risky'] = $this->isFixerRisky($fixer);
        $this->fixerList[$fixer->getName()]['is_inherited'] = false;
        $this->fixerList[$fixer->getName()]['is_deprecated'] = $this->isFixerDeprecated($fixer);
        $this->fixerList[$fixer->getName()]['is_custom'] = $this->isCustomFixer($fixer);
    }

    private function buildTable(OutputInterface $output): Table
    {
        $table = new Table($output);

        $rows = $this->filterFixers();
        $table->setRows($rows);

        $columns = [
            sprintf('Fixer (%d)', \count($rows)),
        ];

        if (!$this->hideConfigured) {
            $columns[] = sprintf('Configured (%d)', $this->countConfiguredFixers);
        }

        if (!$this->hideEnabled) {
            $columns[] = sprintf('Enabled (%d)', $this->countEnabledFixers);
        }

        if (!$this->hideRisky) {
            $columns[] = sprintf('Risky (%d)', $this->countRiskyFixers);
        }

        if (!$this->hideInherited) {
            $columns[] = sprintf('Inherited (%d)', $this->countInheritedFixers);
        }

        if (!$this->hideDeprecated) {
            $columns[] = sprintf('Deprecated (%d)', $this->countDeprecatedFixers);
        }

        if (!$this->hideCustom) {
            $columns[] = sprintf('Custom (%d)', $this->countCustomFixers);
        }

        $table->setHeaders([$columns]);

        return $table;
    }

    private function filterFixers(): array
    {
        $hideConfigured = $this->hideConfigured;
        $hideEnabled = $this->hideEnabled;
        $hideRisky = $this->hideRisky;
        $hideInherited = $this->hideInherited;
        $hideDeprecated = $this->hideDeprecated;
        $hideCustom = $this->hideCustom;

        $rows = array_filter($this->fixerList, function (array $fixer) use ($hideConfigured, $hideEnabled, $hideRisky, $hideInherited, $hideDeprecated, $hideCustom) {
            if ($fixer['is_configured']) {
                if ($hideConfigured) {
                    return false;
                }
                ++$this->countConfiguredFixers;
            }

            if ($fixer['is_enabled']) {
                if ($hideEnabled) {
                    return false;
                }
                ++$this->countEnabledFixers;
            }

            if ($fixer['is_risky']) {
                if ($hideRisky) {
                    return false;
                }
                ++$this->countRiskyFixers;
            }

            if ($fixer['is_inherited']) {
                if ($hideInherited) {
                    return false;
                }
                ++$this->countInheritedFixers;
            }

            if ($fixer['is_deprecated']) {
                if ($hideDeprecated) {
                    return false;
                }
                ++$this->countDeprecatedFixers;
            }

            if ($fixer['is_custom']) {
                if ($hideCustom) {
                    return false;
                }
                ++$this->countCustomFixers;
            }

            return true;
        });

        return array_map(function (array $fixer) {
            $path = null;
            if (isset($fixer['in_set'])) {
                $path = implode("\n  ", array_reverse($fixer['in_set']));
            }

            $color = '<fg=yellow>%s %s</>';
            $icon = self::PLUS;

            if ($fixer['is_enabled']) {
                $color = '<fg=green>%s %s</>';
                $icon = self::THICK;
            }

            if ($fixer['is_enabled_through_inheritance']) {
                $color = '<fg=green>%s %s (>)</>';
                $icon = self::THICK;
            }

            if (!$fixer['is_enabled'] && $fixer['is_configured']) {
                $color = '<fg=red>%s %s</>';
                $icon = self::CROSS;
            }

            $nameFormat = '<fg=green>%s</>';
            if (!$this->hideInheritance && null !== $path) {
                $nameFormat .= "\n  %s";
            }
            $name = sprintf($color, $icon, $fixer['name']);

            $row = [
                'name' => sprintf($nameFormat, $name, $path),
            ];

            if (!$this->hideConfigured) {
                $row['is_configured'] = $fixer['is_configured'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS);
            }

            if (!$this->hideEnabled) {
                $row['is_enabled'] = $fixer['is_enabled'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS);
            }

            if (!$this->hideRisky) {
                $row['is_risky'] = $fixer['is_risky'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS);
            }

            if (!$this->hideInherited) {
                $row['is_inherited'] = $fixer['is_inherited'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS);
            }

            if (!$this->hideDeprecated) {
                $row['is_deprecated'] = $fixer['is_deprecated'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS);
            }

            if (!$this->hideCustom) {
                $row['is_custom'] = $fixer['is_custom'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS);
            }

            return $row;
        }, $rows);
    }

    private function calculateComparison(): void
    {
        $this->undefinedFixers = array_diff_key($this->fixerList, $this->configuredFixers, $this->enabledFixers);
    }

    private function dumpComparison(OutputInterface $output): void
    {
        if ([] === $this->undefinedFixers) {
            $output->writeln("\nYou are aware of all existing rules! Yeah!");

            return;
        }

        $line = var_export(
            array_map(static fn () => false, $this->undefinedFixers),
            true
        );

        $output->writeln(
            sprintf(<<<EOF
                    Copy and paste the following <info>%d</info> undefined rules in your <comment>%s</comment> config file %s.

                    // Below the rules I don't want to use
                    %s;
                    // END Rules to never use
                    EOF,
                \count($this->undefinedFixers),
                $this->configName,
                $this->configFile,
                $line
            )
        );
    }

    private function isFixerConfigured(FixerInterface $fixer): bool
    {
        return isset($this->configuredFixers[$fixer->getName()]);
    }

    private function isFixerEnabled(FixerInterface $fixer): bool
    {
        return isset($this->enabledFixers[$fixer->getName()]);
    }

    private function isFixerEnabledThroughInheritance(FixerInterface $fixer): bool
    {
        return isset($this->enabledFixersThroughInheritance[$fixer->getName()]);
    }

    private function isFixerRisky(FixerInterface $fixer): bool
    {
        return $fixer->isRisky();
    }

    private function isFixerDeprecated(FixerInterface $fixer): bool
    {
        return $fixer instanceof DeprecatedFixerInterface;
    }

    private function isCustomFixer(FixerInterface $fixer): bool
    {
        return $this->fixerNameValidator->isValid($fixer->getName(), true);
    }
}
