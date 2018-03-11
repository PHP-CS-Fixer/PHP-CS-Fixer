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

    /** @var FixerFactory $fixerFactory */
    private $fixerFactory;

    /** @var array $builtInFixers */
    private $builtInFixers;

    /** @var array $configuredFixers */
    private $configuredFixers;

    /** @var array $enabledFixers */
    private $enabledFixers;

    /** @var array $list Stores all Fixers */
    private $list = [];

    /** @var bool $hideConfigured */
    private $hideConfigured;

    /** @var bool $hideEnabled */
    private $hideEnabled;

    /** @var bool $hideRisky */
    private $hideRisky;

    /** @var bool $hideRisky */
    private $hideInherited;

    /** @var bool $hideDeprecated */
    private $hideDeprecated;

    /** @var bool $hideInheritance */
    private $hideInheritance;

    /** @var int $countConfiguredFixers */
    private $countConfiguredFixers = 0;

    /** @var int $countRiskyFixers */
    private $countRiskyFixers = 0;

    /** @var int $countEnabledFixers */
    private $countEnabledFixers = 0;

    /** @var int $countInheritedFixers */
    private $countInheritedFixers = 0;

    /** @var int $countDeprecatedFixers */
    private $countDeprecatedFixers = 0;

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
                    new InputOption('hide-inheritance', '', InputOption::VALUE_NONE, 'Hide the addition inheritance information.'),
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
        $this->hideInheritance = $input->getOption('hide-inheritance');

        $resolver = new ConfigurationResolver(
            new Config(),
            [
                'config' => $input->getOption('config'),
            ],
            getcwd(),
            $this->toolInfo
        );

        $output->writeln(sprintf('Loaded config <comment>%s</comment> from %s.', $resolver->getConfig()->getName(), $resolver->getConfigFile()));

        $this->builtInFixers = $this->fixerFactory->getFixers();
        $this->configuredFixers = $resolver->getConfig()->getRules();
        $this->enabledFixers = $resolver->getRules();

        // Get the RuleSets and their Fixers
        foreach (RuleSet::create()->getSetDefinitionNames() as $setName) {
            $ruleSets[$setName] = new RuleSet([$setName => true]);
        }

        // Order fixers alphabetically
        usort($this->builtInFixers, function (FixerInterface $a, FixerInterface $b) {
            return strcmp($a->getName(), $b->getName());
        });

        /** @var FixerInterface $fixer */
        foreach ($this->builtInFixers as $fixer) {
            $this->processFixer($fixer);
        }

        /**
         * @var string
         * @var RuleSet $set
         */
        foreach ($ruleSets as $name => $set) {
            $this->processRuleSet($name, $set);
        }

        // Render the table
        $this->buildTable($output)->render();
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
            $this->list[$rule]['in_set'][] = $name;
            $this->list[$rule]['is_inherited'] = true;
        }
    }

    /**
     * @param FixerInterface $fixer
     */
    private function processFixer(FixerInterface $fixer)
    {
        $this->list[$fixer->getName()]['name'] = $fixer->getName();
        $this->list[$fixer->getName()]['is_configured'] = $this->isFixerConfigured($fixer);
        $this->list[$fixer->getName()]['is_enabled'] = $this->isFixerEnabled($fixer);
        $this->list[$fixer->getName()]['is_risky'] = $this->isFixerRisky($fixer);
        $this->list[$fixer->getName()]['is_inherited'] = false;
        $this->list[$fixer->getName()]['is_deprecated'] = $this->isFixerDeprecated($fixer);
//        $this->list[$fixer->getName()]['is_custom'] = false;
    }

    /**
     * @param OutputInterface $output
     *
     * @return Table
     */
    private function buildTable(OutputInterface $output)
    {
        $table = new Table($output);

        $table->setRows($this->filterFixers());

        $columns = [
            sprintf('Fixer (%d)', \count($this->builtInFixers)),
            sprintf('Configured (%d)', $this->countConfiguredFixers),
            sprintf('Enabled (%d)', $this->countEnabledFixers),
            sprintf('Risky (%d)', $this->countRiskyFixers),
            sprintf('Inherited (%d)', $this->countInheritedFixers),
            sprintf('Deprecated (%d)', $this->countDeprecatedFixers),
//            sprintf("Custom (%d)", 0),
        ];
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

        $rows = array_filter($this->list, function (array $fixer) use ($hideConfigured, $hideEnabled, $hideRisky, $hideInherited, $hideDeprecated) {
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

            if ($fixer['is_deprecated']) {
                if ($hideDeprecated) {
                    return false;
                }
                ++$this->countDeprecatedFixers;
            }

            if ($fixer['is_inherited']) {
                if ($hideInherited) {
                    return false;
                }
                ++$this->countInheritedFixers;
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

            if (!$fixer['is_enabled'] && $fixer['is_configured']) {
                $color = '<fg=red>%s %s</>';
                $icon = self::CROSS;
            }

            $nameFormat = '<fg=green>%s</>';
            if (!$this->hideInheritance && null !== $path) {
                $nameFormat .= "\n  %s";
            }
            $name = sprintf($color, $icon, $fixer['name']);

            return [
                'name' => sprintf($nameFormat, $name, $path),
                'is_configured' => $fixer['is_configured'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
                'is_enabled' => $fixer['is_enabled'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
                'is_risky' => $fixer['is_risky'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
                'is_inherited' => $fixer['is_inherited'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
                'is_deprecated' => $fixer['is_deprecated'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
//                'is_custom' => $fixer['is_custom'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
            ];
        }, $rows);
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
}
