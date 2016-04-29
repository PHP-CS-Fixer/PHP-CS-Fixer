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
        );
    }
}
