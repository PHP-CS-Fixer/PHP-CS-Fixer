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

namespace PhpCsFixer\Indicator;

/**
 * @internal
 */
final class ClassyExistanceIndicator
{
    public function exists(string $name): bool
    {
        if (class_exists($name) || interface_exists($name) || trait_exists($name)) {
            $rc = new \ReflectionClass($name);

            return $rc->getName() === $name;
        }

        return false;
    }
}
