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

namespace PhpCsFixer\Runner;

use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\Cache\FileCacheManager;
use PhpCsFixer\Cache\FileHandler;
use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Cache\Signature;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\ToolInfo;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class Runner
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var DifferInterface
     */
    private $differ;

    /**
     * @var EventDispatcher|null
     */
    private $eventDispatcher;

    /**
     * @var ErrorsManager
     */
    private $errorsManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var bool
     */
    private $isDryRun;

    /**
     * @var LinterInterface
     */
    private $linter;

    public function __construct(
        ConfigInterface $config,
        DifferInterface $differ,
        EventDispatcher $eventDispatcher = null,
        ErrorsManager $errorsManager,
        LinterInterface $linter,
        $isDryRun
    ) {
        $this->config = $config;
        $this->differ = $differ;
        $this->eventDispatcher = $eventDispatcher;
        $this->errorsManager = $errorsManager;
        $this->linter = $linter;
        $this->isDryRun = $isDryRun;

        $this->cacheManager = $this->createCacheManager(
            $config,
            $isDryRun
        );
    }

    /**
     * @param ConfigInterface $config
     * @param bool            $isDryRun
     *
     * @return CacheManagerInterface
     */
    private function createCacheManager(ConfigInterface $config, $isDryRun)
    {
        if ($config->usingCache() && (ToolInfo::isInstalledAsPhar() || ToolInfo::isInstalledByComposer())) {
            return new FileCacheManager(
                new FileHandler($config->getCacheFile()),
                new Signature(
                    PHP_VERSION,
                    ToolInfo::getVersion(),
                    $config->usingLinter(),
                    $config->getRules()
                ),
                $isDryRun
            );
        }

        return new NullCacheManager();
    }

    /**
     * @return array
     */
    public function fix()
    {
        $changed = array();
        $config = $this->config;

        $finder = $config->getFinder();
        $finderIterator = $finder instanceof \IteratorAggregate ? $finder->getIterator() : $finder;

        $collection = new FileLintingIterator(
            new FileFilterIterator(
                $finderIterator,
                $this->eventDispatcher,
                $this->cacheManager
            ),
            $this->linter
        );

        foreach ($collection as $file) {
            $fixInfo = $this->fixFile($file, $collection->currentLintingResult());

            if ($fixInfo) {
                $name = $this->getFileRelativePathname($file);
                $changed[$name] = $fixInfo;
            }
        }

        return $changed;
    }

    private function fixFile(\SplFileInfo $file, LintingResultInterface $lintingResult)
    {
        $name = $this->getFileRelativePathname($file);

        try {
            $lintingResult->check();
        } catch (LintingException $e) {
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_INVALID)
            );

            $this->errorsManager->report(new Error(Error::TYPE_INVALID, $name));

            return;
        }

        $fixers = $this->config->getFixers();

        $old = file_get_contents($file->getRealPath());
        $new = $old;
        $name = $this->getFileRelativePathname($file);

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
            $this->processException($name);

            return;
        } catch (\Throwable $e) {
            $this->processException($name);

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
                $this->linter->lintSource($new)->check();
            } catch (LintingException $e) {
                $this->dispatchEvent(
                    FixerFileProcessedEvent::NAME,
                    FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_LINT)
                );

                $this->errorsManager->report(new Error(Error::TYPE_LINT, $name));

                return;
            }

            if (!$this->isDryRun) {
                if (false === @file_put_contents($file->getRealPath(), $new)) {
                    $error = error_get_last();
                    if ($error) {
                        throw new IOException(sprintf('Failed to write file "%s", "%s".', $file->getRealPath(), $error['message']), 0, null, $file->getRealPath());
                    }
                    throw new IOException(sprintf('Failed to write file "%s".', $file->getRealPath()), 0, null, $file->getRealPath());
                }
            }

            $fixInfo = array(
                'appliedFixers' => $appliedFixers,
                'diff' => $this->differ->diff($old, $new),
            );
        }

        $this->cacheManager->setFile($name, $new);

        $this->dispatchEvent(
            FixerFileProcessedEvent::NAME,
            FixerFileProcessedEvent::create()->setStatus($fixInfo ? FixerFileProcessedEvent::STATUS_FIXED : FixerFileProcessedEvent::STATUS_NO_CHANGES)
        );

        return $fixInfo;
    }

    /**
     * Process an exception that occurred.
     *
     * @param string $name
     */
    private function processException($name)
    {
        $this->dispatchEvent(
            FixerFileProcessedEvent::NAME,
            FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_EXCEPTION)
        );

        $this->errorsManager->report(new Error(Error::TYPE_EXCEPTION, $name));
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

    private function getFileRelativePathname(\SplFileInfo $file)
    {
        if ($file instanceof SymfonySplFileInfo) {
            return $file->getRelativePathname();
        }

        return $file->getPathname();
    }
}
