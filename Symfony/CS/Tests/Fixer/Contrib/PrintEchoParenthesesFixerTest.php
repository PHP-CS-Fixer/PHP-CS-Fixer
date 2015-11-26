<?php

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 *
 * @internal
 */
final class PrintEchoParenthesesFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                echo "foo";
                print "foo";
                ',
            ),
            array(
                '<?php
                echo (1 + 2) . $foo;
                print (1 + 2) . $foo;
                ',
            ),
            array(
                '<?php
                echo (1 + 2) * 10, "\n";
                ',
            ),
            array(
                '<?php echo (1 + 2) * 10, "\n" ?>',
            ),
            array(
                '<?php
                echo (1 + 2) * 10, "\n";
                ',
                '<?php
                echo ((1 + 2) * 10, "\n");
                ',
            ),
            array(
                '<?php
                echo(1 + 2) * 10, "\n";
                ',
                '<?php
                echo((1 + 2) * 10, "\n");
                ',
            ),
            array(
                '<?php echo "foo" ?>',
                '<?php echo ("foo") ?>',
            ),
            array(
                '<?php print "foo" ?>',
                '<?php print ("foo") ?>',
            ),
            array(
                '<?php
                echo "foo";
                print "foo";
                ',
                '<?php
                echo ("foo");
                print ("foo");
                ',
            ),
            array(
                '<?php
                echo"foo";
                print"foo";
                ',
                '<?php
                echo("foo");
                print("foo");
                ',
            ),
            array(
                '<?php
                echo $a ? $b : $c;
                echo ($a ? $b : $c) ? $d : $e;
                echo 10 * (2 + 3);
                echo ("foo"), ("bar");
                echo my_awesome_function("foo");
                echo $this->getOutput(1);
                ',
                '<?php
                echo ($a ? $b : $c);
                echo ($a ? $b : $c) ? $d : $e;
                echo 10 * (2 + 3);
                echo ("foo"), ("bar");
                echo my_awesome_function("foo");
                echo $this->getOutput(1);
                ',
            ),
        );
    }
}
