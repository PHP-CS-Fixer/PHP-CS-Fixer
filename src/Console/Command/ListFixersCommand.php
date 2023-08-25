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

    public const OPT_CONFIG = 'config';
    public const OPT_ONLY_CONFIGURED = 'only-configured';
    public const OPT_HIDE_CONFIGURED = 'hide-configured';
    public const OPT_HIDE_ENABLED = 'hide-enabled';
    public const OPT_HIDE_RISKY = 'hide-risky';
    public const OPT_HIDE_INHERITED = 'hide-inherited';
    public const OPT_HIDE_DEPRECATED = 'hide-deprecated';
    public const OPT_HIDE_CUSTOM = 'hide-custom';
    public const OPT_HIDE_INHERITANCE = 'hide-inheritance';
    public const OPT_COMPARE = 'compare';

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

    private bool $onlyConfigured;
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
        $optConfig = self::OPT_CONFIG;
        $optOnlyConfigured = self::OPT_ONLY_CONFIGURED;
        $optHideConfigured = self::OPT_HIDE_CONFIGURED;
        $optHideEnabled = self::OPT_HIDE_ENABLED;
        $optHideRisky = self::OPT_HIDE_RISKY;
        $optHideInherited = self::OPT_HIDE_INHERITED;
        $optHideDeprecated = self::OPT_HIDE_DEPRECATED;
        $optHideCustom = self::OPT_HIDE_CUSTOM;
        $optHideInheritance = self::OPT_HIDE_INHERITANCE;
        $optCompare = self::OPT_COMPARE;

        $this
            ->setDefinition(
                [
                    new InputOption(self::OPT_CONFIG, '', InputOption::VALUE_REQUIRED, 'The path to a config file.'),
                    new InputOption(self::OPT_ONLY_CONFIGURED, '', InputOption::VALUE_NONE, 'Show only fixers configured explicitly'),
                    new InputOption(self::OPT_HIDE_CONFIGURED, '', InputOption::VALUE_NONE, 'Hide fixers that are configured (in the config file or because of inheritance).'),
                    new InputOption(self::OPT_HIDE_ENABLED, '', InputOption::VALUE_NONE, 'Hide fixers that are currently enabled (the ones that are not disabled with [\'fixer_name\' => false]).'),
                    new InputOption(self::OPT_HIDE_RISKY, '', InputOption::VALUE_NONE, 'Hide fixers that are marked as risky.'),
                    new InputOption(self::OPT_HIDE_INHERITED, '', InputOption::VALUE_NONE, 'Hide fixers that inherited from RuleSets.'),
                    new InputOption(self::OPT_HIDE_DEPRECATED, '', InputOption::VALUE_NONE, 'Hide fixers that are deprecated.'),
                    new InputOption(self::OPT_HIDE_CUSTOM, '', InputOption::VALUE_NONE, 'Hide fixers that are custom.'),
                    new InputOption(self::OPT_HIDE_INHERITANCE, '', InputOption::VALUE_NONE, 'Hide the addition inheritance information.'),
                    new InputOption(self::OPT_COMPARE, '', InputOption::VALUE_NONE, 'Dumps the comparing result between your config and all available fixers in a copy-and-pastable format ready for the .php_cs file.'),
                ]
            )
            ->setDescription('Lists all available fixers and shows which ones are enabled, inherited or disabled.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command lists all available fixers and shows which ones are enabled, inherited or disabled.

By default, the command searches for one of the default config files of PHP CS Fixer:

- <comment>.php_cs.php</comment>
- <comment>.php-cs-fixer.php</comment>
- <comment>.php_cs.dist.php</comment>
- <comment>.php-cs-fixer.dist.php</comment>

To use a custom config file, use the <comment>--$optConfig</comment> option, passing its pathname:

 <info>%command.name% --$optConfig path/to/.custom_phpcs</info>

<comment>HIDING FIXERS</comment>
<comment>=============</comment>

When you run the command without any option, it will show all fixers that PHP CS Fixer can find, also if they are not present in the loaded config file.

To analyze only configured fixers, pass the <comment>--$optOnlyConfigured</comment> option:

 <info>%command.name% --$optOnlyConfigured</info>

If you pass this option, the report will only list the fixers that are explicitly configured.

The command provides also a lot of filters useful to hide fixers of some kind.

Passing <comment>--$optHideConfigured</comment> option, the report will not show fixers that are configured:

 <info>%command.name% --$optHideConfigured</info>

NOTE: The <comment>--$optOnlyConfigured</comment> and <comment>--$optHideConfigured</comment> options cannot be used together.

Below the name of each fixer you can read the sets that enable it. To hide this information, pass the <comment>--$optHideInheritance</comment> option:

 <info>%command.name% --$optHideInheritance</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configureOptions($input);
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

    private function configureOptions(InputInterface $input): void
    {
        $this->onlyConfigured = $input->getOption(self::OPT_ONLY_CONFIGURED);
        $this->hideConfigured = $input->getOption(self::OPT_HIDE_CONFIGURED);
        $this->hideEnabled = $input->getOption(self::OPT_HIDE_ENABLED);
        $this->hideRisky = $input->getOption(self::OPT_HIDE_RISKY);
        $this->hideInherited = $input->getOption(self::OPT_HIDE_INHERITED);
        $this->hideDeprecated = $input->getOption(self::OPT_HIDE_DEPRECATED);
        $this->hideCustom = $input->getOption(self::OPT_HIDE_CUSTOM);
        $this->hideInheritance = $input->getOption(self::OPT_HIDE_INHERITANCE);

        if ($this->onlyConfigured && $this->hideConfigured) {
            throw new \InvalidArgumentException(sprintf('The options "--%s" and "--%s" cannot be used together.', self::OPT_ONLY_CONFIGURED, self::OPT_HIDE_CONFIGURED));
        }
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
        $rows = array_filter($this->fixerList, function (array $fixer) {
            if ($this->onlyConfigured && false === $fixer['is_configured']) {
                return false;
            }

            if ($fixer['is_configured']) {
                if ($this->hideConfigured) {
                    return false;
                }
                ++$this->countConfiguredFixers;
            }

            if ($fixer['is_enabled']) {
                if ($this->hideEnabled) {
                    return false;
                }
                ++$this->countEnabledFixers;
            }

            if ($fixer['is_risky']) {
                if ($this->hideRisky) {
                    return false;
                }
                ++$this->countRiskyFixers;
            }

            if ($fixer['is_inherited']) {
                if ($this->hideInherited) {
                    return false;
                }
                ++$this->countInheritedFixers;
            }

            if ($fixer['is_deprecated']) {
                if ($this->hideDeprecated) {
                    return false;
                }
                ++$this->countDeprecatedFixers;
            }

            if ($fixer['is_custom']) {
                if ($this->hideCustom) {
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
