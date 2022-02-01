<?php
namespace Vendor\Package;

use FooInterfaceA;
use FooInterfaceB;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;

class Foo extends Bar implements FooInterfaceA
{
    const SOME_CONST = 42;

    public $aaa = 1;
    public $bbb = 2;

    public function sampleFunction($a, $arg1, $arg2, $arg3, $foo, $b = null)
    {
        if ($a === $b) {
            bar();
        } elseif ($a > $b) {
            $foo->bar($arg1);
        } else {
            BazClass::bar($arg2, $arg3);
        }
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
