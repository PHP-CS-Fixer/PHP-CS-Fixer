<?php

declare(strict_types=1);

namespace Vendor\Package;

use FooInterfaceA;
use FooInterfaceB;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;

use function foo;

use const BAR;

class Foo extends Bar implements FooInterfaceA
{
    use FooTrait;
    use BarTrait;

    public $aaa = 1;
    public $bbb = 2;

    public function sampleFunction($a, $arg1, $arg2, $arg3, $foo, $b = null)
    {
        if ($a === $b) {
            bar();
        } elseif ($a    >    $b) {
            $foo->bar($arg1);
        } else {
            BazClass::bar($arg2, $arg3);
        }

        static::baz();
    }

    final public static function bar()
    {
        // method body
    }
}

class Aaa implements
    Bbb,
    Ccc,
    Ddd
{
}

$a = new Foo();
$b = (bool) 1;
$c = true ? (int) '1' : 2;

function bar(): void
{
}

$class = new class {};

class Baz
{
    public const FOO = 'foo';

    public function bar(): ?int
    {
        return 0;
    }
}
