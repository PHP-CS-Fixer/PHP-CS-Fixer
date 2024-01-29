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
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, null|string, array{use: string}}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
                print "test";
                ',
            null,
            ['use' => 'print'],
        ];

        yield [
            '<?php
                print ("test");
                ',
            null,
            ['use' => 'print'],
        ];

        yield [
            '<?php
                print("test");
                ',
            null,
            ['use' => 'print'],
        ];

        // `echo` can take multiple parameters (although such usage is rare) while `print` can take only one argument,
        // @see https://php.net/manual/en/function.echo.php and @see https://php.net/manual/en/function.print.php
        yield [
            '<?php
                echo "This ", "string ", "was ", "made ", "with multiple parameters.";
                ',
            null,
            ['use' => 'print'],
        ];

        yield [
            '<?php
                print "test";
                ',
            '<?php
                echo "test";
                ',
            ['use' => 'print'],
        ];

        yield [
            '<?php
                print ("test");
                ',
            '<?php
                echo ("test");
                ',
            ['use' => 'print'],
        ];

        yield [
            '<?php
                print("test");
                ',
            '<?php
                echo("test");
                ',
            ['use' => 'print'],
        ];

        yield [
            '<?php
                print foo(1, 2);
                ',
            '<?php
                echo foo(1, 2);
                ',
            ['use' => 'print'],
        ];

        yield [
            '<?php
                print ["foo", "bar", "baz"][$x];
                ',
            '<?php
                echo ["foo", "bar", "baz"][$x];
                ',
            ['use' => 'print'],
        ];

        yield [
            '<?php
                print $foo ? "foo" : "bar";
                ',
            '<?php
                echo $foo ? "foo" : "bar";
                ',
            ['use' => 'print'],
        ];

        yield [
            "<?php print 'foo' ?>...<?php echo 'bar', 'baz' ?>",
            "<?php echo 'foo' ?>...<?php echo 'bar', 'baz' ?>",
            ['use' => 'print'],
        ];

        yield [
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
            ['use' => 'print'],
        ];

        yield [
            '<?=$foo?>',
            null,
            ['use' => 'print'],
        ];

        foreach (self::getCodeSnippetsToConvertBothWays() as $codeSnippet) {
            yield [
                sprintf($codeSnippet, 'print'),
                sprintf($codeSnippet, 'echo'),
                ['use' => 'print'],
            ];
        }

        yield [
            '<?php
                echo "test";
                ',
            null,
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                echo ("test");
                ',
            null,
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                echo("test");
                ',
            null,
            ['use' => 'echo'],
        ];

        // https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/1502#issuecomment-156436229
        yield [
            '<?php
                ($some_var) ? print "true" : print "false";
                ',
            null,
            ['use' => 'echo'],
        ];

        // echo has no return value while print has a return value of 1 so it can be used in expressions.
        // https://www.w3schools.com/php/php_echo_print.asp
        yield [
            '<?php
                $ret = print "test";
                ',
            null,
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                @print foo();
                ',
            null,
            ['use' => 'echo'],
        ];

        yield [
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
            null,
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                some_function_call();
                echo "test";
                ',
            '<?php
                some_function_call();
                print "test";
                ',
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                echo "test";
                ',
            '<?php
                print "test";
                ',
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                echo ("test");
                ',
            '<?php
                print ("test");
                ',
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                echo("test");
                ',
            '<?php
                print("test");
                ',
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                echo foo(1, 2);
                ',
            '<?php
                print foo(1, 2);
                ',
            ['use' => 'echo'],
        ];

        yield [
            '<?php
                echo $foo ? "foo" : "bar";
                ',
            '<?php
                print $foo ? "foo" : "bar";
                ',
            ['use' => 'echo'],
        ];

        yield [
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
            ['use' => 'echo'],
        ];

        foreach (self::getCodeSnippetsToConvertBothWays() as $codeSnippet) {
            yield [
                sprintf($codeSnippet, 'echo'),
                sprintf($codeSnippet, 'print'),
                ['use' => 'echo'],
            ];
        }
    }

    public function testConfigure(): void
    {
        $this->fixer->configure([]);

        self::assertCandidateTokenType(T_PRINT, $this->fixer);
    }

    /**
     * @param array<string, mixed> $wrongConfig
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfiguration(array $wrongConfig, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        $this->fixer->configure($wrongConfig);
    }

    public static function provideInvalidConfigurationCases(): iterable
    {
        yield [
            ['a' => 'b'],
            '#^\[no_mixed_echo_print\] Invalid configuration: The option "a" does not exist\. (Known|Defined) options are: "use"\.$#',
        ];

        yield [
            ['a' => 'b', 'b' => 'c'],
            '#^\[no_mixed_echo_print\] Invalid configuration: The options "a", "b" do not exist\. (Known|Defined) options are: "use"\.$#',
        ];

        yield [
            [1],
            '#^\[no_mixed_echo_print\] Invalid configuration: The option "0" does not exist\. (Known|Defined) options are: "use"\.$#',
        ];

        yield [
            ['use' => '_invalid_'],
            '#^\[no_mixed_echo_print\] Invalid configuration: The option "use" with value "_invalid_" is invalid\. Accepted values are: "print", "echo"\.$#',
        ];
    }

    private static function assertCandidateTokenType(int $expected, AbstractFixer $fixer): void
    {
        $reflectionProperty = new \ReflectionProperty($fixer, 'candidateTokenType');
        $reflectionProperty->setAccessible(true);

        self::assertSame($expected, $reflectionProperty->getValue($fixer));
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
