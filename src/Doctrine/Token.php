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

namespace PhpCsFixer\Doctrine;

use UnitEnum;

/**
 * @template T of UnitEnum|string|int
 * @template V of string|int
 */
final class Token implements \ArrayAccess
{
    /**
     * The string value of the token in the input string.
     *
     * @readonly
     *
     * @var V
     */
    public $value;

    /**
     * The type of the token (identifier, numeric, string, input parameter, none).
     *
     * @readonly
     *
     * @var null|T
     */
    public $type;

    /**
     * The position of the token in the input string.
     *
     * @readonly
     *
     * @var int
     */
    public $position;

    /**
     * @param mixed $value
     * @param mixed $type
     */
    public function __construct($value, $type, int $position)
    {
        $this->value = $value;
        $this->type = $type;
        $this->position = $position;
    }

    /** @param T ...$types */
    public function isA(...$types): bool
    {
        return \in_array($this->type, $types, true);
    }

    /**
     * @deprecated Use the value, type or position property instead
     *
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return \in_array($offset, ['value', 'type', 'position'], true);
    }

    /**
     * @deprecated Use the value, type or position property instead
     *
     * @param mixed $offset
     *
     * @return mixed
     *
     * @template O of array-key
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * @deprecated no replacement planned
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->{$offset} = $value;
    }

    /**
     * @deprecated no replacement planned
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->{$offset} = null;
    }
}
