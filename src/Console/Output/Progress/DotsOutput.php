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

namespace PhpCsFixer\Console\Output\Progress;

use PhpCsFixer\Console\Output\OutputContext;
use PhpCsFixer\FixerFileProcessedEvent;

/**
 * Output writer to show the progress of a FixCommand using dots and meaningful letters.
 *
 * @internal
 */
final class DotsOutput extends OneCharLegendOutput
{
    private int $processedFiles = 0;

    private int $symbolsPerLine;

    public function __construct(OutputContext $context)
    {
        parent::__construct($context);

        // max number of characters per line
        // - total length x 2 (e.g. "  1 / 123" => 6 digits and padding spaces)
        // - 11               (extra spaces, parentheses and percentage characters, e.g. " x / x (100%)")
        $this->symbolsPerLine = max(1, $context->getTerminalWidth() - \strlen((string) $context->getFilesCount()) * 2 - 11);
    }

    public function onFixerFileProcessed(FixerFileProcessedEvent $event): void
    {
        $status = parent::getStatusMap()[$event->getStatus()];
        $this->getOutput()->write($this->getOutput()->isDecorated() ? \sprintf($status['format'], $status['symbol']) : $status['symbol']);

        ++$this->processedFiles;

        $symbolsOnCurrentLine = $this->processedFiles % $this->symbolsPerLine;
        $isLast = $this->processedFiles === $this->getContext()->getFilesCount();

        if (0 === $symbolsOnCurrentLine || $isLast) {
            $this->getOutput()->write(\sprintf(
                '%s %'.\strlen((string) $this->getContext()->getFilesCount()).'d / %d (%3d%%)',
                $isLast && 0 !== $symbolsOnCurrentLine ? str_repeat(' ', $this->symbolsPerLine - $symbolsOnCurrentLine) : '',
                $this->processedFiles,
                $this->getContext()->getFilesCount(),
                round($this->processedFiles / $this->getContext()->getFilesCount() * 100)
            ));

            if (!$isLast) {
                $this->getOutput()->writeln('');
            }
        }
    }

    public function shouldShowFileSummary(): bool
    {
        return true;
    }
}
