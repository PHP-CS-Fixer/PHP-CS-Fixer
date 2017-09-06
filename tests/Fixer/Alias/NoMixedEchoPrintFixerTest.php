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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer
 */
final class NoMixedEchoPrintFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideEchoToPrintFixCases
     */
    public function testFixEchoToPrint($expected, $input = null)
    {
        $this->fixer->configure(array('use' => 'print'));

        $this->doTest($expected, $input);
    }

    public function provideEchoToPrintFixCases()
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
            // `echo` can take multiple parameters (although such usage is rare) while `print` can take only one argument,
            // @see https://secure.php.net/manual/en/function.echo.php and @see https://secure.php.net/manual/en/function.print.php
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
            array(
                '<?=$foo?>',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePrintToEchoFixCases
     */
    public function testFixPrintToEcho($expected, $input = null)
    {
        $this->fixer->configure(array('use' => 'echo'));

        $this->doTest($expected, $input);
    }

    public function providePrintToEchoFixCases()
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

    /**
     * @group legacy
     * @expectedDeprecation Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.
     */
    public function testLegacyDefaultConfig()
    {
        $this->fixer->configure(null);

        $this->assertAttributeSame(T_PRINT, 'candidateTokenType', $this->fixer);
    }

    public function testDefaultConfig()
    {
        $this->fixer->configure(array());

        $this->assertAttributeSame(T_PRINT, 'candidateTokenType', $this->fixer);
    }

    /**
     * @dataProvider provideWrongConfigCases
     *
     * @param mixed  $wrongConfig
     * @param string $expectedMessage
     */
    public function testWrongConfig($wrongConfig, $expectedMessage)
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            $expectedMessage
        );

        $this->fixer->configure($wrongConfig);
    }

    public function provideWrongConfigCases()
    {
        return array(
            array(
                array('a' => 'b'),
                '#^\[no_mixed_echo_print\] Invalid configuration: The option "a" does not exist\. (Known|Defined) options are: "use"\.$#',
            ),
            array(
                array('a' => 'b', 'b' => 'c'),
                '#^\[no_mixed_echo_print\] Invalid configuration: The options "a", "b" do not exist\. (Known|Defined) options are: "use"\.$#',
            ),
            array(
                array(1),
                '#^\[no_mixed_echo_print\] Invalid configuration: The option "0" does not exist\. (Known|Defined) options are: "use"\.$#',
            ),
            array(
                array('use' => '_invalid_'),
                '#^\[no_mixed_echo_print\] Invalid configuration: The option "use" with value "_invalid_" is invalid\. Accepted values are: "print", "echo"\.$#',
            ),
        );
    }
}
