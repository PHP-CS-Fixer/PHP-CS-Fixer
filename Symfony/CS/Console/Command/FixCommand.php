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
use Symfony\CS\Console\Output\JsonOutput;
use Symfony\CS\Console\Output\ProcessOutput;
use Symfony\CS\Console\Output\TxtOutput;
use Symfony\CS\Console\Output\XmlOutput;
use Symfony\CS\ErrorsManager;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Linter\Linter;
use Symfony\CS\Linter\UnavailableLinterException;
use Symfony\CS\Utils;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
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

        $this->fixer = $fixer ?: new Fixer();
        $this->fixer->registerBuiltInFixers();
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
                    new InputOption('level', '', InputOption::VALUE_REQUIRED, 'The level of fixes (can be psr0, psr1, psr2, or symfony (formerly all))', null),
                    new InputOption('using-cache', '', InputOption::VALUE_REQUIRED, 'Does cache should be used (can be yes or no)', null),
                    new InputOption('cache-file', '', InputOption::VALUE_REQUIRED, 'The path to the cache file'),
                    new InputOption('fixers', '', InputOption::VALUE_REQUIRED, 'A list of fixers to run'),
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

The <comment>--level</comment> option limits the fixers to apply on the
project:

    <info>php %command.full_name% /path/to/project --level=psr0</info>
    <info>php %command.full_name% /path/to/project --level=psr1</info>
    <info>php %command.full_name% /path/to/project --level=psr2</info>
    <info>php %command.full_name% /path/to/project --level=symfony</info>

By default, all PSR fixers are run. The "contrib
level" fixers cannot be enabled via this option; you should instead set them
manually by their name via the <comment>--fixers</comment> option.

The <comment>--fixers</comment> option lets you choose the exact fixers to
apply (the fixer names must be separated by a comma):

    <info>php %command.full_name% /path/to/dir --fixers=linefeed,short_tag,indentation</info>

You can also blacklist the fixers you don't want by placing a dash in front of the fixer name, if this is more convenient,
using <comment>-name_of_fixer</comment>:

    <info>php %command.full_name% /path/to/dir --fixers=-short_tag,-indentation</info>

When using combinations of exact and blacklist fixers, applying exact fixers along with above blacklisted results:

    <info>php php-cs-fixer.phar fix /path/to/dir --fixers=linefeed,-short_tag</info>

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
which lets you configure the fixers, the level, the files and directories that
need to be analyzed. You may also create <comment>.php_cs</comment> file, which is
the local configuration that will be used instead of the project configuration. It
is a good practice to add that file into your <comment>.gitignore</comment> file.
With the <comment>--config-file</comment> option you can specify the path to the
<comment>.php_cs</comment> file.

The example below will add two contrib fixers to the default list of PSR2-level fixers:

    <?php

    \$finder = Symfony\CS\Finder\DefaultFinder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->fixers(array('strict_param', 'short_array_syntax'))
        ->finder(\$finder)
    ;

    ?>

If you want complete control over which fixers you use, you can use the empty level and
then specify all fixers to be used:

    <?php

    \$finder = Symfony\CS\Finder\DefaultFinder::create()
        ->in(__DIR__)
    ;

    return Symfony\CS\Config\Config::create()
        ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
        ->fixers(array('trailing_spaces', 'encoding'))
        ->finder(\$finder)
    ;

    ?>

You may also use a blacklist for the Fixers instead of the above shown whitelist approach.
The following example shows how to use all ``symfony`` Fixers but the ``psr0`` fixer.
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

The ``psr2`` level is set by default, you can also change the default level:

    <?php

    return Symfony\CS\Config\Config::create()
        ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ;

    ?>

In combination with these config and command line options, you can choose various usage.

For example, the default level is ``psr2``, but if you don't want to use
the ``psr0`` fixer, you can specify the ``--fixers="-psr0"`` option.

But if you use the ``--fixers`` option with only exact fixers,
only those exact fixers are enabled whether or not level is set.

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
                'level' => $input->getOption('level'),
                'fixers' => $input->getOption('fixers'),
                'path' => $input->getArgument('path'),
                'progress'  => (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) && 'txt' === $input->getOption('format'),
                'using-cache' => $input->getOption('using-cache'),
                'cache-file' => $input->getOption('cache-file'),
            ))
            ->resolve()
        ;

        $config = $resolver->getConfig();
        $configFile = $resolver->getConfigFile();

        switch ($input->getOption('format')) {
            case 'txt':
                $output = new TxtOutput($output, $config, $resolver->isDryRun(), $input->getOption('diff'));
                break;
            case 'xml':
                $output = new XmlOutput($output, $config, $resolver->isDryRun(), $input->getOption('diff'));
                break;
            case 'json':
                $output = new JsonOutput($output, $config, $resolver->isDryRun(), $input->getOption('diff'));
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The format "%s" is not defined.', $input->getOption('format')));
        }

        if ($configFile) {
            $output->writeInfo(sprintf('Loaded config from "%s"', $configFile));
        }

        // register custom fixers from config
        $this->fixer->registerCustomFixers($config->getCustomFixers());
        if ($config->usingLinter()) {
            try {
                $this->fixer->setLinter(new Linter($config->getPhpExecutable()));
            } catch (UnavailableLinterException $e) {
                $this->errorsManager->report(ErrorsManager::ERROR_TYPE_LINT, '', 'Unable to use linter, can not find PHP executable');
            }
        }

        $showProgress = $resolver->getProgress();

        if ($showProgress) {
            $this->fixer->setEventDispatcher($this->eventDispatcher);
            $process = new ProcessOutput($this->eventDispatcher);
        }

        $this->stopwatch->start('fixFiles');
        $changed = $this->fixer->fix($config, $resolver->isDryRun(), $input->getOption('diff'));
        $this->stopwatch->stop('fixFiles');

        if ($showProgress) {
            $process->printLegend();
            $this->fixer->setEventDispatcher(null);
        }

        $output->writeChanges($changed);

        if (!$this->errorsManager->isEmpty()) {
            $output->writeErrors($this->errorsManager->getErrors());
        }

        $output->writeTimings($this->stopwatch);

        return !$resolver->isDryRun() || empty($changed) ? 0 : 3;
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
            $chunks = explode("\n", wordwrap(sprintf("[%s]\n%s", $this->fixer->getLevelAsString($fixer), $fixer->getDescription()), 72 - $maxName, "\n"));
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
