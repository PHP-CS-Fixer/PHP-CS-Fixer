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

namespace PhpCsFixer\Error;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 */
final class WorkerError
{
    private string $message;
    private string $filePath;
    private int $line;
    private int $code;
    private string $trace;

    public function __construct(string $message, string $filePath, int $line, int $code, string $trace)
    {
        $this->message = $message;
        $this->filePath = $filePath;
        $this->line = $line;
        $this->code = $code;
        $this->trace = $trace;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getTrace(): string
    {
        return $this->trace;
    }
}
