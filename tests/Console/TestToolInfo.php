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

namespace PhpCsFixer\Tests\Console;

use PhpCsFixer\Console\Application;
use PhpCsFixer\ToolInfoInterface;

/**
 * @internal
 */
final class TestToolInfo implements ToolInfoInterface
{
    public function getComposerInstallationDetails(): array
    {
        throw new \BadMethodCallException();
    }

    public function getComposerVersion(): string
    {
        throw new \BadMethodCallException();
    }

    public function getVersion(): string
    {
        return Application::VERSION;
    }

    public function isInstalledAsPhar(): bool
    {
        return true;
    }

    public function isInstalledByComposer(): bool
    {
        throw new \BadMethodCallException();
    }

    public function getPharDownloadUri(string $version): string
    {
        throw new \BadMethodCallException();
    }
}
