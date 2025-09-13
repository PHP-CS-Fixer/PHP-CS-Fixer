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

namespace PhpCsFixer\RuleSet;

use PhpCsFixer\Preg;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
abstract class AbstractMigrationSetDescription extends AbstractRuleSetDescription
{
    public function getDescription(): string
    {
        $name = $this->getName();

        // @TODO v4 - `x?` -> `x`
        if (Preg::match('#^@PHPUnit(\d+)x?(\d)Migration.*$#', $name, $matches)) {
            return \sprintf('Rules to improve tests code for PHPUnit %d.%d compatibility.', $matches[1], $matches[2]);
        }

        // @TODO v4 - `x?` -> `x`
        if (Preg::match('#^@PHP(\d)x?(\d)Migration.*$#', $name, $matches)) {
            return \sprintf('Rules to improve code for PHP %d.%d compatibility.', $matches[1], $matches[2]);
        }

        throw new \RuntimeException(\sprintf('Cannot generate description for "%s" "%s".', static::class, $name));
    }
}
