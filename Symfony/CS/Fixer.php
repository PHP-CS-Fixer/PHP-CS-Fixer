<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use SebastianBergmann\Diff\Differ;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Fixer
{
    const VERSION = '1.11-DEV';

    protected $fixers = array();
    protected $configs = array();

    /**
     * Differ instance.
     *
     * @var Differ
     */
    protected $diff;

    /**
     * EventDispatcher instance.
     *
     * @var EventDispatcher|null
     */
    protected $eventDispatcher;

    /**
     * ErrorsManager instance.
     *
     * @var ErrorsManager|null
     */
    protected $errorsManager;

    /**
     * LintManager instance.
     *
     * @var LintManager|null
     */
    protected $lintManager;

    /**
     * Stopwatch instance.
     *
     * @var Stopwatch|null
     */
    protected $stopwatch;

    public function __construct()
    {
        $this->diff = new Differ();
    }

    public function registerBuiltInFixers()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/Fixer') as $file) {
            $relativeNamespace = $file->getRelativePath();
            $class = 'Symfony\\CS\\Fixer\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
            $this->addFixer(new $class());
        }
    }

    public function registerCustomFixers($fixers)
    {
        foreach ($fixers as $fixer) {
            $this->addFixer($fixer);
        }
    }

    public function addFixer(FixerInterface $fixer)
    {
        $this->fixers[] = $fixer;
    }

    public function getFixers()
    {
        $this->sortFixers();

        return $this->fixers;
    }

    public function registerBuiltInConfigs()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/Config') as $file) {
            $relativeNamespace = $file->getRelativePath();
            $class = 'Symfony\\CS\\Config\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
            $this->addConfig(new $class());
        }
    }

    public function addConfig(ConfigInterface $config)
    {
        $this->configs[] = $config;
    }

    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Fixes all files for the given finder.
     *
     * @param ConfigInterface $config A ConfigInterface instance
     * @param bool            $dryRun Whether to simulate the changes or not
     * @param bool            $diff   Whether to provide diff
     *
     * @return array
     */
    public function fix(ConfigInterface $config, $dryRun = false, $diff = false)
    {
        $fixers = $this->prepareFixers($config);
        $changed = array();

        if ($this->stopwatch) {
            $this->stopwatch->openSection();
        }

        $fileCacheManager = new FileCacheManager($config->usingCache(), $config->getDir(), $config->getFixers());

        foreach ($config->getFinder() as $file) {
            if ($file->isDir() || $file->isLink()) {
                continue;
            }

            if ($this->stopwatch) {
                $this->stopwatch->start($this->getFileRelativePathname($file));
            }

            if ($fixInfo = $this->fixFile($file, $fixers, $dryRun, $diff, $fileCacheManager)) {
                $changed[$this->getFileRelativePathname($file)] = $fixInfo;
            }

            if ($this->stopwatch) {
                $this->stopwatch->stop($this->getFileRelativePathname($file));
            }
        }

        if ($this->stopwatch) {
            $this->stopwatch->stopSection('fixFile');
        }

        return $changed;
    }

    public function fixFile(\SplFileInfo $file, array $fixers, $dryRun, $diff, FileCacheManager $fileCacheManager)
    {
        $new = $old = file_get_contents($file->getRealpath());

        if (
            '' === $old
            || !$fileCacheManager->needFixing($this->getFileRelativePathname($file), $old)
            // PHP 5.3 has a broken implementation of token_get_all when the file uses __halt_compiler() starting in 5.3.6
            || (PHP_VERSION_ID >= 50306 && PHP_VERSION_ID < 50400 && false !== stripos($old, '__halt_compiler()'))
        ) {
            if ($this->eventDispatcher) {
                $this->eventDispatcher->dispatch(
                    FixerFileProcessedEvent::NAME,
                    FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_SKIPPED)
                );
            }

            return;
        }

        if ($this->lintManager && !$this->lintManager->createProcessForFile($file->getRealpath())->isSuccessful()) {
            if ($this->eventDispatcher) {
                $this->eventDispatcher->dispatch(
                    FixerFileProcessedEvent::NAME,
                    FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_INVALID)
                );
            }

            return;
        }

        $appliedFixers = array();

        // we do not need Tokens to still caching previously fixed file - so clear the cache
        Tokens::clearCache();

        try {
            foreach ($fixers as $fixer) {
                if (!$fixer->supports($file)) {
                    continue;
                }

                $newest = $fixer->fix($file, $new);
                if ($newest !== $new) {
                    $appliedFixers[] = $fixer->getName();
                }
                $new = $newest;
            }
        } catch (\Exception $e) {
            if ($this->eventDispatcher) {
                $this->eventDispatcher->dispatch(
                    FixerFileProcessedEvent::NAME,
                    FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_EXCEPTION)
                );
            }

            if ($this->errorsManager) {
                $this->errorsManager->report(ErrorsManager::ERROR_TYPE_EXCEPTION, $this->getFileRelativePathname($file), $e->__toString());
            }

            return;
        }

        $fixInfo = null;

        if ($new !== $old) {
            if ($this->lintManager) {
                $lintProcess = $this->lintManager->createProcessForSource($new);

                if (!$lintProcess->isSuccessful()) {
                    if ($this->eventDispatcher) {
                        $this->eventDispatcher->dispatch(
                            FixerFileProcessedEvent::NAME,
                            FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_LINT)
                        );
                    }

                    if ($this->errorsManager) {
                        $this->errorsManager->report(ErrorsManager::ERROR_TYPE_LINT, $this->getFileRelativePathname($file), $lintProcess->getOutput());
                    }

                    return;
                }
            }

            if (!$dryRun) {
                file_put_contents($file->getRealpath(), $new);
            }

            $fixInfo = array('appliedFixers' => $appliedFixers);

            if ($diff) {
                $fixInfo['diff'] = $this->stringDiff($old, $new);
            }
        }

        $fileCacheManager->setFile($this->getFileRelativePathname($file), $new);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                FixerFileProcessedEvent::NAME,
                FixerFileProcessedEvent::create()->setStatus($fixInfo ? FixerFileProcessedEvent::STATUS_FIXED : FixerFileProcessedEvent::STATUS_NO_CHANGES)
            );
        }

        return $fixInfo;
    }

    private function getFileRelativePathname(\SplFileInfo $file)
    {
        if ($file instanceof FinderSplFileInfo) {
            return $file->getRelativePathname();
        }

        return $file->getPathname();
    }

    public static function getLevelAsString(FixerInterface $fixer)
    {
        $level = $fixer->getLevel();

        if (($level & FixerInterface::NONE_LEVEL) === $level) {
            return 'none';
        }

        if (($level & FixerInterface::PSR0_LEVEL) === $level) {
            return 'PSR-0';
        }

        if (($level & FixerInterface::PSR1_LEVEL) === $level) {
            return 'PSR-1';
        }

        if (($level & FixerInterface::PSR2_LEVEL) === $level) {
            return 'PSR-2';
        }

        if (($level & FixerInterface::CONTRIB_LEVEL) === $level) {
            return 'contrib';
        }

        return 'symfony';
    }

    protected function stringDiff($old, $new)
    {
        $diff = $this->diff->diff($old, $new);

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
                explode(PHP_EOL, $diff)
            )
        );

        return $diff;
    }

    private function sortFixers()
    {
        usort($this->fixers, function (FixerInterface $a, FixerInterface $b) {
            return Utils::cmpInt($b->getPriority(), $a->getPriority());
        });
    }

    private function prepareFixers(ConfigInterface $config)
    {
        $fixers = $config->getFixers();

        foreach ($fixers as $fixer) {
            if ($fixer instanceof ConfigAwareInterface) {
                $fixer->setConfig($config);
            }
        }

        return $fixers;
    }

    /**
     * Set EventDispatcher instance.
     *
     * @param EventDispatcher|null $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcher $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Set ErrorsManager instance.
     *
     * @param ErrorsManager|null $errorsManager
     */
    public function setErrorsManager(ErrorsManager $errorsManager = null)
    {
        $this->errorsManager = $errorsManager;
    }

    /**
     * Set LintManager instance.
     *
     * @param LintManager|null $lintManager
     */
    public function setLintManager(LintManager $lintManager = null)
    {
        $this->lintManager = $lintManager;
    }

    /**
     * Set Stopwatch instance.
     *
     * @param Stopwatch|null $stopwatch
     */
    public function setStopwatch(Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }
}
