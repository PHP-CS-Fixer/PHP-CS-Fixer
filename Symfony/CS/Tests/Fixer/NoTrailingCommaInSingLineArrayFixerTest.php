<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\NoTrailingCommaInSingLineArrayFixer as Fixer;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class NoTrailingCommaInSingLineArrayFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = array();', '<?php $x = array();'),
            array('<?php $x = array(());', '<?php $x = array(());'),
            array('<?php $x = array("foo");', '<?php $x = array("foo");'),
            array('<?php $x = array("foo");', '<?php $x = array("foo", );'),
            array("<?php \$x = array(\n'foo', \n);", "<?php \$x = array(\n'foo', \n);"),
            array("<?php \$x = array('foo', \n);", "<?php \$x = array('foo', \n);"),
            array("<?php \$x = array(array('foo'), \n);", "<?php \$x = array(array('foo',), \n);"),
            array("<?php \$x = array(array('foo',\n), \n);", "<?php \$x = array(array('foo',\n), \n);"),

            // Short syntax
            array('<?php $x = array([]);', '<?php $x = array([]);'),
            array('<?php $x = [[]];', '<?php $x = [[]];'),
            array('<?php $x = ["foo"];', '<?php $x = ["foo",];'),
            array('<?php $x = bar(["foo"]);', '<?php $x = bar(["foo",]);'),
            array("<?php \$x = bar(['foo'],\n]);", "<?php \$x = bar(['foo'],\n]);"),
            array("<?php \$x = ['foo', \n];", "<?php \$x = ['foo', \n];"),
            array('<?php $x = array([]);', '<?php $x = array([],);'),
            array('<?php $x = [[]];', '<?php $x = [[],];'),
            array('<?php $x = [$y[]];', '<?php $x = [$y[],];'),
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
