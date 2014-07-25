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

use Symfony\CS\Fixer\SpacesNearCastFixer as Fixer;

class SpacesNearCastFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFixCastsProvider
     */
    public function testFixCasts($cast, $castFixed)
    {
        $fixer = new Fixer();

        $this->assertSame($castFixed, $fixer->fix($this->getTestFile(), $cast));
        $this->assertSame($castFixed, $fixer->fix($this->getTestFile(), $castFixed));
    }

    public function testFixCastsProvider()
    {
        return array(
            array('<?php echo "( int ) $foo";', '<?php echo "( int ) $foo";'),
            array('<?php $bar = ( int)$foo;', '<?php $bar = (int) $foo;'),
            array('<?php $bar = (	int)$foo;', '<?php $bar = (int) $foo;'),
            array('<?php $bar = (int)	$foo;', '<?php $bar = (int) $foo;'),
            array('<?php $bar = ( string )( int )$foo;', '<?php $bar = (string) (int) $foo;'),
            array('<?php $bar = (string)(int)$foo;', '<?php $bar = (string) (int) $foo;'),
            array('<?php $bar = ( string   )    (   int )$foo;', '<?php $bar = (string) (int) $foo;'),
            array('<?php $bar = ( string )   $foo;', '<?php $bar = (string) $foo;'),
            array('<?php $bar = (float )Foo::bar();', '<?php $bar = (float) Foo::bar();'),
            array('<?php $bar = Foo::baz((float )Foo::bar());', '<?php $bar = Foo::baz((float) Foo::bar());'),
            array('<?php $bar = $query["params"] = (array)$query["params"];', '<?php $bar = $query["params"] = (array) $query["params"];'),
            array("<?php \$bar = (int)\n \$foo;", "<?php \$bar = (int)\n \$foo;"),
        );
    }

    private function getTestFile()
    {
        return new \SplFileInfo(__FILE__);
    }
}
