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

use Symfony\CS\Fixer\VisibilityFixer;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 */
class ReplaceFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFixReplaceEqualString()
    {
        $fixer = new ReplaceFixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
class Foo {
    public $var;

    public function methodOne() {
        $this->var = 'expressionWithoutTypo';
    }
}
EOF;

        $input = <<<'EOF'
class Foo {
    public $var;

    public function methodOne() {
        $this->var = 'expressionWithTypo';
    }
}
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }


}
