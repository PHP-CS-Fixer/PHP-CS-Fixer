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
 *
 * @extends \IteratorIterator<mixed, \SplFileInfo, \Traversable<\SplFileInfo>>
 */
final class LintingFileIterator extends \IteratorIterator implements LintingResultAwareFileIteratorInterface
{
    /**
     * @var null|LintingResultInterface
     */
    private $currentResult;

    private LinterInterface $linter;

    /**
     * @param \Iterator<mixed, \SplFileInfo> $iterator
     */
    public function __construct(\Iterator $iterator, LinterInterface $linter)
    {
        parent::__construct($iterator);

        $this->linter = $linter;
    }

    public function currentLintingResult(): ?LintingResultInterface
    {
        return $this->currentResult;
    }

    public function next(): void
    {
        parent::next();

        $this->currentResult = $this->valid() ? $this->handleItem($this->current()) : null;
    }

    public function rewind(): void
    {
        parent::rewind();

        $this->currentResult = $this->valid() ? $this->handleItem($this->current()) : null;
    }

    private function handleItem(\SplFileInfo $file): LintingResultInterface
    {
        return $this->linter->lintFile($file->getRealPath());
    }
}
