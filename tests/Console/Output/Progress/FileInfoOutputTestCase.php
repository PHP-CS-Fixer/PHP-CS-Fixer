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

namespace PhpCsFixer\Tests\Console\Output\Progress;

use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 */
abstract class FileInfoOutputTestCase extends TestCase
{
    /**
     * @param list<array{0: string, 1: FixerFileProcessedEvent::STATUS_*, 2?: list<string>}> $statuses
     * @param \Closure(FixerFileProcessedEvent::STATUS_*, string, list<string>): void        $action
     */
    protected function foreachStatus(array $statuses, \Closure $action): void
    {
        foreach ($statuses as $status) {
            $action($status[1], $status[0], $status[2] ?? []);
        }
    }

    protected function splFileStub(string $file): \SplFileInfo
    {
        return new class($file) extends \SplFileInfo {
            private string $file;

            public function __construct(string $file)
            {
                parent::__construct($file);

                $this->file = $file;
            }

            public function getPathname(): string
            {
                $rootDir = realpath(__DIR__.'/../../../../');

                return $rootDir.'/'.$this->file;
            }
        };
    }
}
