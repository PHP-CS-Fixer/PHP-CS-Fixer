<?php declare(strict_types=1);

// https://wiki.php.net/rfc/deprecations_php_8_5#deprecate_the_sleep_and_wakeup_magic_methods
class DoNotSerializeMe
{
    public function __serialize(): array
    {
        throw new \BadMethodCallException('No');
    }

    public function __unserialize(array $data): void
    {
        throw new \BadMethodCallException('No');
    }
}
