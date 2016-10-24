<?
namespace Vendor\Package;
use FooInterface, FooInterfaceB;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;
class Foo extends Bar implements FooInterface{
    var $aaa = 1, $bbb = 2;

    public function sampleFunction($a, $b = null)
    {
        if ($a === $b) {
            bar();
        } else if ($a > $b) {
            $foo->bar($arg1);
        } else {
                BazClass::bar($arg2, $arg3);
        }
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
?>
