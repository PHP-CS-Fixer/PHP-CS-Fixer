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
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\CS\Error\Error;
use Symfony\CS\Error\ErrorsManager;
use Symfony\CS\Linter\LinterInterface;
use Symfony\CS\Linter\LintingException;
use Symfony\CS\Linter\NullLinter;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Fixer
{
    const VERSION = '2.0-DEV';

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
     * Errors manager instance.
     *
     * @var ErrorsManager
     */
    protected $errorsManager;

    /**
     * Linter instance.
     *
     * @var LinterInterface
     */
    protected $linter;

    /**
     * Stopwatch instance.
     *
     * @var Stopwatch
     */
    protected $stopwatch;

    public function __construct()
    {
        $this->diff = new Differ();
        $this->errorsManager = new ErrorsManager();
        $this->linter = new NullLinter();
        $this->stopwatch = new Stopwatch();
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
     * Get the errors manager instance.
     *
     * @return ErrorsManager
     */
    public function getErrorsManager()
    {
        return $this->errorsManager;
    }

    /**
     * Get stopwatch instance.
     *
     * @return Stopwatch
     */
    public function getStopwatch()
    {
        return $this->stopwatch;
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

        $this->stopwatch->openSection();

        $fileCacheManager = new FileCacheManager(
            $config->usingCache(),
            $config->getCacheFile(),
            $config->getFixers()
        );

        foreach ($config->getFinder() as $file) {
            if ($file->isDir() || $file->isLink()) {
                continue;
            }

            $this->stopwatch->start($this->getFileRelativePathname($file));

            if ($fixInfo = $this->fixFile($file, $fixers, $dryRun, $diff, $fileCacheManager)) {
                $changed[$this->getFileRelativePathname($file)] = $fixInfo;
            }

            $this->stopwatch->stop($this->getFileRelativePathname($file));
        }

        $this->stopwatch->stopSection('fixFile');

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
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_SKIPPED)
            );

            return;
        }

        try {
            $this->linter->lintFile($file->getRealpath());
        } catch (LintingException $e) {
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_INVALID)
            );

            $this->errorsManager->report(new Error(
                Error::TYPE_INVALID,
                $this->getFileRelativePathname($file)
            ));

            return;
        }

        $old = file_get_contents($file->getRealpath());
        $appliedFixers = array();

        // we do not need Tokens to still caching previously fixed file - so clear the cache
        Tokens::clearCache();

        $tokens = Tokens::fromCode($old);
        $newHash = $oldHash = $tokens->getCodeHash();

        try {
            foreach ($fixers as $fixer) {
                if (!$fixer->supports($file) || !$fixer->isCandidate($tokens)) {
                    continue;
                }

                $fixer->fix($file, $tokens);

                if ($tokens->isChanged()) {
                    $tokens->clearEmptyTokens();
                    $tokens->clearChanged();
                    $appliedFixers[] = $fixer->getName();
                }
            }
        } catch (\Exception $e) {
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_EXCEPTION)
            );

            $this->errorsManager->report(new Error(
                Error::TYPE_EXCEPTION,
                $this->getFileRelativePathname($file)
            ));

            return;
        }

        $fixInfo = null;

        if (!empty($appliedFixers)) {
            $new = $tokens->generateCode();
            $newHash = $tokens->getCodeHash();
        }

        // We need to check if content was changed and then applied changes.
        // But we can't simple check $appliedFixers, because one fixer may revert
        // work of other and both of them will mark collection as changed.
        // Therefore we need to check if code hashes changed.
        if ($oldHash !== $newHash) {
            try {
                $this->linter->lintSource($new);
            } catch (LintingException $e) {
                $this->dispatchEvent(
                    FixerFileProcessedEvent::NAME,
                    FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_LINT)
                );

                $this->errorsManager->report(new Error(
                    Error::TYPE_LINT,
                    $this->getFileRelativePathname($file)
                ));

                return;
            }

            if (!$dryRun && false === @file_put_contents($file->getRealpath(), $new)) {
                $error = error_get_last();
                if ($error) {
                    throw new IOException(sprintf('Failed to write file "%s", "%s".', $file->getRealpath(), $error['message']), 0, null, $file->getRealpath());
                }
                throw new IOException(sprintf('Failed to write file "%s".', $file->getRealpath()), 0, null, $file->getRealpath());
            }

            $fixInfo = array('appliedFixers' => $appliedFixers);

            if ($diff) {
                $fixInfo['diff'] = $this->stringDiff($old, $new);
            }
        }

        $fileCacheManager->setFile($this->getFileRelativePathname($file), $new);

        $this->dispatchEvent(
            FixerFileProcessedEvent::NAME,
            FixerFileProcessedEvent::create()->setStatus($fixInfo ? FixerFileProcessedEvent::STATUS_FIXED : FixerFileProcessedEvent::STATUS_NO_CHANGES)
        );

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
     * Set linter instance.
     *
     * @param LinterInterface $linter
     */
    public function setLinter(LinterInterface $linter)
    {
        $this->linter = $linter;
    }

    /**
     * Dispatch event.
     *
     * @param string $name
     * @param Event  $event
     */
    private function dispatchEvent($name, Event $event)
    {
        if (null === $this->eventDispatcher) {
            return;
        }

        $this->eventDispatcher->dispatch($name, $event);
    }
}
