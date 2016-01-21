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

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class BlankLineAfterNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
namespace A\B;

class C {}
',
                '<?php
namespace A\B;



class C {}
',
            ),
            array(
                '<?php
namespace A\B;

class C {}
',
            ),
            array(
                '<?php
namespace A\B;

class C {}
',
                '<?php
namespace A\B;
class C {}
',
            ),
            array(
                '<?php
namespace A\B;

class C {}
',
                '<?php
namespace A\B;  class C {}
',
            ),
            array(
                '<?php
namespace A\B;

class C {}
',
                '<?php
namespace A\B;class C {}
',
            ),
            array(
                '<?php
namespace A\B {
    class C {
        public $foo;
        private $bar;
    }
}
',
            ),
            array(
                "<?php\rnamespace A\B;

class C {}\r",
                "<?php\rnamespace A\B;\r\r\r\r\r\rclass C {}\r",
            ),
            array(
                '<?php
namespace A\B;

namespace\C\func();
foo();
',
            ),
        );
    }
}
