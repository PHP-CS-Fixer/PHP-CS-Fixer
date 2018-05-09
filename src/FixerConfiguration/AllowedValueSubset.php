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

namespace PhpCsFixer\FixerConfiguration;

/**
 * @internal
 */
final class AllowedValueSubset
{
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function __invoke($values)
    {
        if (!is_array($values)) {
            return false;
        }

        foreach ($values as $value) {
            if (!in_array($value, $this->values, true)) {
                return false;
            }
        }

        return true;
    }

    public function getValues()
    {
        return $this->values;
    }
}
