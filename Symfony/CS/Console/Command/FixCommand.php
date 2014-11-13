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

use Symfony\CS\ConfigInterface;
use Symfony\CS\Config\Config;
use Symfony\CS\ErrorsManager;
use Symfony\CS\Fixer;
use Symfony\CS\FixerFileProcessedEvent;
use Symfony\CS\FixerInterface;
use Symfony\CS\FixersResolver;
use Symfony\CS\LintManager;
use Symfony\CS\StdinFileInfo;
use Symfony\CS\Utils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FixCommand extends Command
{
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
     * @var Config
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
        $this->errorsManager = new ErrorsManager();
        $this->stopwatch = new Stopwatch();

        $this->fixer = $fixer ?: new Fixer();
        $this->fixer->registerBuiltInFixers();
        $this->fixer->registerBuiltInConfigs();
        $this->fixer->setStopwatch($this->stopwatch);
        $this->fixer->setErrorsManager($this->errorsManager);

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
                    new InputOption('level', '', InputOption::VALUE_REQUIRED, 'The level of fixes (can be psr0, psr1, psr2, or symfony (formerly all))', null),
                    new InputOption('fixers', '', InputOption::VALUE_REQUIRED, 'A list of fixers to run'),
                    new InputOption('diff', '', InputOption::VALUE_NONE, 'Also produce diff for each file'),
                    new InputOption('format', '', InputOption::VALUE_REQUIRED, 'To output results in other formats', 'txt'),
                )
            )
            ->setDescription('Fixes a directory or a file')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command tries to fix as much coding standards
problems as possible on a given file or directory:

    <info>php %command.full_name% /path/to/dir</info>
    <info>php %command.full_name% /path/to/file</info>

The <comment>--level</comment> option limits the fixers to apply on the
project:

    <info>php %command.full_name% /path/to/project --level=psr0</info>
    <info>php %command.full_name% /path/to/project --level=psr1</info>
    <info>php %command.full_name% /path/to/project --level=psr2</info>
    <info>php %command.full_name% /path/to/project --level=symfony</info>

By default, all PSR-2 fixers and some additional ones are run. The "contrib
level" fixers cannot be enabled via this option; you should instead set them
manually by their name via the <comment>--fixers</comment> option.

The <comment>--fixers</comment> option lets you choose the exact fixers to
apply (the fixer names must be separated by a comma):

    <info>php %command.full_name% /path/to/dir --fixers=linefeed,short_tag,indentation</info>

You can also blacklist the fixers you don't want if this is more convenient,
using <comment>-name</comment>:

    <info>php %command.full_name% /path/to/dir --fixers=-short_tag,-indentation</info>

When using combination with exact and blacklist fixers, apply exact fixers along with above blacklisted result:

    <info>php php-cs-fixer.phar fix /path/to/dir --fixers=linefeed,-short_tag</info>

A combination of <comment>--dry-run</comment> and <comment>--diff</comment> will
display summary of proposed fixes, leaving your files unchanged.

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
configuration in a <comment>.php_cs</comment> file in the root directory of
your project. The file must return an instance of
`Symfony\CS\ConfigInterface`, which lets you configure the fixers, the level, the files,
and directories that need to be analyzed:

    <?php

    \$finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->fixers(array('indentation', 'elseif'))
        ->finder(\$finder)
    ;

    ?>

You may also use a blacklist for the Fixers instead of the above shown whitelist approach.
The following example shows how to use all ``symfony`` Fixers but the `psr0` fixer.
Note the additional <comment>-</comment> in front of the Fixer name.

    <?php

    \$finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->fixers(array('-psr0'))
        ->finder(\$finder)
    ;

    ?>

The ``symfony`` level is set by default, you can also change the default level:

    <?php

    return Symfony\CS\Config\Config::create()
        ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ;

    ?>

In combination with these config and command line options, you can choose various usage.

For example, default level is ``symfony``, but if you also don't want to use
the ``psr0`` fixer, you can specify the ``--fixers="-psr0"`` option.

But if you use the ``--fixers`` option with only exact fixers,
only those exact fixers are enabled whether or not level is set.

With the <comment>--config-file</comment> option you can specify the path to the
<comment>.php_cs</comment> file.

Caching
-------

You can enable caching by returning a custom config with caching enabled. This will
speed up further runs.

    <?php

    return Symfony\CS\Config\Config::create()
        ->setUsingCache(true)
    ;

    ?>
