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

namespace PhpCsFixer\Tests\Fixer\NamespaceNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer
 */
final class BlankLineAfterNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php namespace A\B?>
                <?php
                    for($i=0; $i<10; ++$i) {echo $i;}',
            ],
            [
                '<?php namespace A\B?>',
            ],
            [
                '<?php
namespace A\B;

class C {}
',
                '<?php
namespace A\B;



class C {}
',
            ],
            [
                '<?php
namespace A\B;

class C {}
',
                '<?php
namespace A\B;
class C {}
',
            ],
            [
                '<?php
namespace A\B;

class C {}
',
                '<?php
namespace A\B;  class C {}
',
            ],
            [
                '<?php
namespace A\B;

class C {}
',
                '<?php
namespace A\B;class C {}
',
            ],
            [
                '<?php
namespace A\B {
    class C {
        public $foo;
        private $bar;
    }
}
',
            ],
            [
                "<?php\rnamespace A\B;

class C {}\r",
                "<?php\rnamespace A\B;\r\r\r\r\r\rclass C {}\r",
            ],
            [
                '<?php
namespace A\B;

namespace\C\func();
foo();
',
            ],
            [
                '<?php
namespace Foo;
',
                '<?php
namespace Foo;



',
            ],
            [
                '<?php
namespace Foo;

?>',
                '<?php
namespace Foo;



?>',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php namespace A\B;\r\n\r\nclass C {}",
                "<?php namespace A\B;  class C {}",
            ],
            [
                "<?php namespace A\B;\r\n\r\nclass C {}",
                "<?php namespace A\B;\r\n\r\n\r\n\r\n\r\n\r\nclass C {}",
            ],
        ];
    }
}
