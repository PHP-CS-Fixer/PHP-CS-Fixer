<?php

declare(strict_types=1);

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

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer
 */
final class NoMixedEchoPrintFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideEchoToPrintFixCases
     * @dataProvider provideEchoToPrintFixNewCases
     */
    public function testFixEchoToPrint(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['use' => 'print']);
        $this->doTest($expected, $input);
    }

    public static function provideEchoToPrintFixCases(): array
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
            // @see https://php.net/manual/en/function.echo.php and @see https://php.net/manual/en/function.print.php
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
                '<?=$foo?>',
            ],
        ];
    }

    public static function provideEchoToPrintFixNewCases(): iterable
    {
        foreach (self::getCodeSnippetsToConvertBothWays() as $codeSnippet) {
            yield [
                sprintf($codeSnippet, 'print'),
                sprintf($codeSnippet, 'echo'),
            ];
        }
    }

    /**
     * @dataProvider providePrintToEchoFixCases
     * @dataProvider providePrintToEchoFixNewCases
     */
    public function testFixPrintToEcho(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['use' => 'echo']);
        $this->doTest($expected, $input);
    }

    public static function providePrintToEchoFixCases(): array
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
            // https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/1502#issuecomment-156436229
            [
                '<?php
                ($some_var) ? print "true" : print "false";
                ',
            ],
            // echo has no return value while print has a return value of 1 so it can be used in expressions.
            // https://www.w3schools.com/php/php_echo_print.asp
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
        ];
    }

    public static function providePrintToEchoFixNewCases(): iterable
    {
        foreach (self::getCodeSnippetsToConvertBothWays() as $codeSnippet) {
            yield [
                sprintf($codeSnippet, 'echo'),
                sprintf($codeSnippet, 'print'),
            ];
        }
    }

    public function testDefaultConfig(): void
    {
        $this->fixer->configure([]);

        static::assertCandidateTokenType(T_PRINT, $this->fixer);
    }

    /**
     * @param array<mixed> $wrongConfig
     *
     * @dataProvider provideWrongConfigCases
     */
    public function testWrongConfig(array $wrongConfig, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        $this->fixer->configure($wrongConfig);
    }

    public static function provideWrongConfigCases(): array
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

    private static function assertCandidateTokenType(int $expected, AbstractFixer $fixer): void
    {
        $reflectionProperty = new \ReflectionProperty($fixer, 'candidateTokenType');
        $reflectionProperty->setAccessible(true);

        static::assertSame($expected, $reflectionProperty->getValue($fixer));
    }

    /**
     * @return iterable<non-empty-string>
     */
    private static function getCodeSnippetsToConvertBothWays(): iterable
    {
        yield 'inside of HTML' => '<div><?php %1$s "foo" ?></div>';

        yield 'foreach without curly brackets' => '<?php
            %1$s "There will be foos: ";
            foreach ($foos as $foo)
                %1$s $foo;
            %1$s "End of foos";
        ';

        yield 'if and else without curly brackets' => '<?php
            if ($foo)
                %1$s "One";
            elseif ($bar)
                %1$s "Two";
            else
                %1$s "Three";
        ';
    }
}
