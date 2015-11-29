<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 *
 * @internal
 */
final class PrintToEchoFixerTest extends AbstractFixerTestBase
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
                echo "test";
                ',
            ),
            array(
                '<?php
                echo ("test");
                ',
            ),
            array(
                '<?php
                echo("test");
                ',
            ),
            // https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/1502#issuecomment-156436229
            array(
                '<?php
                ($some_var) ? print "true" : print "false";
                ',
            ),
            // echo has no return value while print has a return value of 1 so it can be used in expressions.
            // http://www.w3schools.com/php/php_echo_print.asp
            array(
                '<?php
                $ret = print "test";
                ',
            ),
            array(
                '<?php
                @print foo();
                ',
            ),
            array(
                '<?php
                function testFunction() {
                    return print("test");
                }

                $a = testFunction();
                $b += print($a);
                $c=\'\';
                $c .= $b.print($a);
                $d = print($c) > 0 ? \'a\' : \'b\';
                switch(print(\'a\')) {}
                if (1 === print($a)) {}
                ',
            ),
            array(
                '<?php
                some_function_call();
                echo "test";
                ',
                '<?php
                some_function_call();
                print "test";
                ',
            ),
            array(
                '<?php
                echo "test";
                ',
                '<?php
                print "test";
                ',
            ),
            array(
                '<?php
                echo ("test");
                ',
                '<?php
                print ("test");
                ',
            ),
            array(
                '<?php
                echo("test");
                ',
                '<?php
                print("test");
                ',
            ),
            array(
                '<?php
                echo foo(1, 2);
                ',
                '<?php
                print foo(1, 2);
                ',
            ),
            array(
                '<?php
                echo $foo ? "foo" : "bar";
                ',
                '<?php
                print $foo ? "foo" : "bar";
                ',
            ),
            array(
                '<?php
                if ($foo) {
                    echo "foo";
                }
                echo "bar";
                ',
                '<?php
                if ($foo) {
                    print "foo";
                }
                print "bar";
                ',
            ),
            array(
                "<div><?php echo 'foo' ?></div>",
                "<div><?php print 'foo' ?></div>",
            ),
        );
    }
}