EOF
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        $stdin = false;

        if ('-' === $path) {
            $stdin = true;

            // Can't write to STDIN
            $input->setOption('dry-run', true);
        }

        if (null !== $path) {
            $filesystem = new Filesystem();
            if (!$filesystem->isAbsolutePath($path)) {
                $path = getcwd().DIRECTORY_SEPARATOR.$path;
            }
        }

        $configFile = $input->getOption('config-file');
        if (null === $configFile) {
            if (is_file($path) && $dirName = pathinfo($path, PATHINFO_DIRNAME)) {
                $configDir = $dirName;
            } elseif ($stdin || null === $path) {
                $configDir = getcwd();
                // path is directory
            } else {
                $configDir = $path;
            }
            $configFile = $configDir.DIRECTORY_SEPARATOR.'.php_cs';
        }

        if ($input->getOption('config')) {
            $config = null;
            foreach ($this->fixer->getConfigs() as $c) {
                if ($c->getName() === $input->getOption('config')) {
                    $config = $c;
                    break;
                }
            }

            if (null === $config) {
                throw new \InvalidArgumentException(sprintf('The configuration "%s" is not defined', $input->getOption('config')));
            }
        } elseif (file_exists($configFile)) {
            $config = include $configFile;
            // verify that the config has an instance of Config
            if (!$config instanceof Config) {
                throw new \UnexpectedValueException(sprintf('The config file "%s" does not return an instance of Symfony\CS\Config\Config', $configFile));
            } else {
                $output->writeln(sprintf('Loaded config from "%s"', $configFile));
            }
        } else {
            $config = $this->defaultConfig;
        }

        if ($config->usingLinter()) {
            $this->fixer->setLintManager(new LintManager());
        }

        if (is_file($path)) {
            $config->finder(new \ArrayIterator(array(new \SplFileInfo($path))));
        } elseif ($stdin) {
            $config->finder(new \ArrayIterator(array(new StdinFileInfo())));
        } elseif (null !== $path) {
            $config->setDir($path);
        }

        // register custom fixers from config
        $this->fixer->registerCustomFixers($config->getCustomFixers());

        $resolver = new FixersResolver($this->fixer->getFixers());
        $resolver
            ->setConfig($config)
            ->setOption('level', $input->getOption('level'))
            ->setOption('fixers', $input->getOption('fixers'))
            ->resolve();

        $config->fixers($resolver->getFixers());

        $verbosity = $output->getVerbosity();
        $listenForFixerFileProcessedEvent = OutputInterface::VERBOSITY_VERY_VERBOSE <= $verbosity;

        if ($listenForFixerFileProcessedEvent) {
            $fileProcessedEventListener = function (FixerFileProcessedEvent $event) use ($output) {
                $output->write($event->getStatusAsString());
            };
        }

        if ($listenForFixerFileProcessedEvent) {
            $this->fixer->setEventDispatcher($this->eventDispatcher);
            $this->eventDispatcher->addListener(FixerFileProcessedEvent::NAME, $fileProcessedEventListener);
        }

        $this->stopwatch->start('fixFiles');
        $changed = $this->fixer->fix($config, $input->getOption('dry-run'), $input->getOption('diff'));
        $this->stopwatch->stop('fixFiles');

        if ($listenForFixerFileProcessedEvent) {
            $this->fixer->setEventDispatcher(null);
            $this->eventDispatcher->removeListener(FixerFileProcessedEvent::NAME, $fileProcessedEventListener);
            $output->writeln('');

            $legend = array();
            foreach (FixerFileProcessedEvent::getStatusMap() as $status) {
                if ($status['symbol'] && $status['description']) {
                    $legend[] = $status['symbol'].'-'.$status['description'];
                }
            }

            $output->writeln('Legend: '.implode(', ', array_unique($legend)));
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
                $dom      = new \DOMDocument('1.0', 'UTF-8');
                $filesXML = $dom->createElement('files');
                $dom->appendChild($filesXML);

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
                $dom->appendChild($timeXML);
                $dom->appendChild($memoryXML);

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

        if (!$this->errorsManager->isEmpty()) {
            $output->writeLn('');
            $output->writeLn('Files that were not fixed due to internal error:');

            foreach ($this->errorsManager->getErrors() as $i => $error) {
                $output->writeLn(sprintf('%4d) %s', $i + 1, $error['filepath']));
            }
        }

        return empty($changed) ? 0 : 1;
    }

    protected function getFixersHelp()
    {
        $help = '';
        $maxName = 0;
        $fixers = $this->fixer->getFixers();

        // sort fixers by level and name
        usort(
            $fixers,
            function (FixerInterface $a, FixerInterface $b) {
                $cmp = Utils::cmpInt($a->getLevel(), $b->getLevel());

                if (0 !== $cmp) {
                    return $cmp;
                }

                return strcmp($a->getName(), $b->getName());
            }
        );

        foreach ($fixers as $fixer) {
            if (strlen($fixer->getName()) > $maxName) {
                $maxName = strlen($fixer->getName());
            }
        }

        $count = count($fixers) - 1;
        foreach ($fixers as $i => $fixer) {
            $chunks = explode("\n", wordwrap(sprintf('[%s] %s', $this->fixer->getLevelAsString($fixer), $fixer->getDescription()), 72 - $maxName, "\n"));
            $help .= sprintf(" * <comment>%s</comment>%s %s\n", $fixer->getName(), str_repeat(' ', $maxName - strlen($fixer->getName())), array_shift($chunks));
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
