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
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Console\ConfigurationResolver;
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
 */
final class ShowCommand extends Command
{
    const COMMAND_NAME = 'show';
    const THICK = "\xE2\x9C\x94";
    const CROSS = "\xE2\x9C\x96";
    const PLUS = "\xe2\x9c\x9a";

    /** @var ToolInfoInterface */
    private $toolInfo;

    /** @var ConfigInterface $defaultConfig */
    private $defaultConfig;

    /** @var FixerFactory $fixerFactory */
    private $fixerFactory;

    /** @var bool $hideConfigured */
    private $hideConfigured;

    /** @var bool $hideEnabled */
    private $hideEnabled;

    /** @var bool $hideRisky */
    private $hideRisky;

    /** @var bool $hideRisky */
    private $hideInherited;

    /** @var bool $hidePath */
    private $hidePath;

    /** @var array $builtInFixers */
    private $builtInFixers;

    /** @var array $configuredFixers */
    private $configuredFixers;

    /** @var array $enabledFixers */
    private $enabledFixers;

    /** @var array $riskyFixers */
    private $riskyFixers;

    /** @var int $countInherited */
    private $countInherited = 0;

    /** @var array $ruleSets Stores all the RuleSets and the Fixers they have */
    private $ruleSets = [];

    /** @var array $list */
    private $list = [];

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
        $this->defaultConfig = new Config();
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
                    new InputOption('hide-path', '', InputOption::VALUE_NONE, 'Hide the addition path information.'),
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
        /**
         * @var string
         * @var RuleSet $set
         */
        foreach ($this->ruleSets as $name => $set) {
            $this->processRuleSet($name, $set);
        }

        /** @var FixerInterface $fixer */
        foreach ($this->builtInFixers as $fixer) {
            $this->processFixer($fixer);
        }

        // Render the table
        $this->buildTable($output)->render();
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $resolver = new ConfigurationResolver(
            $this->defaultConfig,
            [
                'allow-risky' => true,
                'config' => $input->getOption('config'),
            ],
            getcwd(),
            $this->toolInfo
        );

        $this->builtInFixers = $this->fixerFactory->getFixers();
        $this->configuredFixers = $resolver->getConfig()->getRules();
        $this->enabledFixers = $resolver->getRules();

        // Get the RuleSets and their Fixers
        foreach (RuleSet::create()->getSetDefinitionNames() as $setName) {
            $this->ruleSets[$setName] = new RuleSet([$setName => true]);
        }

        // Order alphabetically
        usort($this->builtInFixers, function (FixerInterface $a, FixerInterface $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $this->hideConfigured = $input->getOption('hide-configured');
        $this->hideEnabled = $input->getOption('hide-enabled');
        $this->hideRisky = $input->getOption('hide-risky');
        $this->hideInherited = $input->getOption('hide-inherited');
        $this->hidePath = $input->getOption('hide-path');
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
            if (false === isset($this->list[$rule]['in_set'])) {
                $this->list[$rule]['in_set'] = [];
            }
            $this->list[$rule]['in_set'][] = $name;
        }
    }

    /**
     * @param FixerInterface $fixer
     */
    private function processFixer(FixerInterface $fixer)
    {
        $isInherited = !empty($this->list[$fixer->getName()]['in_set']);

        if ($isInherited) {
            ++$this->countInherited;
        }

        $this->list[$fixer->getName()]['name'] = $fixer->getName();
        $this->list[$fixer->getName()]['is_configured'] = $this->isFixerConfigured($fixer);
        $this->list[$fixer->getName()]['is_enabled'] = $this->isFixerEnabled($fixer);
        $this->list[$fixer->getName()]['is_risky'] = $this->isFixerRisky($fixer);
        $this->list[$fixer->getName()]['is_inherited'] = $isInherited;

        if ($this->isFixerRisky($fixer)) {
            $this->riskyFixers[] = $fixer->getName();
        }
    }

    /**
     * @param OutputInterface $output
     *
     * @return Table
     */
    private function buildTable(OutputInterface $output)
    {
        $table = new Table($output);

        $columns = [
            sprintf('Fixer (%s)', count($this->builtInFixers)),
            sprintf("Configured (%s)", count($this->configuredFixers)),
            sprintf("Enabled (%s)", count($this->enabledFixers)),
            sprintf("Risky (%s)", count($this->riskyFixers)),
            sprintf("Inherited (%s)", $this->countInherited),
        ];

        $table->setHeaders([$columns]);

        $table->setRows($this->filterFixers());

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

        $rows = array_filter($this->list, function (array $fixer) use ($hideConfigured, $hideEnabled, $hideRisky, $hideInherited) {
            if ($hideConfigured && $fixer['is_configured']) {
                return false;
            }

            if ($hideEnabled && $fixer['is_enabled']) {
                return false;
            }

            if ($hideRisky && $fixer['is_risky']) {
                return false;
            }

            if ($hideInherited && $fixer['is_inherited']) {
                return false;
            }

            return true;
        });

        return array_map(function (array $fixer) {
            $path = null;
            if (isset($fixer['in_set'])) {
                $path = implode("\n  ", array_reverse($fixer['in_set']));
            }

            $color = '<fg=green>%s %s</>';
            $icon = self::THICK;

            if ($fixer['is_inherited']) {
                $color = '<fg=yellow>%s %s</>';
                $icon = self::PLUS;
            }

            if (false === $fixer['is_enabled']) {
                $color = '<fg=red>%s %s</>';
                $icon = self::CROSS;
            }

            $nameFormat = '<fg=green>%s</>';
            if (!$this->hidePath && null !== $path) {
                $nameFormat .= "\n  %s";
            }
            $name = sprintf($color, $icon, $fixer['name']);

            return [
                'name' => sprintf($nameFormat, $name, $path),
                'is_configured' => $fixer['is_configured'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
                'is_enabled' => $fixer['is_enabled'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
                'is_risky' => $fixer['is_risky'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
                'is_inherited' => $fixer['is_inherited'] ? sprintf('<fg=green;>%s</>', self::THICK) : sprintf('<fg=red;>%s</>', self::CROSS),
                //'in_set' => $path,
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
    private function isFixerRisky(FixerInterface $fixer)
    {
        return $fixer->isRisky();
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
}
