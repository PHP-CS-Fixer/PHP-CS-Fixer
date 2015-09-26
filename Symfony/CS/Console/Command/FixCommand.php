<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\CS\Config\Config;
use Symfony\CS\ConfigInterface;
use Symfony\CS\Console\ConfigurationResolver;
use Symfony\CS\Console\Output\ProcessOutput;
use Symfony\CS\Error\Error;
use Symfony\CS\Error\ErrorsManager;
use Symfony\CS\Fixer;
use Symfony\CS\FixerFactory;
use Symfony\CS\FixerInterface;
use Symfony\CS\Linter\Linter;
use Symfony\CS\Linter\UnavailableLinterException;
use Symfony\CS\RuleSet;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class FixCommand extends Command
{
    const EXIT_STATUS_FLAG_HAS_INVALID_FILES = 4;
    const EXIT_STATUS_FLAG_HAS_CHANGED_FILES = 8;

    /**
     * EventDispatcher instance.
     *
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * ErrorsManager instance.
     *
     * @var ErrorsManager
     */
    protected $errorsManager;

    /**
     * Stopwatch instance.
     *
     * @var Stopwatch
     */
    protected $stopwatch;

    /**
     * Fixer instance.
     *
     * @var Fixer
     */
    protected $fixer;

    /**
     * Config instance.
     *
     * @var ConfigInterface
     */
    protected $defaultConfig;

    /**
     * @param Fixer|null           $fixer
     * @param ConfigInterface|null $config
     */
    public function __construct(Fixer $fixer = null, ConfigInterface $config = null)
    {
        $this->defaultConfig = $config ?: new Config();
        $this->eventDispatcher = new EventDispatcher();

        $this->fixer = $fixer ?: new Fixer();
        $this->fixer->registerBuiltInConfigs();

        $this->errorsManager = $this->fixer->getErrorsManager();
        $this->stopwatch = $this->fixer->getStopwatch();

        parent::__construct();
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fix')
            ->setDefinition(
                array(
                    new InputArgument('path', InputArgument::OPTIONAL, 'The path', null),
                    new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The configuration name', null),
                    new InputOption('config-file', '', InputOption::VALUE_OPTIONAL, 'The path to a .php_cs file ', null),
                    new InputOption('dry-run', '', InputOption::VALUE_NONE, 'Only shows which files would have been modified'),
                    new InputOption('rules', '', InputOption::VALUE_REQUIRED, 'The rules', null),
                    new InputOption('using-cache', '', InputOption::VALUE_REQUIRED, 'Does cache should be used (can be yes or no)', null),
                    new InputOption('cache-file', '', InputOption::VALUE_REQUIRED, 'The path to the cache file'),
                    new InputOption('diff', '', InputOption::VALUE_NONE, 'Also produce diff for each file'),
                    new InputOption('format', '', InputOption::VALUE_REQUIRED, 'To output results in other formats', 'txt'),
                )
            )
            ->setDescription('Fixes a directory or a file')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command tries to fix as much coding standards
problems as possible on a given file or files in a given directory and its subdirectories:

    <info>php %command.full_name% /path/to/dir</info>
    <info>php %command.full_name% /path/to/file</info>

The <comment>--format</comment> option can be used to set the output format of the results; ``txt`` (default one), ``xml`` or ``json``.

The <comment>--verbose</comment> option will show the applied fixers. When using the ``txt`` format it will also displays progress notifications.

The <comment>--rules</comment> option limits the rules to apply on the
project:

    <info>php %command.full_name% /path/to/project --rules=@PSR2</info>

By default, all PSR fixers are run.

The <comment>--rules</comment> option lets you choose the exact fixers to
apply (the fixer names must be separated by a comma):

    <info>php %command.full_name% /path/to/dir --rules=linefeed,short_tag,indentation</info>

You can also blacklist the fixers you don't want by placing a dash in front of the fixer name, if this is more convenient,
using <comment>-name_of_fixer</comment>:

    <info>php %command.full_name% /path/to/dir --rules=-short_tag,-indentation</info>

When using combinations of exact and blacklist fixers, applying exact fixers along with above blacklisted results:

    <info>php %command.full_name% /path/to/project --rules=@Symfony,-@PSR1,-return,strict</info>

A combination of <comment>--dry-run</comment> and <comment>--diff</comment> will
display a summary of proposed fixes, leaving your files unchanged.

The command can also read from standard input, in which case it won't
automatically fix anything:

    <info>cat foo.php | php %command.full_name% --diff -</info>

Choose from the list of available fixers:

{$this->getFixersHelp()}

The <comment>--config</comment> option customizes the files to analyse, based
on some well-known directory structures:

    <comment># For the Symfony 2.3+ branch</comment>
    <info>php %command.full_name% /path/to/sf23 --config=sf23</info>

Choose from the list of available configurations:

{$this->getConfigsHelp()}
The <comment>--dry-run</comment> option displays the files that need to be
fixed but without actually modifying them:

    <info>php %command.full_name% /path/to/code --dry-run</info>

Instead of using command line options to customize the fixer, you can save the
project configuration in a <comment>.php_cs.dist</comment> file in the root directory
of your project. The file must return an instance of ``Symfony\CS\ConfigInterface``,
which lets you configure the rules, the files and directories that
need to be analyzed. You may also create <comment>.php_cs</comment> file, which is
the local configuration that will be used instead of the project configuration. It
is a good practice to add that file into your <comment>.gitignore</comment> file.
With the <comment>--config-file</comment> option you can specify the path to the
<comment>.php_cs</comment> file.

The example below will add two fixers to the default list of PSR2 set fixers:

    <?php

    \$finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->setRules(array(
            '@PSR2' => true,
            'strict_param' => true,
            'short_array_syntax' => true,
        ))
        ->finder(\$finder)
    ;

    ?>

You may also use a blacklist for the Fixers instead of the above shown whitelist approach.
The following example shows how to use all ``Symfony`` Fixers but the ``short_tag`` Fixer.

    <?php

    \$finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->setRules(array(
            '@Symfony' => true,
            'short_tag' => false,
        ))
        ->finder(\$finder)
    ;

    ?>

By using ``--using-cache`` option with yes or no you can set if the caching
mechanism should be used.

Caching
-------

The caching mechanism is enabled by default. This will speed up further runs by
fixing only files that were modified since the last run. The tool will fix all
files if the tool version has changed or the list of fixers has changed.
Cache is supported only for tool downloaded as phar file or installed via
composer.

Cache can be disabled via ``--using-cache`` option or config file:

    <?php

    return Symfony\CS\Config\Config::create()
        ->setUsingCache(false)
    ;

    ?>

Cache file can be specified via ``--cache-file`` option or config file:

    <?php

    return Symfony\CS\Config\Config::create()
        ->setCacheFile(__DIR__.'/.php_cs.cache')
    ;

    ?>

Using PHP CS Fixer on Travis
----------------------------

Require ``fabpot/php-cs-fixer`` as a `dev`` dependency:

    $ ./composer.phar require --dev fabpot/php-cs-fixer

Create a build file to run ``php-cs-fixer`` on Travis. It's advisable to create a dedicated directory
for PHP CS Fixer cache files and have Travis cache it between builds.

    <?yml

    language: php
    php:
        - 5.5
    sudo: false
    cache:
        directories:
            - "\$HOME/.composer/cache"
            - "\$HOME/.php-cs-fixer"
    before_script:
        - mkdir -p "\$HOME/.php-cs-fixer"
    script:
        - vendor/bin/php-cs-fixer fix --cache-file "\$HOME/.php-cs-fixer/.php_cs.cache" --dry-run --diff --verbose

    ?>

Note: This will only trigger a build if you have a subscription for Travis
or are using their free open source plan.
EOF
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbosity = $output->getVerbosity();
        $resolver = new ConfigurationResolver();
        $resolver
            ->setCwd(getcwd())
            ->setDefaultConfig($this->defaultConfig)
            ->setFixer($this->fixer)
            ->setOptions(array(
                'config' => $input->getOption('config'),
                'config-file' => $input->getOption('config-file'),
                'dry-run' => $input->getOption('dry-run'),
                'rules' => $input->getOption('rules'),
                'path' => $input->getArgument('path'),
                'progress' => (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) && 'txt' === $input->getOption('format'),
                'using-cache' => $input->getOption('using-cache'),
                'cache-file' => $input->getOption('cache-file'),
            ))
            ->resolve()
        ;

        $config = $resolver->getConfig();
        $configFile = $resolver->getConfigFile();

        if ($configFile && 'txt' === $input->getOption('format')) {
            $output->writeln(sprintf('Loaded config from "%s"', $configFile));
        }

        if ($config->usingLinter()) {
            try {
                $this->fixer->setLinter(new Linter($config->getPhpExecutable()));
            } catch (UnavailableLinterException $e) {
                if ($configFile && 'txt' === $input->getOption('format')) {
                    $output->writeln('Unable to use linter, can not find PHP executable');
                }
            }
        }

        $showProgress = $resolver->getProgress();

        if ($showProgress) {
            $this->fixer->setEventDispatcher($this->eventDispatcher);
            $progressOutput = new ProcessOutput($this->eventDispatcher);
        }

        $this->stopwatch->start('fixFiles');
        $changed = $this->fixer->fix($config, $resolver->isDryRun(), $input->getOption('diff'));
        $this->stopwatch->stop('fixFiles');

        if ($showProgress) {
            $progressOutput->printLegend();
            $this->fixer->setEventDispatcher(null);
        }

        $i = 1;

        switch ($input->getOption('format')) {
            case 'txt':
                foreach ($changed as $file => $fixResult) {
                    $output->write(sprintf('%4d) %s', $i++, $file));

                    if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                        $output->write(sprintf(' (<comment>%s</comment>)', implode(', ', $fixResult['appliedFixers'])));
                    }

                    if ($input->getOption('diff')) {
                        $output->writeln('');
                        $output->writeln('<comment>      ---------- begin diff ----------</comment>');
                        $output->writeln($fixResult['diff']);
                        $output->writeln('<comment>      ---------- end diff ----------</comment>');
                    }

                    $output->writeln('');
                }

                if (OutputInterface::VERBOSITY_DEBUG <= $verbosity) {
                    $output->writeln('Fixing time per file:');

                    foreach ($this->stopwatch->getSectionEvents('fixFile') as $file => $event) {
                        if ('__section__' === $file) {
                            continue;
                        }

                        $output->writeln(sprintf('[%.3f s] %s', $event->getDuration() / 1000, $file));
                    }

                    $output->writeln('');
                }

                $fixEvent = $this->stopwatch->getEvent('fixFiles');
                $output->writeln(sprintf('Fixed all files in %.3f seconds, %.3f MB memory used', $fixEvent->getDuration() / 1000, $fixEvent->getMemory() / 1024 / 1024));
                break;
            case 'xml':
                $dom = new \DOMDocument('1.0', 'UTF-8');
                // new nodes should be added to this or existing children
                $root = $dom->createElement('report');
                $dom->appendChild($root);

                $filesXML = $dom->createElement('files');
                $root->appendChild($filesXML);

                foreach ($changed as $file => $fixResult) {
                    $fileXML = $dom->createElement('file');
                    $fileXML->setAttribute('id', $i++);
                    $fileXML->setAttribute('name', $file);
                    $filesXML->appendChild($fileXML);

                    if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                        $appliedFixersXML = $dom->createElement('applied_fixers');
                        $fileXML->appendChild($appliedFixersXML);

                        foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                            $appliedFixerXML = $dom->createElement('applied_fixer');
                            $appliedFixerXML->setAttribute('name', $appliedFixer);
                            $appliedFixersXML->appendChild($appliedFixerXML);
                        }
                    }

                    if ($input->getOption('diff')) {
                        $diffXML = $dom->createElement('diff');
                        $diffXML->appendChild($dom->createCDATASection($fixResult['diff']));
                        $fileXML->appendChild($diffXML);
                    }
                }

                $fixEvent = $this->stopwatch->getEvent('fixFiles');

                $timeXML = $dom->createElement('time');
                $memoryXML = $dom->createElement('memory');
                $root->appendChild($timeXML);
                $root->appendChild($memoryXML);

                $memoryXML->setAttribute('value', round($fixEvent->getMemory() / 1024 / 1024, 3));
                $memoryXML->setAttribute('unit', 'MB');

                $timeXML->setAttribute('unit', 's');
                $timeTotalXML = $dom->createElement('total');
                $timeTotalXML->setAttribute('value', round($fixEvent->getDuration() / 1000, 3));
                $timeXML->appendChild($timeTotalXML);

                if (OutputInterface::VERBOSITY_DEBUG <= $verbosity) {
                    $timeFilesXML = $dom->createElement('files');
                    $timeXML->appendChild($timeFilesXML);
                    $eventCounter = 1;

                    foreach ($this->stopwatch->getSectionEvents('fixFile') as $file => $event) {
                        if ('__section__' === $file) {
                            continue;
                        }

                        $timeFileXML = $dom->createElement('file');
                        $timeFilesXML->appendChild($timeFileXML);
                        $timeFileXML->setAttribute('id', $eventCounter++);
                        $timeFileXML->setAttribute('name', $file);
                        $timeFileXML->setAttribute('value', round($event->getDuration() / 1000, 3));
                    }
                }

                $dom->formatOutput = true;
                $output->write($dom->saveXML());
                break;
            case 'json':
                $jFiles = array();

                foreach ($changed as $file => $fixResult) {
                    $jfile = array('name' => $file);

                    if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                        $jfile['appliedFixers'] = $fixResult['appliedFixers'];
                    }

                    if ($input->getOption('diff')) {
                        $jfile['diff'] = $fixResult['diff'];
                    }

                    $jFiles[] = $jfile;
                }

                $fixEvent = $this->stopwatch->getEvent('fixFiles');

                $json = array(
                    'files' => $jFiles,
                    'memory' => round($fixEvent->getMemory() / 1024 / 1024, 3),
                    'time' => array(
                        'total' => round($fixEvent->getDuration() / 1000, 3),
                    ),
                );

                if (OutputInterface::VERBOSITY_DEBUG <= $verbosity) {
                    $jFileTime = array();

                    foreach ($this->stopwatch->getSectionEvents('fixFile') as $file => $event) {
                        if ('__section__' === $file) {
                            continue;
                        }

                        $jFileTime[$file] = round($event->getDuration() / 1000, 3);
                    }

                    $json['time']['files'] = $jFileTime;
                }

                $output->write(json_encode($json));
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The format "%s" is not defined.', $input->getOption('format')));
        }

        $invalidErrors = $this->errorsManager->getInvalidErrors();
        if (!empty($invalidErrors)) {
            $this->listErrors($output, 'linting before fixing', $invalidErrors);
        }

        $exceptionErrors = $this->errorsManager->getExceptionErrors();
        if (!empty($exceptionErrors)) {
            $this->listErrors($output, 'fixing', $exceptionErrors);
        }

        $lintErrors = $this->errorsManager->getLintErrors();
        if (!empty($lintErrors)) {
            $this->listErrors($output, 'linting after fixing', $lintErrors);
        }

        $exitStatus = 0;

        if ($resolver->isDryRun()) {
            if (!empty($invalidErrors)) {
                $exitStatus |= self::EXIT_STATUS_FLAG_HAS_INVALID_FILES;
            }

            if (!empty($changed)) {
                $exitStatus |= self::EXIT_STATUS_FLAG_HAS_CHANGED_FILES;
            }
        }

        return $exitStatus;
    }

    /**
     * @param OutputInterface $output
     * @param string          $process
     * @param Error[]         $errors
     */
    private function listErrors(OutputInterface $output, $process, array $errors)
    {
        $output->writeLn('');
        $output->writeLn(sprintf(
            'Files that were not fixed due to errors reported during %s:',
             $process
        ));

        foreach ($errors as $i => $error) {
            $output->writeLn(sprintf('%4d) %s', $i + 1, $error->getFilePath()));
        }
    }

    protected function getFixersHelp()
    {
        $help = '';
        $maxName = 0;
        $fixerFactory = new FixerFactory();
        $fixers = $fixerFactory->registerBuiltInFixers()->getFixers();

        // sort fixers by name
        usort(
            $fixers,
            function (FixerInterface $a, FixerInterface $b) {
                return strcmp($a->getName(), $b->getName());
            }
        );

        foreach ($fixers as $fixer) {
            if (strlen($fixer->getName()) > $maxName) {
                $maxName = strlen($fixer->getName());
            }
        }

        $ruleSets = array();
        foreach (RuleSet::create()->getSetDefinitionNames() as $setName) {
            $ruleSets[$setName] = new RuleSet(array($setName => true));
        }

        $getSetsWithRule = function ($rule) use ($ruleSets) {
            $sets = array();

            foreach ($ruleSets as $setName => $ruleSet) {
                if ($ruleSet->hasRule($rule)) {
                    $sets[] = $setName;
                }
            }

            return $sets;
        };

        $count = count($fixers) - 1;
        foreach ($fixers as $i => $fixer) {
            $sets = $getSetsWithRule($fixer->getName());

            if (!empty($sets)) {
                $chunks = explode("\n", wordwrap(sprintf("[%s]\n%s", implode(', ', $sets), $fixer->getDescription()), 72 - $maxName, "\n"));
                $help .= sprintf(" * <comment>%s</comment>%s %s\n", $fixer->getName(), str_repeat(' ', $maxName - strlen($fixer->getName())), array_shift($chunks));
            } else {
                $chunks = explode("\n", wordwrap(sprintf("\n%s", $fixer->getDescription()), 72 - $maxName, "\n"));
                $help .= sprintf(" * <comment>%s</comment>%s\n", $fixer->getName(), array_shift($chunks));
            }

            while ($c = array_shift($chunks)) {
                $help .= str_repeat(' ', $maxName + 4).$c."\n";
            }

            if ($count !== $i) {
                $help .= "\n";
            }
        }

        return $help;
    }

    protected function getConfigsHelp()
    {
        $help = '';
        $maxName = 0;

        $configs = $this->fixer->getConfigs();

        usort(
            $configs,
            function (ConfigInterface $a, ConfigInterface $b) {
                return strcmp($a->getName(), $b->getName());
            }
        );

        foreach ($configs as $config) {
            if (strlen($config->getName()) > $maxName) {
                $maxName = strlen($config->getName());
            }
        }

        $count = count($this->fixer->getConfigs()) - 1;
        foreach ($configs as $i => $config) {
            $chunks = explode("\n", wordwrap($config->getDescription(), 72 - $maxName, "\n"));
            $help .= sprintf(" * <comment>%s</comment>%s %s\n", $config->getName(), str_repeat(' ', $maxName - strlen($config->getName())), array_shift($chunks));
            while ($c = array_shift($chunks)) {
                $help .= str_repeat(' ', $maxName + 4).$c."\n";
            }

            if ($count !== $i) {
                $help .= "\n";
            }
        }

        return $help;
    }
}
