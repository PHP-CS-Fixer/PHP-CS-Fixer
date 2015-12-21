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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
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
        );
    }
}
