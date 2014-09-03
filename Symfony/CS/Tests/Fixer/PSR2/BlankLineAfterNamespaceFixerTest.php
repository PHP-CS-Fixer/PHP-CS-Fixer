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

use Symfony\CS\Fixer\PSR2\BlankLineAfterNamespaceFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class BlankLineAfterNamespaceFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
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
                '<?php
namespace A\B {
    class C {
        public $foo;
        private $bar;
    }
}
',
            ),
        );
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
