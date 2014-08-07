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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Config\Config;
use Symfony\CS\ConfigInterface;
use Symfony\CS\StdinFileInfo;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FixCommand extends Command
{
    protected $fixer;
    protected $defaultConfig;

    /**
     * @param Fixer           $fixer
     * @param ConfigInterface $config
     */
    public function __construct(Fixer $fixer = null, ConfigInterface $config = null)
    {
        $this->fixer = $fixer ?: new Fixer();
        $this->fixer->registerBuiltInFixers();
        $this->fixer->registerBuiltInConfigs();
        $this->defaultConfig = $config ?: new Config();

        parent::__construct();
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fix')
            ->setDefinition(array(
                new InputArgument('path', InputArgument::OPTIONAL, 'The path', null),
                new InputOption('config', '', InputOption::VALUE_REQUIRED, 'The configuration name', null),
                new InputOption('config-file', '', InputOption::VALUE_OPTIONAL, 'The path to a .php_cs file ', null),
                new InputOption('dry-run', '', InputOption::VALUE_NONE, 'Only shows which files would have been modified'),
                new InputOption('level', '', InputOption::VALUE_REQUIRED, 'The level of fixes (can be psr0, psr1, psr2, or all)', null),
                new InputOption('fixers', '', InputOption::VALUE_REQUIRED, 'A list of fixers to run'),
                new InputOption('diff', '', InputOption::VALUE_NONE, 'Also produce diff for each file'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'To output results in other formats', 'txt')
            ))
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
    <info>php %command.full_name% /path/to/project --level=all</info>

By default, all PSR-2 fixers and some additional ones are run. The "contrib
level" fixers cannot be enabled via this option; you should instead set them
manually by their name via the <comment>--fixers</comment> option.

The <comment>--fixers</comment> option lets you choose the exact fixers to
apply (the fixer names must be separated by a comma):

    <info>php %command.full_name% /path/to/dir --fixers=linefeed,short_tag,indentation</info>

You can also blacklist the fixers you don't want if this is more convenient,
using <comment>-name</comment>:

    <info>php %command.full_name% /path/to/dir --fixers=-short_tag,-indentation</info>

A combination of <comment>--dry-run</comment>, <comment>--verbose</comment> and <comment>--diff</comment> will
display summary of proposed fixes, leaving your files unchanged.

The command can also read from standard input, in which case it won't
automatically fix anything:

    <info>cat foo.php | php %command.full_name% -v --diff -</info>

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
`Symfony\CS\ConfigInterface`, which lets you configure the fixers, the files,
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

You may also use a blacklist for the Fixers instead of the above shown whitelist approach.
The following example shows how to use all Fixers but the `psr0` fixer.
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

With the <comment>--config-file</comment> option you can specify the path to the
<comment>.php_cs</comment> file.
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
                $path = getcwd() . DIRECTORY_SEPARATOR . $path;
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
            $configFile = $configDir . DIRECTORY_SEPARATOR . '.php_cs';
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

        if (is_file($path)) {
            $config->finder(new \ArrayIterator(array(new \SplFileInfo($path))));
        } elseif ($stdin) {
            $config->finder(new \ArrayIterator(array(new StdinFileInfo())));
        } elseif (null !== $path) {
            $config->setDir($path);
        }

        // register custom fixers from config
        $this->fixer->registerCustomFixers($config->getCustomFixers());

        $allFixers = $this->fixer->getFixers();

        switch ($input->getOption('level')) {
            case 'psr0':
                $level = FixerInterface::PSR0_LEVEL;
                break;
            case 'psr1':
                $level = FixerInterface::PSR1_LEVEL;
                break;
            case 'psr2':
                $level = FixerInterface::PSR2_LEVEL;
                break;
            case 'all':
                $level = FixerInterface::ALL_LEVEL;
                break;
            case null:
                $fixerOption = $input->getOption('fixers');
                if (empty($fixerOption) || preg_match('{(^|,)-}', $fixerOption)) {
                    $level = $config->getFixers();
                } else {
                    $level = null;
                }
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The level "%s" is not defined.', $input->getOption('level')));
        }

        // select base fixers for the given level
        $fixers = array();
        if (is_array($level)) {
            foreach ($allFixers as $fixer) {
                if (in_array($fixer->getName(), $level, true) || in_array($fixer, $level, true)) {
                    $fixers[] = $fixer;
                }
            }
        } else {
            foreach ($allFixers as $fixer) {
                if ($fixer->getLevel() === ($fixer->getLevel() & $level)) {
                    $fixers[] = $fixer;
                }
            }
        }

        // remove/add fixers based on the fixers option
        if (preg_match('{(^|,)-}', $input->getOption('fixers'))) {
            foreach ($fixers as $key => $fixer) {
                if (preg_match('{(^|,)-'.preg_quote($fixer->getName()).'}', $input->getOption('fixers'))) {
                    unset($fixers[$key]);
                }
            }
        } elseif ($input->getOption('fixers')) {
            $names = array_map('trim', explode(',', $input->getOption('fixers')));

            foreach ($allFixers as $fixer) {
                if (in_array($fixer->getName(), $names) && !in_array($fixer, $fixers)) {
                    $fixers[] = $fixer;
                }
            }
        }

        $config->fixers($fixers);

        $changed = $this->fixer->fix($config, $input->getOption('dry-run'), $input->getOption('diff'));

        $i = 1;
        switch ($input->getOption('format')) {
            case 'txt':
                foreach ($changed as $file => $fixResult) {
                    $output->write(sprintf('%4d) %s', $i++, $file));
                    if ($input->getOption('verbose')) {
                        $output->write(sprintf(' (<comment>%s</comment>)', implode(', ', $fixResult['appliedFixers'])));
                        if ($input->getOption('diff')) {
                            $output->writeln('');
                            $output->writeln('<comment>      ---------- begin diff ----------</comment>');
                            $output->writeln($fixResult['diff']);
                            $output->writeln('<comment>      ---------- end diff ----------</comment>');
                        }
                    }
                    $output->writeln('');
                }
                break;
            case 'xml':
                $dom = new \DOMDocument('1.0', 'UTF-8');
                $dom->appendChild($filesXML = $dom->createElement('files'));
                foreach ($changed as $file => $fixResult) {
                    $filesXML->appendChild($fileXML = $dom->createElement('file'));

                    $fileXML->setAttribute('id', $i++);
                    $fileXML->setAttribute('name', $file);
                    if ($input->getOption('verbose')) {
                        $fileXML->appendChild($appliedFixersXML = $dom->createElement('applied_fixers'));
                        foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                            $appliedFixersXML->appendChild($appliedFixerXML = $dom->createElement('applied_fixer'));
                            $appliedFixerXML->setAttribute('name', $appliedFixer);
                        }

                        if ($input->getOption('diff')) {
                            $fileXML->appendChild($diffXML = $dom->createElement('diff'));

                            $diffXML->appendChild($dom->createCDATASection($fixResult['diff']));
                        }
                    }
                }

                $dom->formatOutput = true;
                $output->write($dom->saveXML());
                break;
            case 'json':
                $json = array('files' => array());
                foreach ($changed as $file => $fixResult) {
                    $jfile = array('name' => $file);

                    if ($input->getOption('verbose')) {
                        $jfile['appliedFixers'] = $fixResult['appliedFixers'];
                        if ($input->getOption('diff')) {
                            $jfile['diff'] = $fixResult['diff'];
                        }
                    }

                    $json['files'][] = $jfile;
                }
                $output->write(json_encode($json));
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The format "%s" is not defined.', $input->getOption('format')));
        }

        return empty($changed) ? 0 : 1;
    }

    protected function getFixersHelp()
    {
        $fixers = '';
        $maxName = 0;
        foreach ($this->fixer->getFixers() as $fixer) {
            if (strlen($fixer->getName()) > $maxName) {
                $maxName = strlen($fixer->getName());
            }
        }

        $count = count($this->fixer->getFixers()) - 1;
        foreach ($this->fixer->getFixers() as $i => $fixer) {
            $chunks = explode("\n", wordwrap(sprintf('[%s] %s', $this->fixer->getLevelAsString($fixer), $fixer->getDescription()), 72 - $maxName, "\n"));
            $fixers .= sprintf(" * <comment>%s</comment>%s %s\n", $fixer->getName(), str_repeat(' ', $maxName - strlen($fixer->getName())), array_shift($chunks));
            while ($c = array_shift($chunks)) {
                $fixers .= str_repeat(' ', $maxName + 4).$c."\n";
            }

            if ($count !== $i) {
                $fixers .= "\n";
            }
        }

        return $fixers;
    }

    protected function getConfigsHelp()
    {
        $configs = '';
        $maxName = 0;
        foreach ($this->fixer->getConfigs() as $config) {
            if (strlen($config->getName()) > $maxName) {
                $maxName = strlen($config->getName());
            }
        }

        $count = count($this->fixer->getConfigs()) - 1;
        foreach ($this->fixer->getConfigs() as $i => $config) {
            $chunks = explode("\n", wordwrap($config->getDescription(), 72 - $maxName, "\n"));
            $configs .= sprintf(" * <comment>%s</comment>%s %s\n", $config->getName(), str_repeat(' ', $maxName - strlen($config->getName())), array_shift($chunks));
            while ($c = array_shift($chunks)) {
                $configs .= str_repeat(' ', $maxName + 4).$c."\n";
            }

            if ($count !== $i) {
                $configs .= "\n";
            }
        }

        return $configs;
    }
}
