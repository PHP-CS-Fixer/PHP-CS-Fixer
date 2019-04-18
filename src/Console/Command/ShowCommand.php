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
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\FixerNameValidator;
use PhpCsFixer\RuleSet;
use PhpCsFixer\RuleSetInterface;
use PhpCsFixer\ToolInfoInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Patrick Landolt <landolt@gmail.com>
 */
final class ShowCommand extends Command
{
    const COMMAND_NAME = 'show';
    const THICK = "\xE2\x9C\x94";
    const CROSS = "\xE2\x9C\x96";
    const PLUS = "\xe2\x9c\x9a";

    /** @var ToolInfoInterface */
    private $toolInfo;

    /** @var FixerFactory */
    private $fixerFactory;

    /** @var FixerNameValidator */
    private $fixerNameValidator;

    /** @var string */
    private $configName;

    /** @var string */
    private $configFile;

    /** @var FixerInterface[] */
    private $availableFixers;

    /** @var FixerInterface[] */
    private $configuredFixers;

    /** @var FixerInterface[] */
    private $enabledFixers;

    /** @var FixerInterface[] */
    private $enabledFixersTroughInheritance;

    /** @var FixerInterface[] */
    private $undefinedFixers;

    /** @var array */
    private $fixerList = [];

    /** @var bool */
    private $hideConfigured;

    /** @var bool */
    private $hideEnabled;

    /** @var bool */
    private $hideRisky;

    /** @var bool */
    private $hideInherited;

    /** @var bool */
    private $hideDeprecated;

    /** @var bool */
    private $hideCustom;

    /** @var bool */
    private $hideInheritance;

    /** @var int */
    private $countConfiguredFixers = 0;

    /** @var int */
    private $countRiskyFixers = 0;

    /** @var int */
    private $countEnabledFixers = 0;

    /** @var int */
    private $countInheritedFixers = 0;

    /** @var int */
    private $countDeprecatedFixers = 0;

    /** @var int */
    private $countCustomFixers = 0;

    /**
     * @param null|FixerFactory $fixerFactory
     */
    public function __construct(ToolInfoInterface $toolInfo, FixerFactory $fixerFactory = null)
    {
        parent::__construct();

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
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDefinition(
                [
                    new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The path to a .php_cs file.'),
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
            ->setDescription('Shows existent Fixers with the ones actually configured or enabled by inheritance.')
        ;
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

        $resolver = new ConfigurationResolver(
            new Config(),
            [
                'config' => $input->getOption('config'),
            ],
            getcwd(),
            $this->toolInfo
        );

        $this->configName = $resolver->getConfig()->getName();
        $this->configFile = $resolver->getConfigFile();

        $output->writeln(sprintf('Loaded config <comment>%s</comment> from %s.', $this->configName, $this->configFile));

        $this->availableFixers = array_merge($this->fixerFactory->getFixers(), $resolver->getConfig()->getCustomFixers());
        $this->configuredFixers = $resolver->getConfig()->getRules();
        $this->enabledFixers = $resolver->getRules();

        // fixers that are in enabled, but not in configured!
        $this->enabledFixersTroughInheritance = array_diff_key($this->enabledFixers, $this->configuredFixers);

        // Get the RuleSets and their Fixers
        foreach (RuleSet::create()->getSetDefinitionNames() as $setName) {
            $ruleSets[$setName] = new RuleSet([$setName => true]);
        }

        // Order fixers alphabetically
        usort($this->availableFixers, function (FixerInterface $a, FixerInterface $b) {
            return strcmp($a->getName(), $b->getName());
        });

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
    }

    /**
     * @param string           $name
     * @param RuleSetInterface $set
     */
    private function processRuleSet($name, RuleSetInterface $set)
    {
        /**
         * @var string
         * @var bool   $value
         */
        foreach ($set->getRules() as $rule => $value) {
            $this->fixerList[$rule]['in_set'][] = $name;
            $this->fixerList[$rule]['is_inherited'] = true;
        }
    }

    /**
     * @param FixerInterface $fixer
     */
    private function processFixer(FixerInterface $fixer)
    {
        $this->fixerList[$fixer->getName()]['name'] = $fixer->getName();
        $this->fixerList[$fixer->getName()]['is_configured'] = $this->isFixerConfigured($fixer);
        $this->fixerList[$fixer->getName()]['is_enabled'] = $this->isFixerEnabled($fixer);
        $this->fixerList[$fixer->getName()]['is_enabled_trough_inheritance'] = $this->isFixerEnabledTroughInheritance($fixer);
        $this->fixerList[$fixer->getName()]['is_risky'] = $this->isFixerRisky($fixer);
        $this->fixerList[$fixer->getName()]['is_inherited'] = false;
        $this->fixerList[$fixer->getName()]['is_deprecated'] = $this->isFixerDeprecated($fixer);
        $this->fixerList[$fixer->getName()]['is_custom'] = $this->isCustomFixer($fixer);
    }

    /**
     * @param OutputInterface $output
     *
     * @return Table
     */
    private function buildTable(OutputInterface $output)
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

    /**
     * @return array
     */
    private function filterFixers()
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

            if ($fixer['is_enabled_trough_inheritance']) {
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

    private function calculateComparison()
    {
        $this->undefinedFixers = array_diff_key($this->fixerList, $this->configuredFixers, $this->enabledFixers);
    }

    /**
     * @param OutputInterface $output
     */
    private function dumpComparison(OutputInterface $output)
    {
        if (empty($this->undefinedFixers)) {
            $output->writeln("\nYou are aware of all exsisting rules! Yeah!");

            return;
        }

        $line = var_export(
            array_map(function () {return false; }, $this->undefinedFixers),
            true
        );

        $output->writeln(
            sprintf(
                "\nCopy and paste the following <info>%d</info> undefined rules in your <comment>%s</comment> config file %s.\n\n"
            ."// Below the rules I don't want to use\n"
            ."%s;\n"
            .'// END Rules to never use',
                \count($this->undefinedFixers),
                $this->configName,
                $this->configFile,
                $line
        )
        );
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerConfigured(FixerInterface $fixer)
    {
        return isset($this->configuredFixers[$fixer->getName()]);
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerEnabled(FixerInterface $fixer)
    {
        return isset($this->enabledFixers[$fixer->getName()]);
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerEnabledTroughInheritance(FixerInterface $fixer)
    {
        return isset($this->enabledFixersTroughInheritance[$fixer->getName()]);
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerRisky(FixerInterface $fixer)
    {
        return $fixer->isRisky();
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerDeprecated(FixerInterface $fixer)
    {
        return $fixer instanceof DeprecatedFixerInterface;
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isCustomFixer(FixerInterface $fixer)
    {
        return $this->fixerNameValidator->isValid($fixer->getName(), true);
    }
}
