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
        $this->fixer->configure(['use' => 'print']);

        $this->doTest($expected, $input);
    }

    public function provideEchoToPrintFixCases()
    {
        return [
            [
                '<?php
                print "test";
                ',
            ],
            [
                '<?php
                print ("test");
                ',
            ],
            [
                '<?php
                print("test");
                ',
            ],
            // `echo` can take multiple parameters (although such usage is rare) while `print` can take only one argument,
            // @see https://secure.php.net/manual/en/function.echo.php and @see https://secure.php.net/manual/en/function.print.php
            [
                '<?php
                echo "This ", "string ", "was ", "made ", "with multiple parameters.";
                ',
            ],
            [
                '<?php
                print "test";
                ',
                '<?php
                echo "test";
                ',
            ],
            [
                '<?php
                print ("test");
                ',
                '<?php
                echo ("test");
                ',
            ],
            [
                '<?php
                print("test");
                ',
                '<?php
                echo("test");
                ',
            ],
            [
                '<?php
                print foo(1, 2);
                ',
                '<?php
                echo foo(1, 2);
                ',
            ],
            [
                '<?php
                print ["foo", "bar", "baz"][$x];
                ',
                '<?php
                echo ["foo", "bar", "baz"][$x];
                ',
            ],
            [
                '<?php
                print $foo ? "foo" : "bar";
                ',
                '<?php
                echo $foo ? "foo" : "bar";
                ',
            ],
            [
                "<?php print 'foo' ?>...<?php echo 'bar', 'baz' ?>",
                "<?php echo 'foo' ?>...<?php echo 'bar', 'baz' ?>",
            ],
            [
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
            ],
            [
                "<div><?php print 'foo' ?></div>",
                "<div><?php echo 'foo' ?></div>",
            ],
            [
                '<?=$foo?>',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePrintToEchoFixCases
     */
    public function testFixPrintToEcho($expected, $input = null)
    {
        $this->fixer->configure(['use' => 'echo']);

        $this->doTest($expected, $input);
    }

    public function providePrintToEchoFixCases()
    {
        return [
            [
                '<?php
                echo "test";
                ',
            ],
            [
                '<?php
                echo ("test");
                ',
            ],
            [
                '<?php
                echo("test");
                ',
            ],
            // https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/1502#issuecomment-156436229
            [
                '<?php
                ($some_var) ? print "true" : print "false";
                ',
            ],
            // echo has no return value while print has a return value of 1 so it can be used in expressions.
            // http://www.w3schools.com/php/php_echo_print.asp
            [
                '<?php
                $ret = print "test";
                ',
            ],
            [
                '<?php
                @print foo();
                ',
            ],
            [
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
            ],
            [
                '<?php
                some_function_call();
                echo "test";
                ',
                '<?php
                some_function_call();
                print "test";
                ',
            ],
            [
                '<?php
                echo "test";
                ',
                '<?php
                print "test";
                ',
            ],
            [
                '<?php
                echo ("test");
                ',
                '<?php
                print ("test");
                ',
            ],
            [
                '<?php
                echo("test");
                ',
                '<?php
                print("test");
                ',
            ],
            [
                '<?php
                echo foo(1, 2);
                ',
                '<?php
                print foo(1, 2);
                ',
            ],
            [
                '<?php
                echo $foo ? "foo" : "bar";
                ',
                '<?php
                print $foo ? "foo" : "bar";
                ',
            ],
            [
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
            ],
            [
                "<div><?php echo 'foo' ?></div>",
                "<div><?php print 'foo' ?></div>",
            ],
        ];
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
        $this->fixer->configure([]);

        $this->assertAttributeSame(T_PRINT, 'candidateTokenType', $this->fixer);
    }

    /**
     * @dataProvider provideWrongConfig
     *
     * @param mixed  $wrongConfig
     * @param string $expectedMessage
     */
    public function testWrongConfig($wrongConfig, $expectedMessage)
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            $expectedMessage
        );

        $this->fixer->configure($wrongConfig);
    }

    public function provideWrongConfig()
    {
        return [
            [
                ['a' => 'b'],
                '#^\[no_mixed_echo_print\] Invalid configuration: The option "a" does not exist\. (Known|Defined) options are: "use"\.$#',
            ],
            [
                ['a' => 'b', 'b' => 'c'],
                '#^\[no_mixed_echo_print\] Invalid configuration: The options "a", "b" do not exist\. (Known|Defined) options are: "use"\.$#',
            ],
            [
                [1],
                '#^\[no_mixed_echo_print\] Invalid configuration: The option "0" does not exist\. (Known|Defined) options are: "use"\.$#',
            ],
            [
                ['use' => '_invalid_'],
                '#^\[no_mixed_echo_print\] Invalid configuration: The option "use" with value "_invalid_" is invalid\. Accepted values are: "print", "echo"\.$#',
            ],
        ];
    }
}
