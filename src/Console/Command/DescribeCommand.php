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

use PhpCsFixer\ConfigurationException\UnallowedFixerConfigurationException;
use PhpCsFixer\Differ\DiffConsoleFormatter;
use PhpCsFixer\Differ\SebastianBergmannDiffer;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\FixerInterface;
use PhpCsFixer\RuleSet;
use PhpCsFixer\ShortFixerDefinition;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class DescribeCommand extends Command
{
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

        if ('@' === substr($name, 0, 1)) {
            $this->describeSet($input, $output, $name);
        } else {
            $this->describeRule($input, $output, $name);
        }
    }

    private function describeRule(InputInterface $input, OutputInterface $output, $name)
    {
        $fixerFactory = new FixerFactory();
        $fixers = array();

        foreach ($fixerFactory->registerBuiltInFixers()->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        if (!isset($fixers[$name])) {
            throw new \InvalidArgumentException(sprintf('Rule "%s" does not exist.', $name));
        }

        $fixer = $fixers[$name];
        $definition = $fixer->getDefinition();

        $output->writeln(sprintf('<info>Description of %s rule.</info>', $name));
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

        if ($this->isFixerConfigurable($fixer)) {
            $output->writeln('<comment>Fixer is configurable.</comment>');

            if ($definition->getConfigurationDescription()) {
                $output->writeln($definition->getConfigurationDescription());
            }

            if ($definition->getDefaultConfiguration()) {
                $output->writeln(sprintf('Default configuration: <comment>%s</comment>.', $this->arrayToText($definition->getDefaultConfiguration())));
            }

            $output->writeln('');
        }

        if ($definition->getCodeSamples()) {
            $output->writeln('Fixing examples:');

            $differ = new SebastianBergmannDiffer();
            $diffFormatter = new DiffConsoleFormatter($output->isDecorated(), sprintf(
                '<comment>   ---------- begin diff ----------</comment>%s%%s%s<comment>   ----------- end diff -----------</comment>',
                PHP_EOL,
                PHP_EOL
            ));

            foreach ($definition->getCodeSamples() as $index => $codeSample) {
                $old = $codeSample[0];
                $tokens = Tokens::fromCode($old);
                $fixer->configure($codeSample[1]);
                $fixer->fix(new StdinFileInfo(), $tokens);
                $new = $tokens->generateCode();
                $diff = $differ->diff($old, $new);

                if (null === $codeSample[1]) {
                    $output->writeln(sprintf(' * Example #%d.', $index + 1));
                } else {
                    $output->writeln(sprintf(' * Example #%d. Fixing with configuration: <comment>%s</comment>.', $index + 1, $this->arrayToText($codeSample[1])));
                }
                $output->writeln($diffFormatter->format($diff, '   %s'));
                $output->writeln('');
            }
        }

        if ($definition instanceof ShortFixerDefinition) {
            $output->writeln(sprintf('<question>This rule is not yet described, do you want to help us and describe it?</question>'));
            $output->writeln('Contribute at <comment>https://github.com/FriendsOfPHP/PHP-CS-Fixer</comment>');
            $output->writeln('');
        }
    }

    private function describeSet(InputInterface $input, OutputInterface $output, $name)
    {
        $ruleSet = new RuleSet(array($name => true));
        $rules = $ruleSet->getRules();
        ksort($rules);

        $fixerFactory = new FixerFactory();
        $fixers = array();

        foreach ($fixerFactory->registerBuiltInFixers()->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $output->writeln(sprintf('<info>Description of %s set.</info>', $name));
        $output->writeln('');

        $help = '';

        $count = count($rules) - 1;
        foreach ($rules as $rule => $config) {
            $help .= sprintf(
                " * <info>%s</info>%s\n   | %s\n%s\n",
                $rule,
                $fixers[$rule]->isRisky() ? ' <error>[risky]</error>' : '',
                $fixers[$rule]->getDescription(),
                true !== $config ? sprintf("   <comment>| Configuration: %s</comment>\n", $this->arrayToText($config)) : ''
            );
        }

        $output->write($help);
    }

    /**
     * @param FixerInterface $fixer
     *
     * @return bool
     */
    private function isFixerConfigurable(FixerInterface $fixer)
    {
        try {
            $fixer->configure(array());

            return true;
        } catch (UnallowedFixerConfigurationException $e) {
            return false;
        } catch (\Exception $e) {
            return true;
        }
    }

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
}
