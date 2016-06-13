<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Ceeram <ceeram@cakephp.org>
 */
class SingleLineAfterImportsFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php use \Exception;

?>
<?php
$a = new Exception();
',
                '<?php use \Exception?>
<?php
$a = new Exception();
',
            ),
            array(
                '<?php use \stdClass;
use \DateTime;

?>
<?php
$a = new DateTime();
',
                '<?php use \stdClass; use \DateTime?>
<?php
$a = new DateTime();
', ),
            array(
                '<?php namespace Foo;
              '.'
use Bar\Baz;

/**
 * Foo.
 */',
                '<?php namespace Foo;
              '.'
use Bar\Baz;
/**
 * Foo.
 */',
            ),
            array(
                '<?php
namespace A\B;

use D;

class C {}
',
                '<?php
namespace A\B;

use D;
class C {}
',
            ),
            array(
                '<?php
    namespace A\B;

    use D;

    class C {}
',
                '<?php
    namespace A\B;

    use D;
    class C {}
',
            ),
            array(
                '<?php
namespace A\B;

use D;
use E;

class C {}
',
                '<?php
namespace A\B;

use D;
use E;
class C {}
',
            ),
            array(
                '<?php
namespace A\B;

use D;

class C {}
',
                '<?php
namespace A\B;

use D; class C {}
',
            ),
            array(
                '<?php
namespace A\B;
use D;
use E;

{
    class C {}
}',
                '<?php
namespace A\B;
use D; use E; {
    class C {}
}',
            ),
            array(
                '<?php
namespace A\B;
use D;
use E;

{
    class C {}
}',
                '<?php
namespace A\B;
use D;
use E; {
    class C {}
}',
            ),
            array(
                '<?php
namespace A\B {
    use D;
    use E;

    class C {}
}',
                '<?php
namespace A\B {
    use D; use E; class C {}
}',
            ),
            array(
                '<?php
namespace A\B;
class C {
    use SomeTrait;
}',
            ),
            array(
                '<?php
$lambda = function () use (
    $arg
){
    return true;
};',
            ),
            array(
                '<?php
namespace A\B;
use D, E;

class C {

}',
                '<?php
namespace A\B;
use D, E;
class C {

}',
            ),
            array(
                '<?php
    namespace A1;
    use B1; // need to import this !
    use B2;

    class C1 {}
',
            ),
            array(
                '<?php
    namespace A1;
    use B1;// need to import this !
    use B2;

    class C1 {}
',
            ),
            array(
                '<?php
namespace A1;
use B1; // need to import this !
use B2;

class C1 {}
',
            ),
            array(
                '<?php
namespace A1;
use B1;// need to import this !
use B2;

class C1 {}
',
            ),
            array(
                '<?php
namespace A1;
use B1; /** need to import this !*/
use B2;

class C1 {}
',
            ),
            array(
                '<?php
namespace A1;
use B1;// need to import this !
use B2;

class C1 {}
',
            ),
            array(
                '<?php
    namespace A1;
    use B1; // need to import this !
    use B2;

    class C1 {}
',
                '<?php
    namespace A1;
    use B1; // need to import this !

    use B2;

    class C1 {}
',
            ),
            array(
                '<?php
namespace Foo;

use Bar;
use Baz;

class Hello {}
',
                '<?php
namespace Foo;

use Bar;
use Baz;


class Hello {}
',
            ),
            array(
                '<?php
class Hello {
    use SomeTrait;

    use Another;// ensure use statements for traits are not touched
}
',
            ),
            array(
                '<?php
namespace Foo {}
namespace Bar {
    class Baz
    {
        use Aaa;
    }
}
',
            ),
            array(
                '<?php use A\B;

?>',
                '<?php use A\B?>',
            ),
            array(
                '<?php use A\B;

',
                '<?php use A\B;',
            ),
        );
    }

    /**
     * @dataProvider provide70Cases
     * @requires PHP 7.0
     */
    public function test70($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provide70Cases()
    {
        return array(
            array(
                '<?php
use some\test\{ClassA, ClassB, ClassC as C};

?>
test 123
',
                '<?php
use some\test\{ClassA, ClassB, ClassC as C}         ?>
test 123
',
            ),
            array(
                '<?php
use some\test\{CA, Cl, ClassC as C};

class Test {}
',
                '<?php
use some\test\{CA, Cl, ClassC as C};
class Test {}
',
            ),
            array(
                '<?php
use function some\test\{fn_g, fn_f, fn_e};

fn_a();',
                '<?php
use function some\test\{fn_g, fn_f, fn_e};
fn_a();',
            ),
            array(
                '<?php
use const some\test\{ConstA, ConstB, ConstD};

',
                '<?php
use const some\test\{ConstA, ConstB, ConstD};
',
            ),
            array(
                '<?php
namespace Z\B;
use const some\test\{ConstA, ConstB, ConstC};
use A\B\C;

',
                '<?php
namespace Z\B;
use const some\test\{ConstA, ConstB, ConstC};
use A\B\C;
',
            ),
        );
    }
}
