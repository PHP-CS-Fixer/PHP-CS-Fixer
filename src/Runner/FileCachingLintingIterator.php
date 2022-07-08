<?php

declare(strict_types=1);

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

use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingResultInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileCachingLintingIterator extends \CachingIterator
{
    private LinterInterface $linter;

    /**
     * @var LintingResultInterface
     */
    private $currentResult;

    /**
     * @var LintingResultInterface
     */
    private $nextResult;

    public function __construct(\Iterator $iterator, LinterInterface $linter)
    {
        parent::__construct($iterator);

        $this->linter = $linter;
    }

    public function currentLintingResult(): LintingResultInterface
    {
        return $this->currentResult;
    }

    public function next(): void
    {
        parent::next();

        $this->currentResult = $this->nextResult;

        if ($this->hasNext()) {
            $this->nextResult = $this->handleItem($this->getInnerIterator()->current());
        }
    }

    public function rewind(): void
    {
        parent::rewind();

        if ($this->valid()) {
            $this->currentResult = $this->handleItem($this->current());
        }

        if ($this->hasNext()) {
            $this->nextResult = $this->handleItem($this->getInnerIterator()->current());
        }
    }

    private function handleItem(\SplFileInfo $file): LintingResultInterface
    {
        return $this->linter->lintFile($file->getRealPath());
    }
}
