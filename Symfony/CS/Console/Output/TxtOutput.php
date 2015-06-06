<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\CS\Events\FixerConfigurationResolvedEvent;
use Symfony\CS\Events\FixerFinishedEvent;
use Symfony\CS\Fixer;

class TxtOutput extends AbstractOutput
{
    public function onFixerConfigurationResolved(FixerConfigurationResolvedEvent $event)
    {
        if (OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity() && extension_loaded('xdebug')) {
            $format = $this->output->isDecorated() ? '<comment>Warning</comment>%s' : '%s';
            $this->output->writeln(sprintf($format, ' xdebug loaded, this might have a significant impact on the run time of the Fixer.'));
        }

        if (OutputInterface::VERBOSITY_VERY_VERBOSE <= $this->output->getVerbosity()) {
            $this->output->writeln($this->output->isDecorated() ? '<comment>Environment:</comment>' : $this->output->writeln('Environment:'));
            $this->writeConfigItem('PHP version', phpversion());
            $this->writeConfigItem('Operating system', php_uname());

            $config = $event->getConfig();

            $this->output->writeln($this->output->isDecorated() ? '<comment>Configuration:</comment>' : $this->output->writeln('Configuration:'));

            if (null !== $event->getConfigFile()) {
                $this->writeConfigItem('File', $event->getConfigFile());
            }

            $this->writeConfigItem('Level', Fixer::getLevelDescription($config->getLevel()));
            $this->writeConfigItem('Description', $config->getDescription());
            $this->writeConfigItem('Directory', $config->getDir());

            if ($config->usingCache()) {
                $this->writeConfigItem('Use cache', 'Yes');
                $this->writeConfigItem('Cache file', $config->getCacheFile());
            } else {
                $this->writeConfigItem('Use cache', 'No');
            }

            if ($config->usingLinter()) {
                $this->writeConfigItem('Use linter', 'Yes');
                $this->writeConfigItem('PHP executable', null === $config->getPhpExecutable() ? 'default' : $config->getPhpExecutable());
            } else {
                $this->writeConfigItem('Use linter', 'No');
            }

            if (OutputInterface::VERBOSITY_VERY_VERBOSE < $this->output->getVerbosity()) {
                $this->output->writeln($this->output->isDecorated() ? '<comment>Fixers:</comment>' : $this->output->writeln('Fixers:'));
                foreach ($config->getFixers() as $fixer) {
                    $this->writeConfigItem($fixer->getName(), $fixer->getDescription(), 40);
                }
            }

            $this->output->writeln('');
        } elseif (null !== $event->getConfigFile()) {
            $this->output->writeln(sprintf('Loaded config from "%s"', $event->getConfigFile()));
            $this->output->writeln('');
        }
    }

    private function writeConfigItem($name, $value, $padding = 18)
    {
        if ($this->output->isDecorated()) {
            $format = sprintf('<info>%%-%ds</info>%%s', $padding);
        } else {
            $format = sprintf('%%-%ds %%s', $padding);
        }
        $this->output->writeln(sprintf($format, $name, $value));
    }

    public function onFixerFinished(FixerFinishedEvent $event)
    {
        $verbosity = $this->output->getVerbosity();
        $changes = $event->getChanged();
        if (empty($changes)) {
            if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                $this->output->writeln('No changes made.');
            }
        } else {
            $i = 0;
            foreach ($changes as $file => $fixResult) {
                $this->output->write(sprintf('%4d) %s', $i++, $file));

                if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                    $format = $this->output->isDecorated() ? ' (<comment>%s</comment>)' : ' (%s)';
                    $this->output->write(sprintf($format, implode(', ', $fixResult['appliedFixers'])));
                }

                if ($event->isDiff()) {
                    $this->output->writeln('');
                    if ($this->output->isDecorated()) {
                        $this->output->writeln('<comment>      ---------- begin diff ----------</comment>');

                        $diff = implode(
                            PHP_EOL,
                            array_map(
                                function ($string) {
                                    $string = preg_replace('/^(\+){3}/', '<info>+++</info>', $string);
                                    $string = preg_replace('/^(\+){1}/', '<info>+</info>', $string);
                                    $string = preg_replace('/^(\-){3}/', '<error>---</error>', $string);
                                    $string = preg_replace('/^(\-){1}/', '<error>-</error>', $string);
                                    $string = str_repeat(' ', 6).$string;

                                    return $string;
                                },
                                explode(PHP_EOL, $fixResult['diff'])
                            )
                        );
                        $this->output->writeln($diff);
                        $this->output->writeln('<comment>      ---------- end diff ----------</comment>');
                    } else {
                        $this->output->writeln('      ---------- begin diff ----------');
                        $this->output->writeln($fixResult['diff']);
                        $this->output->writeln('      ---------- end diff ----------');
                    }
                }

                $this->output->writeln('');
            }

            if (OutputInterface::VERBOSITY_DEBUG <= $verbosity) {
                $stopwatch = $event->getStopWatch();
                $this->output->writeln('Fixing time per file:');

                foreach ($stopwatch->getSectionEvents('fixFile') as $file => $stopwatchEvent) {
                    if ('__section__' === $file) {
                        continue;
                    }

                    $this->output->writeln(sprintf(' [%.3f s] %s', $stopwatchEvent->getDuration() / 1000, $file));
                }

                $this->output->writeln('');
            }
        }

        $errorManager = $event->getErrors();

        $invalidErrors = $errorManager->getConfigurationErrors();
        if (!empty($invalidErrors)) {
            $this->listErrors('configuration before fixing', $invalidErrors);
        }

        $invalidErrors = $errorManager->getInvalidErrors();
        if (!empty($invalidErrors)) {
            $this->listErrors('linting before fixing', $invalidErrors);
        }

        $exceptionErrors = $errorManager->getExceptionErrors();
        if (!empty($exceptionErrors)) {
            $this->listErrors('fixing', $exceptionErrors);
        }

        $lintErrors = $errorManager->getLintErrors();
        if (!empty($lintErrors)) {
            $this->listErrors('linting after fixing', $lintErrors);
        }

        $this->output->writeln('');
        $this->output->writeln(sprintf('All files %s in %.3f seconds, %.3f MB memory used', $event->isDryRun() || empty($changes) ? 'checked' : 'fixed', $event->getDuration() / 1000, $event->getMemoryUsed() / 1024 / 1024));
    }

    private function listErrors($process, array $errors)
    {
        $this->output->writeLn(sprintf('Files that were not fixed due to errors reported during %s:', $process));

        foreach ($errors as $i => $error) {
            if (OutputInterface::VERBOSITY_VERY_VERBOSE <= $this->output->getVerbosity()) {
                $this->output->writeLn(sprintf('%4d) %s %s', $i + 1, $error->getFilePath(), $error->getSource() === null ? '' : $error->getSource()->getMessage()));
            } else {
                $this->output->writeLn(sprintf('%4d) %s', $i + 1, $error->getFilePath()));
            }
        }

        $this->output->writeLn('');
    }

    public static function getSubscribedEvents()
    {
        return array(
            FixerConfigurationResolvedEvent::NAME => 'onFixerConfigurationResolved',
            FixerFinishedEvent::NAME => 'onFixerFinished',
        );
    }
}
