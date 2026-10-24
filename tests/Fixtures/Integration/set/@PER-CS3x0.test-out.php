<?php

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

    public const X = 1;
    public const Y = 2;
    public $aaa = 1;
    public $bbb = 2;

    public function sampleFunction($a, $arg1, $arg2, $arg3, $foo, $b = null)
    {
        if ($a === $b) {
            bar();
        } elseif ($a    >    $b) {
            $foo->bar($arg1);
        } elseif ($a < $b
            && null === $arg1) {
            foo();
        } else {
            BazClass::bar($arg2, $arg3);
        }

        $combined = $a . $b;
        $array
            = [
                'foo' => 'bar',
                'abc'
                    => 'edf',
            ];

        try {
            static::baz();
        } catch (\InvalidArgumentException|\ValueError $e) {
            foo();
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
    Ddd {}

$a = new Foo();
$b = (bool) 1;
$c = true ? (int) '1' : 2;

$anonymousClass = new class {
    public function test()
    {
        // method body
    }
};

$fn = fn($a) => $a;

$arrayNotMultiline = ['foo' => 'bar', 'foo2' => 'bar'];
$arrayMultiline = [
    'foo' => 'bar',
    'foo2' => 'bar',
];

$arrayMultilineWithoutComma = [
    'foo' => 'bar',
    'foo2' => 'bar',
];
$heredocMultilineWithoutComma = [
    'foo',
    <<<EOD
        bar
        EOD,
];
argumentsMultilineWithoutComma(
    1,
    2,
);
