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
 * @TODO 4.0 Include parallel runner config in main ConfigInterface
 */
interface FailOnUnsupportedVersionConfigInterface extends ConfigInterface
{
    /**
     * Returns true if execution should fail on unsupported PHP version.
     */
    public function getFailOnUnsupportedVersion(): bool;

    public function setFailOnUnsupportedVersion(bool $failOnUnsupportedVersion): ConfigInterface;
}
