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

namespace PhpCsFixer;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event that is fired when file was processed by Fixer.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FixerFileProcessedEvent extends Event
{
    /**
     * Event name.
     */
    public const NAME = 'fixer.file_processed';

    public const STATUS_INVALID = 1;
    public const STATUS_SKIPPED = 2;
    public const STATUS_NO_CHANGES = 3;
    public const STATUS_FIXED = 4;
    public const STATUS_EXCEPTION = 5;
    public const STATUS_LINT = 6;

    private \SplFileInfo $file;

    private int $status;

    /** @var array<string> */
    private array $appliedFixers;

    /**
     * @param array<string> $appliedFixers
     */
    public function __construct(\SplFileInfo $file, int $status, array $appliedFixers = [])
    {
        $this->file = $file;
        $this->status = $status;
        $this->appliedFixers = $appliedFixers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    /**
     * @return array<string>
     */
    public function getAppliedFixers(): array
    {
        return $this->appliedFixers;
    }
}
