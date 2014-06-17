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

use Symfony\CS\Fixer\FunctionDeclarationSpacingFixer as Fixer;

class FunctionDeclarationSpacingFixerTest extends \PHPUnit_Framework_TestCase
{
    public function data()
    {
        return array(
            array('function foo () {', 'function foo() {'),
            array('function foo($a, $b = true){', 'function foo($a, $b = true) {'),
            array('function($i) {', 'function ($i) {'),
            array('function($a)use($b) {', 'function ($a) use ($b) {'),
            array('function foo( $a ) {', 'function foo($a) {'),
            array('function ( $a) use ( $b ) {', 'function ($a) use ($b) {'),
            array("function foo(\$a)\n{", "function foo(\$a)\n{"),
            array("function foo( \$a)\n{", "function foo(\$a)\n{"),
            array("function foo( \$a)\t\n\t{", "function foo(\$a)\n\t{"),
            array("function foo(\n\$a\n) {", "function foo(\n\$a\n) {"),
            array("function _function () {", "function _function() {"),
            array("\$function = function(){", "\$function = function () {"),
            array("\$function('');", "\$function('');"),
        );
    }

    /**
     * @dataProvider data
     */
    public function testDouble($source, $target)
    {
        $fixer = new Fixer();
        $this->assertSame($target, $fixer->fix($this->getTestFile(), $target));
    }

    /**
     * @dataProvider data
     */
    public function testSimple($source, $target)
    {
        $fixer = new Fixer();
        $this->assertSame($target, $fixer->fix($this->getTestFile(), $source));
    }

    private function getTestFile()
    {
        return new \SplFileInfo(__FILE__);
    }
}
