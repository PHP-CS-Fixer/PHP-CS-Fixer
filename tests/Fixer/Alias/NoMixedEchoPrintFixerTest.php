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
            <<<'EOD'
                <?php
                                print "test";
                EOD."\n                ",
            null,
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                print ("test");
                EOD."\n                ",
            null,
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                print("test");
                EOD."\n                ",
            null,
            ['use' => 'print'],
        ];

        // `echo` can take multiple parameters (although such usage is rare) while `print` can take only one argument,
        // @see https://php.net/manual/en/function.echo.php and @see https://php.net/manual/en/function.print.php
        yield [
            <<<'EOD'
                <?php
                                echo "This ", "string ", "was ", "made ", "with multiple parameters.";
                EOD."\n                ",
            null,
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                print "test";
                EOD."\n                ",
            <<<'EOD'
                <?php
                                echo "test";
                EOD."\n                ",
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                print ("test");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                echo ("test");
                EOD."\n                ",
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                print("test");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                echo("test");
                EOD."\n                ",
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                print foo(1, 2);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                echo foo(1, 2);
                EOD."\n                ",
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                print ["foo", "bar", "baz"][$x];
                EOD."\n                ",
            <<<'EOD'
                <?php
                                echo ["foo", "bar", "baz"][$x];
                EOD."\n                ",
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                print $foo ? "foo" : "bar";
                EOD."\n                ",
            <<<'EOD'
                <?php
                                echo $foo ? "foo" : "bar";
                EOD."\n                ",
            ['use' => 'print'],
        ];

        yield [
            "<?php print 'foo' ?>...<?php echo 'bar', 'baz' ?>",
            "<?php echo 'foo' ?>...<?php echo 'bar', 'baz' ?>",
            ['use' => 'print'],
        ];

        yield [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    print "foo";
                                }
                                print "bar";
                EOD."\n                ",
            <<<'EOD'
                <?php
                                if ($foo) {
                                    echo "foo";
                                }
                                echo "bar";
                EOD."\n                ",
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
            <<<'EOD'
                <?php
                                echo "test";
                EOD."\n                ",
            null,
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                echo ("test");
                EOD."\n                ",
            null,
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                echo("test");
                EOD."\n                ",
            null,
            ['use' => 'echo'],
        ];

        // https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/1502#issuecomment-156436229
        yield [
            <<<'EOD'
                <?php
                                ($some_var) ? print "true" : print "false";
                EOD."\n                ",
            null,
            ['use' => 'echo'],
        ];

        // echo has no return value while print has a return value of 1 so it can be used in expressions.
        // https://www.w3schools.com/php/php_echo_print.asp
        yield [
            <<<'EOD'
                <?php
                                $ret = print "test";
                EOD."\n                ",
            null,
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                @print foo();
                EOD."\n                ",
            null,
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                function testFunction() {
                                    return print("test");
                                }

                                $a = testFunction();
                                $b += print($a);
                                $c='';
                                $c .= $b.print($a);
                                $d = print($c) > 0 ? 'a' : 'b';
                                switch(print('a')) {}
                                if (1 === print($a)) {}
                EOD."\n                ",
            null,
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                some_function_call();
                                echo "test";
                EOD."\n                ",
            <<<'EOD'
                <?php
                                some_function_call();
                                print "test";
                EOD."\n                ",
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                echo "test";
                EOD."\n                ",
            <<<'EOD'
                <?php
                                print "test";
                EOD."\n                ",
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                echo ("test");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                print ("test");
                EOD."\n                ",
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                echo("test");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                print("test");
                EOD."\n                ",
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                echo foo(1, 2);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                print foo(1, 2);
                EOD."\n                ",
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                echo $foo ? "foo" : "bar";
                EOD."\n                ",
            <<<'EOD'
                <?php
                                print $foo ? "foo" : "bar";
                EOD."\n                ",
            ['use' => 'echo'],
        ];

        yield [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    echo "foo";
                                }
                                echo "bar";
                EOD."\n                ",
            <<<'EOD'
                <?php
                                if ($foo) {
                                    print "foo";
                                }
                                print "bar";
                EOD."\n                ",
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
     * @param array<mixed> $wrongConfig
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

        yield 'foreach without curly brackets' => <<<'EOD'
            <?php
                        %1$s "There will be foos: ";
                        foreach ($foos as $foo)
                            %1$s $foo;
                        %1$s "End of foos";
            EOD."\n        ";

        yield 'if and else without curly brackets' => <<<'EOD'
            <?php
                        if ($foo)
                            %1$s "One";
                        elseif ($bar)
                            %1$s "Two";
                        else
                            %1$s "Three";
            EOD."\n        ";
    }
}
