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
 * Output writer which prints status, file name and list of applied fixers BUT only for meaningful statuses.
 * This writer does not print info for files that were not changed or were skipped.
 *
 * @internal
 */
final class OnlyMeaningfulOutput extends FileInfoOutput
{
    /**
     * @var array<int, null>
     */
    private const STATUSES_TO_IGNORE = [
        FixerFileProcessedEvent::STATUS_NO_CHANGES => null,
        FixerFileProcessedEvent::STATUS_SKIPPED => null,
    ];

    public function onFixerFileProcessed(FixerFileProcessedEvent $event): void
    {
        if (\array_key_exists($event->getStatus(), self::STATUSES_TO_IGNORE)) {
            return;
        }

        parent::onFixerFileProcessed($event);
    }

    protected function getStatusMap(): array
    {
        return array_diff_key(parent::getStatusMap(), self::STATUSES_TO_IGNORE);
    }
}
