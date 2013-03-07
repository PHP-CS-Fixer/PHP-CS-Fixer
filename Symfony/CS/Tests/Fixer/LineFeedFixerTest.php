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

use Symfony\CS\Fixer\LineFeedFixer;

class LineFeedFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSampleData
     */
    public function testFix($input, $expected)
    {
        $fixer = new LineFeedFixer();
        $file = new \SplFileInfo(__FILE__);

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function getSampleData()
    {
        return array(
            array("echo 'Foo';\n", "echo 'Foo';\n"),
            array("echo 'Foo';\r", "echo 'Foo';\n"),
            array("echo 'Foo';\r\n", "echo 'Foo';\n"),
            array("echo 'Foo';\r\n\r", "echo 'Foo';\n\n"),
            array("echo 'Foo';\r\r\n", "echo 'Foo';\n\n"),
        );
    }
}
