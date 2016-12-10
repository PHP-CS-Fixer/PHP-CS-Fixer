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

use PhpCsFixer\Differ\DiffConsoleFormatter;
use PhpCsFixer\Differ\SebastianBergmannDiffer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSampleInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\ShortFixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class DescribeCommand extends Command
{
    /**
     * @var string[]
     */
    private $setNames;

    /**
     * @var array<string, FixerInterface>
     */
    private $fixers;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('describe')
            ->setDefinition(
                array(
                    new InputArgument('name', InputArgument::REQUIRED, 'Name of rule / set.'),
                )
            )
            ->setDescription('Describe rule / ruleset.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        try {
            if ('@' === $name[0]) {
                $this->describeSet($output, $name);

                return;
            }

            $this->describeRule($output, $name);
        } catch (DescribeNameNotFoundException $e) {
            $alternative = $this->getAlternative($e->getType(), $name);
            $this->describeList($output, $e->getType());

            throw new \InvalidArgumentException(sprintf(
                '%s %s not found.%s',
                ucfirst($e->getType()), $name, null === $alternative ? '' : ' Did you mean "'.$alternative.'"?'
            ));
        }
    }

    /**
     * @param OutputInterface $output
     * @param string          $name
     */
    private function describeRule(OutputInterface $output, $name)
    {
        $fixers = $this->getFixers();

        if (!isset($fixers[$name])) {
            throw new DescribeNameNotFoundException($name, 'rule');
        }

        /** @var FixerInterface $fixer */
        $fixer = $fixers[$name];
        if ($fixer instanceof DefinedFixerInterface) {
            $definition = $fixer->getDefinition();
        } else {
            $definition = new ShortFixerDefinition('Description is not available.');
        }

        $output->writeln(sprintf('<info>Description of</info> %s <info>rule</info>.', $name));
        $output->writeln($definition->getSummary());
        if ($definition->getDescription()) {
            $output->writeln($definition->getDescription());
        }
        $output->writeln('');

        if ($fixer->isRisky()) {
            $output->writeln('<error>Fixer applying this rule is risky.</error>');

            if ($definition->getRiskyDescription()) {
                $output->writeln($definition->getRiskyDescription());
            }

            $output->writeln('');
        }

        if ($fixer instanceof ConfigurableFixerInterface) {
            $output->writeln('<comment>Fixer is configurable.</comment>');

            if ($definition->getConfigurationDescription()) {
                $output->writeln($definition->getConfigurationDescription());
            }

            if ($definition->getDefaultConfiguration()) {
                $output->writeln(sprintf('Default configuration: <comment>%s</comment>.', $this->arrayToText($definition->getDefaultConfiguration())));
            }

            $output->writeln('');
        }

        $codeSamples = array_filter($definition->getCodeSamples(), function (CodeSampleInterface $codeSample) {
            if ($codeSample instanceof VersionSpecificCodeSampleInterface) {
                return $codeSample->isSuitableFor(PHP_VERSION_ID);
            }

            return true;
        });

        if (!count($codeSamples)) {
            $output->writeln(array(
                'Fixing examples can not be demonstrated on the current PHP version.',
                '',
            ));
        } else {
            $output->writeln('Fixing examples:');

            $differ = new SebastianBergmannDiffer();
            $diffFormatter = new DiffConsoleFormatter($output->isDecorated(), sprintf(
                '<comment>   ---------- begin diff ----------</comment>%s%%s%s<comment>   ----------- end diff -----------</comment>',
                PHP_EOL,
                PHP_EOL
            ));

            foreach ($codeSamples as $index => $codeSample) {
                $old = $codeSample->getCode();
                $tokens = Tokens::fromCode($old);
                if ($fixer instanceof ConfigurableFixerInterface) {
                    $fixer->configure($codeSample->getConfiguration());
                }

                $fixer->fix(new StdinFileInfo(), $tokens);
                $new = $tokens->generateCode();
                $diff = $differ->diff($old, $new);

                if (null === $codeSample->getConfiguration()) {
                    $output->writeln(sprintf(' * Example #%d.', $index + 1));
                } else {
                    $output->writeln(sprintf(' * Example #%d. Fixing with configuration: <comment>%s</comment>.', $index + 1, $this->arrayToText($codeSample->getConfiguration())));
                }
                $output->writeln($diffFormatter->format($diff, '   %s'));
                $output->writeln('');
            }
        }

        if ($definition instanceof ShortFixerDefinition) {
            $output->writeln(sprintf('<question>This rule is not yet described, do you want to help us and describe it?</question>'));
            $output->writeln('Contribute at <comment>https://github.com/FriendsOfPHP/PHP-CS-Fixer</comment> !');
            $output->writeln('');
        }
    }

    /**
     * @param OutputInterface $output
     * @param string          $name
     */
    private function describeSet(OutputInterface $output, $name)
    {
        if (!in_array($name, $this->getSetNames(), true)) {
            throw new DescribeNameNotFoundException($name, 'set');
        }

        $ruleSet = new RuleSet(array($name => true));
        $rules = $ruleSet->getRules();
        ksort($rules);

        $fixers = $this->getFixers();

        $output->writeln(sprintf('<info>Description of</info> %s <info>set.</info>', $name));
        $output->writeln('');

        $help = '';

        foreach ($rules as $rule => $config) {
            /** @var FixerDefinitionInterface $definition */
            $definition = $fixers[$rule]->getDefinition();
            $help .= sprintf(
                " * <info>%s</info>%s\n   | %s\n%s\n",
                $rule,
                $fixers[$rule]->isRisky() ? ' <error>risky</error>' : '',
                $definition->getSummary(),
                true !== $config ? sprintf("   <comment>| Configuration: %s</comment>\n", $this->arrayToText($config)) : ''
            );
        }

        $output->write($help);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function arrayToText(array $data)
    {
        // Output modifications:
        // - remove new-lines
        // - combine multiple whitespaces
        // - switch array-syntax to short array-syntax
        // - remove whitespace at array opening
        // - remove trailing array comma and whitespace at array closing
        // - remove numeric array indexes
        static $replaces = array(
            array('#\r|\n#', '#\s{1,}#', '#array\s*\((.*)\)#s', '#\[\s+#', '#,\s*\]#', '#\d+\s*=>\s*#'),
            array('', ' ', '[$1]', '[', ']', ''),
        );

        return preg_replace(
            $replaces[0],
            $replaces[1],
            var_export($data, true)
        );
    }

    /**
     * @return array<string, FixerInterface>
     */
    private function getFixers()
    {
        if (null !== $this->fixers) {
            return $this->fixers;
        }

        $fixerFactory = new FixerFactory();
        $fixers = array();

        foreach ($fixerFactory->registerBuiltInFixers()->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $this->fixers = $fixers;
        ksort($this->fixers);

        return $this->fixers;
    }

    /**
     * @return string[]
     */
    private function getSetNames()
    {
        if (null !== $this->setNames) {
            return $this->setNames;
        }

        $set = new RuleSet();
        $this->setNames = $set->getSetDefinitionNames();
        sort($this->setNames);

        return $this->setNames;
    }

    /**
     * @param string $type 'rule'|'set'
     * @param string $name
     *
     * @return null|string
     */
    private function getAlternative($type, $name)
    {
        $other = null;
        $alternatives = 'set' === $type ? $this->getSetNames() : array_keys($this->getFixers());

        foreach ($alternatives as $alternative) {
            $distance = levenshtein($name, $alternative);
            if (3 > $distance) {
                $other = $alternative;

                break;
            }
        }

        return $other;
    }

    /**
     * @param OutputInterface $output
     * @param string          $type   'rule'|'set'
     */
    private function describeList(OutputInterface $output, $type)
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $describe = array(
                'set' => $this->getSetNames(),
                'rules' => $this->getFixers(),
            );
        } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $describe = 'set' === $type ? array('set' => $this->getSetNames()) : array('rules' => $this->getFixers());
        } else {
            return;
        }

        /** @var string[] $items */
        foreach ($describe as $list => $items) {
            $output->writeln(sprintf('<comment>Defined %s:</comment>', $list));
            foreach ($items as $name => $item) {
                $output->writeln(sprintf('* <info>%s</info>', is_string($name) ? $name : $item));
            }
        }
    }
}
