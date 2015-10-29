<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Loïck Piera <pyrech@gmail.com>
 */
class LineBetweenMethodsFixerTest extends AbstractFixerTestBase
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
class A {
    public function b()
    {
    }
}
class C {
    public function d()
    {
    }
}
'
            ),
            array(
                '<?php
class A {
    public function b()
    {
    }

    public function c()
    {
    }
}
',
                '<?php
class A {
    public function b()
    {
    }
    public function c()
    {
    }
}
'
            ),
            array(
                '<?php
class A {
    public function b()
    {
    }

    /** @return void */
    public function c()
    {
    }
}
',
                '<?php
class A {
    public function b()
    {
    }
    /** @return void */
    public function c()
    {
    }
}
'
            ),
            array(
                '<?php
class A {
    public function b()
    {
    }

    function c()
    {
    }
}
',
                '<?php
class A {
    public function b()
    {
    }
    function c()
    {
    }
}
'
            ),
            array(
                '<?php
class A {
    public function b()
    {
    }

    function c()
    {
    }
}
',
                '<?php
class A {
    public function b()
    {
    }function c()
    {
    }
}
'
            ),
            array(
                '<?php
class A {
    public function b()
    {
    }

    public function c()
    {
    }
}
',
                '<?php
class A {
    public function b()
    {
    }public function c()
    {
    }
}
'
            ),
            array(
                '<?php
class A {
    public function b()
    {
    }

    /** @return void */
    public function c()
    {
    }
}
'           ),
            array(
                '<?php
class A {
    public function b()
    {
    }

    /** @return void */
    public function c()
    {
    }
}
',
                '<?php
class A {
    public function b()
    {
    }


    /** @return void */
    public function c()
    {
    }
}
'
            ),
            array(
                '<?php
class A {
    public function b()
    {
        $c = function() {
        };
    }
}
'
            ),
            array(
                '<?php
class A {
    public function b()
    {
        if(true) {
            echo "";
        }
        $c = function() {
        };
    }
}
'
            ),
        );
    }
}
