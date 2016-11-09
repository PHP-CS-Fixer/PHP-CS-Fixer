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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
class MultipleUseFixerTest extends AbstractFixerTestBase
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
                    /**/use Foo;
use FooB;
                ',
                '<?php
                    /**/use Foo,FooB;
                ',
            ),
            array(
                <<<'EOF'
use Some, Not, PHP, Like, Use, Statement;
<?php

use Foo;
use FooA;
use FooB;
use FooC;
use FooD as D;
use FooE;
use FooF;
use FooG as G;
use FooH;
use FooI;
use FooJ;
use FooZ;

EOF
            ,
                <<<'EOF'
use Some, Not, PHP, Like, Use, Statement;
<?php

use Foo;
use FooA, FooB;
use FooC, FooD as D, FooE;
use FooF,
    FooG as G,
  FooH,     FooI,
        FooJ;
use FooZ;

EOF
            ),
            array(
                <<<'EOF'
<?php

namespace {
    use Foo;
    use FooA;
    use FooB;
    use FooC;
    use FooD as D;
    use FooE;
    use FooF;
    use FooG as G;
    use FooH;
    use FooI;
    use FooJ;
    use FooZ;
}

namespace Boo {
    use Bar;
    use BarA;
    use BarB;
    use BarC;
    use BarD as D;
    use BarE;
    use BarF;
    use BarG as G;
    use BarH;
    use BarI;
    use BarJ;
    use BarZ;
}

EOF
            ,
                <<<'EOF'
<?php

namespace {
    use Foo;
    use FooA, FooB;
    use FooC, FooD as D, FooE;
    use FooF,
        FooG as G,
      FooH,     FooI,
            FooJ;
    use FooZ;
}

namespace Boo {
    use Bar;
    use BarA, BarB;
    use BarC, BarD as D, BarE;
    use BarF,
        BarG as G,
      BarH,     BarI,
            BarJ;
    use BarZ;
}

EOF
            ),
            array(
                '<?php
                    use FooA;
                    use FooB;
                ',
                '<?php
                    use FooA, FooB;
                ',
            ),
            array(
                '<?php use FooA;
use FooB?>',
                '<?php use FooA, FooB?>',
            ),
            array(
                '<?php
use B;
use C;
    use E;
    use F;
        use G;
        use H;
',
                '<?php
use B,C;
    use E,F;
        use G,H;
',
            ),
            array(
                '<?php
use B;
/*
*/use C;
',
                '<?php
use B,
/*
*/C;
',
            ),
            array(
                '<?php
use A;
use B;
//,{} use ; :
#,{} use ; :
/*,{} use ; :*/
use C  ; ',
                '<?php
use A,B,
//,{} use ; :
#,{} use ; :
/*,{} use ; :*/
C  ; ',
            ),
            array(
                '<?php use Z ;
use X ?><?php new X(); // run before white space around semicolon',
                '<?php use Z , X ?><?php new X(); // run before white space around semicolon',
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
use some\a\ClassA;
use some\a\ClassB;
use some\a\ClassC as C;
use function some\b\fn_a;
use function some\b\fn_b;
use function some\b\fn_c;
use const some\c\ConstA/**/as/**/E; /* group comment */
use const some\c\ConstB as D;
use const some\c\// use.,{}
ConstC;
use A\{B};
use D\E;
use D\F;
                ',
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use    function some\b\{fn_a, fn_b, fn_c};
use const/* group comment */some\c\{ConstA/**/as/**/ E   ,    ConstB   AS    D,
// use.,{}
ConstC};
use A\{B};
use D\{E,F};
                ',
            ),
        );
    }
}
