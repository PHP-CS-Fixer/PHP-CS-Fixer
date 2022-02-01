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

/**
 * File reader that unify access to regular file and stdin-alike file.
 *
 * Regular file could be read multiple times with `file_get_contents`, but file provided on stdin cannot.
 * Consecutive try will provide empty content for stdin-alike file.
 * This reader unifies access to them.
 *
 * @internal
 */
final class FileReader
{
    /**
     * @var null|string
     */
    private $stdinContent;

    public static function createSingleton(): self
    {
        static $instance = null;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    public function read(string $filePath): string
    {
        if ('php://stdin' === $filePath) {
            if (null === $this->stdinContent) {
                $this->stdinContent = $this->readRaw($filePath);
            }

            return $this->stdinContent;
        }

        return $this->readRaw($filePath);
    }

    private function readRaw(string $realPath): string
    {
        $content = @file_get_contents($realPath);

        if (false === $content) {
            $error = error_get_last();

            throw new \RuntimeException(sprintf(
                'Failed to read content from "%s".%s',
                $realPath,
                $error ? ' '.$error['message'] : ''
            ));
        }

        return $content;
    }
}
