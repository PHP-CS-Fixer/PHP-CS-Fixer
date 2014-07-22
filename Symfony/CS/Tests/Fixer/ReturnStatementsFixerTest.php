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

        $this->assertEquals($returnFixed, $fixer->fix($this->getTestFile(), $return));
        $this->assertEquals($returnFixed, $fixer->fix($this->getTestFile(), $returnFixed));
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

        $return3 = <<<TEST
    \$foo = \$bar;
    return;
TEST;
        $returnFixed3 = <<<TEST
    \$foo = \$bar;

    return;
TEST;

        $return4 = <<<TEST
    if (\$foo === \$bar)
        return;
TEST;
        $returnFixed4 = <<<TEST
    if (\$foo === \$bar)
        return;
TEST;

        $return5 = <<<TEST
    else
        return;
TEST;
        $returnFixed5 = <<<TEST
    else
        return;
TEST;

        $return6 = <<<TEST
    elseif (\$foo === \$bar)
        return;
TEST;
        $returnFixed6 = <<<TEST
    elseif (\$foo === \$bar)
        return;
TEST;

        $return7 = <<<TEST
    if (\$foo === \$bar)





        return;
TEST;
        $returnFixed7 = <<<TEST
    if (\$foo === \$bar)
        return;
TEST;

        $return8 = <<<TEST
    \$foo = \$bar;







    return \$foo;
TEST;
        $returnFixed8 = <<<TEST
    \$foo = \$bar;

    return \$foo;
TEST;

        return array(
            array($return1, $returnFixed1),
            array($return2, $returnFixed2),
            array($return3, $returnFixed3),
            array($return4, $returnFixed4),
            array($return5, $returnFixed5),
            array($return6, $returnFixed6),
            array($return7, $returnFixed7),
            array($return8, $returnFixed8),
        );
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
