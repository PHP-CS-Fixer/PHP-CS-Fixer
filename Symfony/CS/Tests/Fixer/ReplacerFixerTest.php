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

use Symfony\CS\Fixer\ReplacerFixer;

class ReplacerFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFixReplacerEqualString()
    {
        $fixer = new ReplacerFixer();
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
        $options = array('target'=>'expressionWithTypo', 'source'=> 'expressionWithoutTypo');
        $this->assertEquals($expected, $fixer->fixWithOptions($file, $input, $options));
    }
}
