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
 * @author Krystian Marcisz <simivar@gmail.com>
 *
 * @internal
 */
final class RuleSetNameValidator
{
    public function isValid(string $name, bool $isCustom): bool
    {
        if (!$isCustom) {
            return 1 === Preg::match('/^\@[A-Z][A-Za-z0-9]*(:risky)?$/', $name);
        }

        return 1 === Preg::match('/^\@[A-Z][a-zA-Z0-9]*\/[A-Z][a-zA-Z0-9]*(:risky)?$/', $name);
    }
}
