<?
namespace Vendor\Package;
use \FooInterfaceA, FooInterfaceB;
use \BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;
class Foo extends Bar implements FooInterfaceA{
    use FooTrait, BarTrait;

    var $aaa = 1, $bbb = 2;

    public function sampleFunction($a, $arg1, $arg2, $arg3, $foo, $b = null)
    {
        if ($a === $b) {
            bar();
        } else if ($a    >    $b) {
            $foo->bar($arg1);
        } else {
                BazClass::bar($arg2, $arg3);
        }

        STATIC::baz();
    }

    static public  final function bar() {
    // method body
    }
}

class Aaa implements
    Bbb, Ccc,
    Ddd
    {
    }

$a = new Foo;
$b = (boolean) 1;
$c = true  ?  1  :  2;

?>
