<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Contrib;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 *
 * @internal
 */
final class EchoToPrintFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                print "test";
                ',
            ),
            array(
                '<?php
                print ("test");
                ',
            ),
            array(
                '<?php
                print("test");
                ',
            ),
            // echo can take multiple parameters (although such usage is rare) while print can take one argument
            // http://www.w3schools.com/php/php_echo_print.asp
            array(
                '<?php
                echo "This ", "string ", "was ", "made ", "with multiple parameters.";
                ',
            ),
            array(
                '<?php
                print "test";
                ',
                '<?php
                echo "test";
                ',
            ),
            array(
                '<?php
                print ("test");
                ',
                '<?php
                echo ("test");
                ',
            ),
            array(
                '<?php
                print("test");
                ',
                '<?php
                echo("test");
                ',
            ),
            array(
                '<?php
                print foo(1, 2);
                ',
                '<?php
                echo foo(1, 2);
                ',
            ),
            array(
                '<?php
                print ["foo", "bar", "baz"][$x];
                ',
                '<?php
                echo ["foo", "bar", "baz"][$x];
                ',
            ),
            array(
                '<?php
                print $foo ? "foo" : "bar";
                ',
                '<?php
                echo $foo ? "foo" : "bar";
                ',
            ),
            array(
                "<?php print 'foo' ?>...<?php echo 'bar', 'baz' ?>",
                "<?php echo 'foo' ?>...<?php echo 'bar', 'baz' ?>",
            ),
            array(
                '<?php
                if ($foo) {
                    print "foo";
                }
                print "bar";
                ',
                '<?php
                if ($foo) {
                    echo "foo";
                }
                echo "bar";
                ',
            ),
            array(
                "<div><?php print 'foo' ?></div>",
                "<div><?php echo 'foo' ?></div>",
            ),
        );
    }
}
