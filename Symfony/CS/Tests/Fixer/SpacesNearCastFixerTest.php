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

        $this->assertEquals($castFixed, $fixer->fix($this->getTestFile(), $cast));
        $this->assertEquals($castFixed, $fixer->fix($this->getTestFile(), $castFixed));
    }

    public function testFixCastsProvider()
    {
        return array(
            array('( int)$foo', '(int) $foo'),
            array('( string )( int )$foo', '(string) (int) $foo'),
            array('(string)(int)$foo', '(string) (int) $foo'),
            array('( string   )    (   int )$foo', '(string) (int) $foo'),
            array('( string )   $foo', '(string) $foo'),
            array('(float )Foo::bar()', '(float) Foo::bar()'),
            array('Foo::baz((float )Foo::bar())', 'Foo::baz((float) Foo::bar())'),
            array('$query["params"] = (array)$query["params"]', '$query["params"] = (array) $query["params"]'),
            array("(int)\n    *", "(int)\n    *"),
        );
    }

    private function getTestFile()
    {
        return new \SplFileInfo(__FILE__);
    }
}
