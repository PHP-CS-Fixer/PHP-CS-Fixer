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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class RemoveLinesBetweenUsesFixerTest extends AbstractFixerTestBase
{
    public function testRemoveLinesBetweenUseStatements()
    {
        $expected = <<<'EOF'
<?php

use Zxy\Qux;
use Zoo\Bar as Bar2;
use Foo\Bar as Bar1;
use Foo\Zar\Baz;

$c = 1;

use Foo\Quxx as Quxx1;
use Foo\Zar\Quxx;

$a = new Bar1();
$a = new Bar2();
$a = new Baz();
$a = new Qux();
EOF
        ;

        $input = <<<'EOF'
<?php

use Zxy\Qux;

use Zoo\Bar as Bar2;

use Foo\Bar as Bar1;
use Foo\Zar\Baz;

$c = 1;

use Foo\Quxx as Quxx1;

use Foo\Zar\Quxx;

$a = new Bar1();
$a = new Bar2();
$a = new Baz();
$a = new Qux();
EOF
        ;

        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provideWithoutChanges
     */
    public function testWithoutChanges($expected)
    {
        $this->makeTest($expected);
    }

    public function provideWithoutChanges()
    {
        return array(
            array(
 <<<'EOF'
<?php

$c = 1;

$a = new Baz();
$a = new Qux();
EOF
            ),
            array(
                '<?php use A\B;',
            ),
            array(
                '<?php use A\B?>',
            ),
            array(
                '<?php use A\B;?>',
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
use some\a\{ClassA, ClassB, ClassC as C};
use function some\a\{fn_a, fn_b, fn_c};
use const some\a\{ConstA, ConstB, ConstC};
',
'<?php
use some\a\{ClassA, ClassB, ClassC as C};

use function some\a\{fn_a, fn_b, fn_c};

use const some\a\{ConstA, ConstB, ConstC};
',
            ),
        );
    }
}
