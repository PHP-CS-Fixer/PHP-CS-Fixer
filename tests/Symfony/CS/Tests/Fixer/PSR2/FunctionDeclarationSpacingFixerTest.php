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

use Symfony\CS\Fixer\PSR2\FunctionDeclarationSpacingFixer as Fixer;

class FunctionDeclarationSpacingFixerTest extends \PHPUnit_Framework_TestCase
{
    public function data()
    {
        return array(
            array("function\tfoo () {", 'function foo() {'),
            array("function foo\t() {", 'function foo() {'),
            array("function\nfoo () {", 'function foo() {'),
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
    public function testFix($source, $target)
    {
        $fixer = new Fixer();
        $testFile = $this->getTestFile();
        $this->assertSame($target, $fixer->fix($testFile, $source));
        $this->assertSame($target, $fixer->fix($testFile, $target));
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
