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

namespace PhpCsFixer;

use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\NullLinter;
use PhpCsFixer\Tokenizer\Tokens;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Fixer
{
    const VERSION = '2.0-DEV';

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
        $changed = array();
        $fixers = $config->getFixers();

        $this->stopwatch->openSection();

        $fileCacheManager = new FileCacheManager(
            $config->usingCache(),
            $config->getCacheFile(),
            $config->getRules()
        );

        $finder = $config->getFinder();
        $finderIterator = $finder instanceof \IteratorAggregate ? $finder->getIterator() : $finder;

        foreach (new UniqueFileIterator($finderIterator) as $file) {
            $name = $this->getFileRelativePathname($file);

            $this->stopwatch->start($name);

            if ($fixInfo = $this->fixFile($file, $fixers, $dryRun, $diff, $fileCacheManager)) {
                $changed[$name] = $fixInfo;
            }

            $this->stopwatch->stop($name);
        }

        $this->stopwatch->stopSection('fixFile');

        return $changed;
    }

    public function fixFile(\SplFileInfo $file, array $fixers, $dryRun, $diff, FileCacheManager $fileCacheManager)
    {
        $new = $old = file_get_contents($file->getRealPath());

        $name = $this->getFileRelativePathname($file);

        if (
            '' === $old
            || !$fileCacheManager->needFixing($name, $old)
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
            $this->linter->lintFile($file->getRealPath());
        } catch (LintingException $e) {
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_INVALID)
            );

            $this->errorsManager->report(new Error(Error::TYPE_INVALID, $name));

            return;
        }

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

            $this->errorsManager->report(new Error(Error::TYPE_EXCEPTION, $name));

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

                $this->errorsManager->report(new Error(Error::TYPE_LINT, $name));

                return;
            }

            if (!$dryRun && false === @file_put_contents($file->getRealPath(), $new)) {
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

        $fileCacheManager->setFile($name, $new);

        $this->dispatchEvent(
            FixerFileProcessedEvent::NAME,
            FixerFileProcessedEvent::create()->setStatus($fixInfo ? FixerFileProcessedEvent::STATUS_FIXED : FixerFileProcessedEvent::STATUS_NO_CHANGES)
        );

        return $fixInfo;
    }

    private function getFileRelativePathname(\SplFileInfo $file)
    {
        if ($file instanceof SymfonySplFileInfo) {
            return $file->getRelativePathname();
        }

        return $file->getPathname();
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
