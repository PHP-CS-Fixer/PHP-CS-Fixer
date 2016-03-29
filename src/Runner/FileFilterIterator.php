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

use PhpCsFixer\FileCacheManager;
use PhpCsFixer\FixerFileProcessedEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileFilterIterator extends \FilterIterator
{
    /**
     * @var EventDispatcher|null
     */
    private $eventDispatcher;

    /**
     * @var FileCacheManager
     */
    private $cacheManager;

    /**
     * @var array<string,bool>
     */
    private $visitedElements = array();

    public function __construct(
        \Iterator $iterator,
        EventDispatcher $eventDispatcher = null,
        FileCacheManager $cacheManager
    ) {
        parent::__construct($iterator);

        $this->eventDispatcher = $eventDispatcher;
        $this->cacheManager = $cacheManager;
    }

    public function accept()
    {
        $file = $this->current();
        $path = $file->getRealPath();

        if (isset($this->visitedElements[$path])) {
            return false;
        }

        $this->visitedElements[$path] = true;

        if ($file->isDir() || $file->isLink()) {
            return false;
        }

        $content = file_get_contents($path);

        // mark as skipped:
        if (
            // empty file
            '' === $content
            // file uses __halt_compiler() on ~5.3.6 due to broken implementation of token_get_all
            || (PHP_VERSION_ID >= 50306 && PHP_VERSION_ID < 50400 && false !== stripos($content, '__halt_compiler()'))
            // file that does not need fixing due to cache
            || !$this->cacheManager->needFixing($this->getFileRelativePathname($file), $content)
        ) {
            $this->dispatchEvent(
                FixerFileProcessedEvent::NAME,
                FixerFileProcessedEvent::create()->setStatus(FixerFileProcessedEvent::STATUS_SKIPPED)
            );

            return false;
        }

        return true;
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
