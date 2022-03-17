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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractPhpdocTypesFixer
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer
 */
final class PhpdocTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function testWindowsLinebreaks(): void
    {
        $this->doTest(
            "<?php /**\r\n * @param string|string[] \$bar\r\n *\r\n * @return int[]\r\n */\r\n",
            "<?php /**\r\n * @param STRING|String[] \$bar\r\n *\r\n * @return inT[]\r\n */\r\n"
        );
    }

    public function testConversion(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param boolean|array|Foo $bar
     *
     * @return int|float
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Boolean|Array|Foo $bar
     *
     * @return inT|Float
     */

EOF;
        $this->doTest($expected, $input);
    }

    public function testArrayStuff(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param string|string[] $bar
     *
     * @return int[]
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param STRING|String[] $bar
     *
     * @return inT[]
     */

EOF;
        $this->doTest($expected, $input);
    }

    public function testNestedArrayStuff(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return int[][][]
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return INT[][][]
     */
EOF;
        $this->doTest($expected, $input);
    }

    public function testMixedAndVoid(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param mixed $foo
     *
     * @return void
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Mixed $foo
     *
     * @return Void
     */

EOF;
        $this->doTest($expected, $input);
    }

    public function testIterableFix(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param iterable $foo
     *
     * @return Itter
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Iterable $foo
     *
     * @return Itter
     */

EOF;
        $this->doTest($expected, $input);
    }

    public function testMethodAndPropertyFix(): void
    {
        $expected = <<<'EOF'
<?php
/**
 * @method self foo()
 * @property int $foo
 * @property-read boolean $bar
 * @property-write mixed $baz
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * @method Self foo()
 * @property Int $foo
 * @property-read Boolean $bar
 * @property-write MIXED $baz
 */

EOF;

        $this->doTest($expected, $input);
    }

    public function testThrows(): void
    {
        $expected = <<<'EOF'
<?php
/**
 * @throws static
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * @throws STATIC
 */

EOF;

        $this->doTest($expected, $input);
    }

    public function testInlineDoc(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * Does stuff with stuffs.
     *
     * @param array $stuffs {
     *     @var bool $foo
     *     @var int  $bar
     * }
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Does stuff with stuffs.
     *
     * @param array $stuffs {
     *     @var Bool $foo
     *     @var INT  $bar
     * }
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testWithConfig(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param self|array|Foo $bar
     *
     * @return int|float|callback
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param SELF|Array|Foo $bar
     *
     * @return inT|Float|callback
     */

EOF;

        $this->fixer->configure(['groups' => ['simple', 'meta']]);
        $this->doTest($expected, $input);
    }

    public function testGenerics(): void
    {
        $this->fixer->configure(['groups' => ['simple', 'meta']]);
        $this->doTest(
            '<?php
            /**
             * @param array<int, object> $a
             * @param array<iterable> $b
             * @param array<parent|$this|self> $c
             * @param array<\int, \object> $d
             * @param iterable<Foo\Int\Bar|Foo\Int|Int\Bar> $thisShouldNotBeChanged
             * @param iterable<BOOLBOOLBOOL|INTINTINT|ARRAY_BOOL_INT_STRING_> $thisShouldNotBeChangedNeither
             *
             * @return array<int, array<string, array<int, DoNotChangeThisAsThisIsAClass>>>
             */',
            '<?php
            /**
             * @param ARRAY<INT, OBJECT> $a
             * @param ARRAY<ITERABLE> $b
             * @param array<Parent|$This|Self> $c
             * @param ARRAY<\INT, \OBJECT> $d
             * @param iterable<Foo\Int\Bar|Foo\Int|Int\Bar> $thisShouldNotBeChanged
             * @param iterable<BOOLBOOLBOOL|INTINTINT|ARRAY_BOOL_INT_STRING_> $thisShouldNotBeChangedNeither
             *
             * @return ARRAY<INT, ARRAY<STRING, ARRAY<INT, DoNotChangeThisAsThisIsAClass>>>
             */'
        );
    }

    public static function provideFixCases(): iterable
    {
        yield 'callable' => [
            '<?php /**
                    * @param callable() $a
                    * @param callable(): void $b
                    * @param callable(bool, int, string): float $c
                    */',
            '<?php /**
                    * @param CALLABLE() $a
                    * @param Callable(): VOID $b
                    * @param CALLABLE(BOOL, INT, STRING): FLOAT $c
                    */',
        ];
    }

    public function testWrongConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[phpdoc_types\] Invalid configuration: The option "groups" .*\.$/');

        $this->fixer->configure(['groups' => ['__TEST__']]);
    }
}
