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

use Symfony\CS\Fixer\ObjectOperatorFixer as Fixer;

/**
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
class ObjectOperatorFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFixObjectOperatorSpaces
     */
    public function testFixControlsWithParenthesesAndSuffixBrace($toBeFixed, $expected)
    {
        $fixer = new Fixer();

        $this->assertEquals($expected, $fixer->fix($this->getTestFile(), $toBeFixed));
    }

    public function testFixObjectOperatorSpaces()
    {
        return array(
            array('object ->method', 'object->method'),
            array('object -> method', 'object->method'),
            array('object-> method', 'object->method'),
            array('object->method', 'object->method'),
            // Ensure that doesn't break chained multi-line statements
            array('object->method()
                        ->method2()
                        ->method3()',
                    'object->method()
                        ->method2()
                        ->method3()'),
        );
    }

    private function getTestFile()
    {
        return new \SplFileInfo(__FILE__);
    }
}
