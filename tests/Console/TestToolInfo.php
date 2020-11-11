<?php

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
    public function getComposerInstallationDetails()
    {
        throw new \BadMethodCallException();
    }

    public function getComposerVersion()
    {
        throw new \BadMethodCallException();
    }

    public function getVersion()
    {
        return Application::VERSION;
    }

    public function isInstalledAsPhar()
    {
        return true;
    }

    public function isInstalledByComposer()
    {
        throw new \BadMethodCallException();
    }

    public function getPharDownloadUri($version)
    {
        throw new \BadMethodCallException();
    }
}
