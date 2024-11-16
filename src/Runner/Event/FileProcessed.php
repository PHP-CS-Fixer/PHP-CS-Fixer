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

namespace PhpCsFixer\Runner\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event that is fired when file was processed by Fixer.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileProcessed extends Event
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

    private int $status;

    private ?string $fileRelativePath;
    private ?string $fileHash;

    public function __construct(int $status, ?string $fileRelativePath = null, ?string $fileHash = null)
    {
        $this->status = $status;
        $this->fileRelativePath = $fileRelativePath;
        $this->fileHash = $fileHash;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getFileRelativePath(): ?string
    {
        return $this->fileRelativePath;
    }

    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }
}
