<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\NewWithBracesFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class NewWithBracesFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
        $this->assertSame($expected, $fixer->fix($file, $expected));
    }

    public function provideCases()
    {
        return array(
            array('<?php $x = new X();', '<?php $x = new X;', ),
            array('<?php $y = new Y ();', '<?php $y = new Y ;', ),
            array('<?php $foo = new $foo();', '<?php $foo = new $foo;', ),
            array('<?php $baz = new {$bar->baz}();', '<?php $baz = new {$bar->baz};', ),
            array('<?php $xyz = new X(new Y(new Z()));', '<?php $xyz = new X(new Y(new Z));', ),
            array('<?php $foo = (new $bar())->foo;', '<?php $foo = (new $bar)->foo;', ),
            array('<?php $self = new self();', '<?php $self = new self;', ),
            array('<?php $static = new static();', '<?php $static = new static;', ),
            array('<?php $magic = new __CLASS__();', '<?php $magic = new __CLASS__;'),
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
