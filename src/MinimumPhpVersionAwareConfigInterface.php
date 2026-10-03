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
 * @TODO 4.0 Include in main ConfigInterface
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
interface MinimumPhpVersionAwareConfigInterface extends ConfigInterface
{
    /**
     * Returns the minimum PHP version supported by the project, overriding detection from composer.json.
     *
     * @return null|non-empty-string major.minor version, e.g. "8.1", or null when relying on composer.json detection
     */
    public function getMinimumPhpVersion(): ?string;

    /**
     * @param null|non-empty-string $minimumPhpVersion major.minor version, e.g. "8.1"
     *
     * @return $this
     */
    public function setMinimumPhpVersion(?string $minimumPhpVersion): ConfigInterface;
}
