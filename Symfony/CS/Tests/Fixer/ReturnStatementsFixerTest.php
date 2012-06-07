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

use Symfony\CS\Fixer\ReturnStatementsFixer as Fixer;

class ReturnStatementsFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFixProvider
     */
    public function testFix($return, $returnFixed)
    {
        $fixer = new Fixer();

        $this->assertEquals($returnFixed, $fixer->fix($this->getFileMock(), $return));
        $this->assertEquals($returnFixed, $fixer->fix($this->getFileMock(), $returnFixed));
    }

    public function testFixProvider()
    {
        $return1 = <<<TEST
    \$foo = \$bar;
    return \$foo;
TEST;
        $returnFixed1 = <<<TEST
    \$foo = \$bar;

    return \$foo;
TEST;
        $return2 = <<<TEST
    throw new Exception("MyClass::myMethod(\$param1, \$param2)
            returned: \$status,
            p3=\$p3, p4=\$p4,
            p5=\$p5, style=\$style", ERROR_CODE);
TEST;
        $returnFixed2 = <<<TEST
    throw new Exception("MyClass::myMethod(\$param1, \$param2)
            returned: \$status,
            p3=\$p3, p4=\$p4,
            p5=\$p5, style=\$style", ERROR_CODE);
TEST;

        return array(
            array($return1, $returnFixed1),
            array($return2, $returnFixed2),
        );
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
