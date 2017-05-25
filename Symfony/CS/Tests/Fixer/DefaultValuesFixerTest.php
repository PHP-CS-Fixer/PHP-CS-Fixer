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

use Symfony\CS\Fixer\DefaultValuesFixer as Fixer;

class DefaultValuesFixerTest extends \PHPUnit_Framework_TestCase
{
    public function data() {
        return array(
            array('function foo($name="var") {', 'function foo($name = "var") {'),
            array('function foo($name="var", $bar=23) {', 'function foo($name = "var", $bar = 23) {'),
            array(
                'function foo($name = "var", $bar=23, $quux=false) {',
                'function foo($name = "var", $bar = 23, $quux = false) {'
            ),
            array(
                'function foo($q, $name = "var", $bar=23, $quux=false) {',
                'function foo($q, $name = "var", $bar = 23, $quux = false) {'
            ),
            array('function baR32($name, $bar=23) {', 'function baR32($name, $bar = 23) {'),
            array('function foo($name = "var") {', 'function foo($name = "var") {'),
            array('function foo ($name=true){', 'function foo ($name = true){'),
            array('function foo($nameFoo= 53) {', 'function foo($nameFoo = 53) {'),
        );
    }

    /**
     * @dataProvider data
     */
    public function testDouble($source, $target)
    {
        $fixer = new Fixer();
        $this->assertSame($target, $fixer->fix($this->getFileMock(), $target));
    }

    /**
     * @dataProvider data
     */
    public function testSimple($source, $target)
    {
        $fixer = new Fixer();
        $this->assertSame($target, $fixer->fix($this->getFileMock(), $source));
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
