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

use PhpCsFixer\FixerFileProcessedEvent;

/**
 * Ideas for improvements:
 *   - clickable file name (open file in IDE, should have Docker support).
 *
 * @internal
 */
abstract class FileInfoOutput extends OneCharLegendOutput
{
    public function onFixerFileProcessed(FixerFileProcessedEvent $event): void
    {
        $status = parent::getStatusMap()[$event->getStatus()];
        $this->getOutput()->writeln(\sprintf(
            '[%s] %s%s',
            $this->getOutput()->isDecorated() ? \sprintf($status['format'], $status['symbol']) : $status['symbol'],
            $event->getFileRelativePath(),
            [] !== $event->getAppliedFixers()
                ? \sprintf(' (%s)', implode(', ', array_map(
                    fn (string $fixer): string => $this->getOutput()->isDecorated() ? "<comment>{$fixer}</comment>" : $fixer,
                    $event->getAppliedFixers()
                )))
                : ''
        ));
    }

    public function shouldShowFileSummary(): bool
    {
        return false;
    }
}
