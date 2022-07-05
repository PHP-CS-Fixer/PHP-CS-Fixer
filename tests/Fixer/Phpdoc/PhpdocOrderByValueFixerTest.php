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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer
 */
final class PhpdocOrderByValueFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideInvalidAnnotationCases
     *
     * @param mixed $annotation
     */
    public function testConfigureRejectsInvalidControlStatement($annotation): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure([
            'annotations' => [
                $annotation,
            ],
        ]);
    }

    public function provideInvalidAnnotationCases(): array
    {
        return [
            'null' => [null],
            'false' => [false],
            'true' => [true],
            'int' => [0],
            'float' => [3.14],
            'array' => [[]],
            'object' => [new \stdClass()],
            'unknown' => ['foo'],
        ];
    }

    /**
     * @dataProvider provideFixWithCoversCases
     */
    public function testFixWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixWithAuthorCases
     */
    public function testFixWithAuthor(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'author',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAuthorCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @author Bob
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @author Alice
                     * @author Bob
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @author Bob
                     * @author Alice
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @author Alice
                     * Comment 3
                     * @author Bob
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @author Bob
                     * Comment 2
                     * @author Alice
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @author Alice
                     * @author chris
                     * @author Daniel
                     * @author Erna
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @author Alice
                     * @author Erna
                     * @author chris
                     * @author Daniel
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @author Alice
                         * @dataProvider provide
                         * @author Bob
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @author Bob
                         * @dataProvider provide
                         * @author Alice
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithCoversCases
     */
    public function testFixWithCovers(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'covers',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithCoversCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @covers Foo
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @covers Bar
                     * @covers Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @covers Foo
                     * @covers Bar
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @covers Bar
                     * Comment 3
                     * @covers Foo
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @covers Foo
                     * Comment 2
                     * @covers Bar
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @covers A
                     * @covers c
                     * @covers D
                     * @covers E
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @covers A
                     * @covers E
                     * @covers c
                     * @covers D
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @covers Bar
                         * @dataProvider provide
                         * @covers Foo
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @covers Foo
                         * @dataProvider provide
                         * @covers Bar
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithCoversNothingCases
     */
    public function testFixWithCoversNothing(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'coversNothing',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithCoversNothingCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @coversNothing Foo
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @coversNothing Bar
                     * @coversNothing Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @coversNothing Foo
                     * @coversNothing Bar
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @coversNothing Bar
                     * Comment 3
                     * @coversNothing Foo
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @coversNothing Foo
                     * Comment 2
                     * @coversNothing Bar
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @coversNothing A
                     * @coversNothing c
                     * @coversNothing D
                     * @coversNothing E
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @coversNothing A
                     * @coversNothing E
                     * @coversNothing c
                     * @coversNothing D
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @coversNothing Bar
                         * @dataProvider provide
                         * @coversNothing Foo
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @coversNothing Foo
                         * @dataProvider provide
                         * @coversNothing Bar
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithDataProviderCases
     */
    public function testFixWithDataProvider(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'dataProvider',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithDataProviderCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @dataProvider Foo::provideData()
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @dataProvider Bar::provideData()
                     * @dataProvider Foo::provideData()
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @dataProvider Foo::provideData()
                     * @dataProvider Bar::provideData()
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @dataProvider Bar::provideData()
                     * Comment 3
                     * @dataProvider Foo::provideData()
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @dataProvider Foo::provideData()
                     * Comment 2
                     * @dataProvider Bar::provideData()
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @dataProvider A::provideData()
                     * @dataProvider c::provideData()
                     * @dataProvider D::provideData()
                     * @dataProvider E::provideData()
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @dataProvider A::provideData()
                     * @dataProvider E::provideData()
                     * @dataProvider c::provideData()
                     * @dataProvider D::provideData()
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @dataProvider Bar::provideData()
                         * @dataProvider dataProvider
                         * @dataProvider Foo::provideData()
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @dataProvider Foo::provideData()
                         * @dataProvider dataProvider
                         * @dataProvider Bar::provideData()
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithDependsCases
     */
    public function testFixWithDepends(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'depends',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithDependsCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @depends testFoo
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @depends testBar
                     * @depends testFoo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @depends testFoo
                     * @depends testBar
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @depends testBar
                     * Comment 3
                     * @depends testFoo
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @depends testFoo
                     * Comment 2
                     * @depends testBar
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @depends testA
                     * @depends testc
                     * @depends testD
                     * @depends testE
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @depends testA
                     * @depends testE
                     * @depends testc
                     * @depends testD
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @depends testBar
                         * @dataProvider provide
                         * @depends testFoo
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @depends testFoo
                         * @dataProvider provide
                         * @depends testBar
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithGroupCases
     */
    public function testFixWithGroup(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'group',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithGroupCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @group slow
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @group fast
                     * @group slow
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @group slow
                     * @group fast
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @group fast
                     * Comment 3
                     * @group slow
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @group slow
                     * Comment 2
                     * @group fast
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @group A
                     * @group c
                     * @group D
                     * @group E
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @group A
                     * @group E
                     * @group c
                     * @group D
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @group fast
                         * @dataProvider provide
                         * @group slow
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @group slow
                         * @dataProvider provide
                         * @group fast
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithInternalCases
     */
    public function testFixWithInternal(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'internal',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithInternalCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @internal Foo
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @internal Bar
                     * @internal Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @internal Foo
                     * @internal Bar
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @internal Bar
                     * Comment 3
                     * @internal Foo
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @internal Foo
                     * Comment 2
                     * @internal Bar
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @internal A
                     * @internal c
                     * @internal D
                     * @internal E
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @internal A
                     * @internal E
                     * @internal c
                     * @internal D
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @internal Bar
                         * @dataProvider provide
                         * @internal Foo
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @internal Foo
                         * @dataProvider provide
                         * @internal Bar
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithMethodCases
     */
    public function testFixWithMethod(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'method',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithMethodCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                /**
                 * @method int foo(array $b)
                 */
                class Foo {}
                ',
            ],
            'base case' => [
                '<?php
                /**
                 * @method bool bar(int $a, bool $b)
                 * @method array|null baz()
                 * @method int foo(array $b)
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @method int foo(array $b)
                 * @method bool bar(int $a, bool $b)
                 * @method array|null baz()
                 */
                class Foo {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                /**
                 * Comment 1
                 * @method bool bar(int $a, bool $b)
                 * Comment 3
                 * @method int foo(array $b)
                 * Comment 2
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * Comment 1
                 * @method int foo(array $b)
                 * Comment 2
                 * @method bool bar(int $a, bool $b)
                 * Comment 3
                 */
                class Foo {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                /**
                 * @method int A()
                 * @method bool b()
                 * @method array|null c()
                 * @method float D()
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @method array|null c()
                 * @method float D()
                 * @method bool b()
                 * @method int A()
                 */
                class Foo {}
                ',
            ],
            'with-possibly-unexpected-comparable' => [
                '<?php
                /**
                 * @method int foo(Z $b)
                 * @method int fooA( $b)
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @method int fooA( $b)
                 * @method int foo(Z $b)
                 */
                class Foo {}
                ',
            ],
            'with-and-without-types' => [
                '<?php
                /**
                 * @method int A()
                 * @method b()
                 * @method array|null c()
                 * @method D()
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @method array|null c()
                 * @method D()
                 * @method b()
                 * @method int A()
                 */
                class Foo {}
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithMixinCases
     */
    public function testFixWithMixin(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'mixin',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithMixinCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    /**
                     * @package SomePackage
                     * @mixin Bar
                     * @license MIT
                     */
                    class Foo {
                    }

                    /**
                     * @package SomePackage
                     * @license MIT
                     */
                    class Foo2 {
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @mixin Bar1
                     * @mixin Bar2
                     * @mixin Bar3
                     */
                    class Foo {
                    }
                ',
                '<?php
                    /**
                     * @mixin Bar2
                     * @mixin Bar3
                     * @mixin Bar1
                     */
                    class Foo {
                    }
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @mixin Bar1
                     * Comment 3
                     * @mixin Bar2
                     * Comment 2
                     */
                    class Foo {
                    }
                ',
                '<?php
                    /**
                     * Comment 1
                     * @mixin Bar2
                     * Comment 2
                     * @mixin Bar1
                     * Comment 3
                     */
                    class Foo {
                    }
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @mixin A
                     * @mixin b
                     * @mixin C
                     */
                    class Foo {
                    }
                ',
                '<?php
                    /**
                     * @mixin b
                     * @mixin A
                     * @mixin C
                     */
                    class Foo {
                    }
                ',
            ],
            'fully-qualified' => [
                '<?php
                    /**
                     * @mixin \A\B\Bar2
                     * @mixin Bar1
                     * @mixin Bar3
                     */
                    class Foo {
                    }
                ',
                '<?php
                    /**
                     * @mixin Bar3
                     * @mixin Bar1
                     * @mixin \A\B\Bar2
                     */
                    class Foo {
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithPropertyCases
     */
    public function testFixWithProperty(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'property',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPropertyCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                /**
                 * @property int|\stdClass $foo
                 */
                class Foo {}
                ',
            ],
            'base case' => [
                '<?php
                /**
                 * @property bool $bar
                 * @property array|null $baz
                 * @property int|\stdClass $foo
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property int|\stdClass $foo
                 * @property bool $bar
                 * @property array|null $baz
                 */
                class Foo {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                /**
                 * Comment 1
                 * @property bool $bar
                 * Comment 3
                 * @property int|\stdClass $foo
                 * Comment 2
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * Comment 1
                 * @property int|\stdClass $foo
                 * Comment 2
                 * @property bool $bar
                 * Comment 3
                 */
                class Foo {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                /**
                 * @property int|\stdClass $A
                 * @property bool $b
                 * @property array|null $C
                 * @property float $D
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property array|null $C
                 * @property float $D
                 * @property bool $b
                 * @property int|\stdClass $A
                 */
                class Foo {}
                ',
            ],
            'with-and-without-types' => [
                '<?php
                /**
                 * @property int|\stdClass $A
                 * @property $b
                 * @property array|null $C
                 * @property $D
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property array|null $C
                 * @property $D
                 * @property $b
                 * @property int|\stdClass $A
                 */
                class Foo {}
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithPropertyReadCases
     */
    public function testFixWithPropertyRead(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'property-read',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPropertyReadCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                /**
                 * @property-read int|\stdClass $foo
                 */
                class Foo {}
                ',
            ],
            'base case' => [
                '<?php
                /**
                 * @property-read bool $bar
                 * @property-read array|null $baz
                 * @property-read int|\stdClass $foo
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property-read int|\stdClass $foo
                 * @property-read bool $bar
                 * @property-read array|null $baz
                 */
                class Foo {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                /**
                 * Comment 1
                 * @property-read bool $bar
                 * Comment 3
                 * @property-read int|\stdClass $foo
                 * Comment 2
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * Comment 1
                 * @property-read int|\stdClass $foo
                 * Comment 2
                 * @property-read bool $bar
                 * Comment 3
                 */
                class Foo {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                /**
                 * @property-read int|\stdClass $A
                 * @property-read bool $b
                 * @property-read array|null $C
                 * @property-read float $D
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property-read array|null $C
                 * @property-read float $D
                 * @property-read bool $b
                 * @property-read int|\stdClass $A
                 */
                class Foo {}
                ',
            ],
            'with-and-without-types' => [
                '<?php
                /**
                 * @property-read int|\stdClass $A
                 * @property-read $b
                 * @property-read array|null $C
                 * @property-read $D
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property-read array|null $C
                 * @property-read $D
                 * @property-read $b
                 * @property-read int|\stdClass $A
                 */
                class Foo {}
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithPropertyWriteCases
     */
    public function testFixWithPropertyWrite(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'property-write',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPropertyWriteCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                /**
                 * @property-write int|\stdClass $foo
                 */
                class Foo {}
                ',
            ],
            'base case' => [
                '<?php
                /**
                 * @property-write bool $bar
                 * @property-write array|null $baz
                 * @property-write int|\stdClass $foo
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property-write int|\stdClass $foo
                 * @property-write bool $bar
                 * @property-write array|null $baz
                 */
                class Foo {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                /**
                 * Comment 1
                 * @property-write bool $bar
                 * Comment 3
                 * @property-write int|\stdClass $foo
                 * Comment 2
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * Comment 1
                 * @property-write int|\stdClass $foo
                 * Comment 2
                 * @property-write bool $bar
                 * Comment 3
                 */
                class Foo {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                /**
                 * @property-write int|\stdClass $A
                 * @property-write bool $b
                 * @property-write array|null $C
                 * @property-write float $D
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property-write array|null $C
                 * @property-write float $D
                 * @property-write bool $b
                 * @property-write int|\stdClass $A
                 */
                class Foo {}
                ',
            ],
            'with-and-without-types' => [
                '<?php
                /**
                 * @property-write int|\stdClass $A
                 * @property-write $b
                 * @property-write array|null $C
                 * @property-write $D
                 */
                class Foo {}
                ',
                '<?php
                /**
                 * @property-write array|null $C
                 * @property-write $D
                 * @property-write $b
                 * @property-write int|\stdClass $A
                 */
                class Foo {}
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithRequiresCases
     */
    public function testFixWithRequires(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'requires',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithRequiresCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @requires function json_decode
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @requires extension redis
                     * @requires function json_decode
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @requires function json_decode
                     * @requires extension redis
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @requires extension redis
                     * Comment 3
                     * @requires function json_decode
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @requires function json_decode
                     * Comment 2
                     * @requires extension redis
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @requires extension redis
                     * @requires function json_decode
                     * @requires OS Linux
                     * @requires PHP 7.2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @requires PHP 7.2
                     * @requires function json_decode
                     * @requires extension redis
                     * @requires OS Linux
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @requires extension redis
                         * @dataProvider provide
                         * @requires function json_decode
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @requires function json_decode
                         * @dataProvider provide
                         * @requires extension redis
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithThrowsCases
     */
    public function testFixWithThrows(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'throws',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithThrowsCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class Foo {
                        /**
                         * @throws Bar
                         * @params bool $bool
                         * @return void
                         */
                        public function bar() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function baz() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    class Foo
                    {
                        /**
                         * @throws Bar
                         * @throws Baz
                         */
                        public function bar() {}
                    }
                ',
                '<?php
                    class Foo
                    {
                        /**
                         * @throws Baz
                         * @throws Bar
                         */
                        public function bar() {}
                    }
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    class Foo
                    {
                        /**
                         * Comment 1
                         * @throws Bar
                         * Comment 3
                         * @throws Baz
                         * Comment 2
                         */
                        public function bar() {}
                    }
                ',
                '<?php
                    class Foo
                    {
                        /**
                         * Comment 1
                         * @throws Baz
                         * Comment 2
                         * @throws Bar
                         * Comment 3
                         */
                        public function bar() {}
                    }
                ',
            ],
            'case-insensitive' => [
                '<?php
                    class Foo
                    {
                        /**
                         * @throws A
                         * @throws b
                         * @throws C
                         */
                        public function bar() {}
                    }
                ',
                '<?php
                    class Foo
                    {
                        /**
                         * @throws b
                         * @throws C
                         * @throws A
                         */
                        public function bar() {}
                    }
                ',
            ],
            'fully-qualified' => [
                '<?php
                    class Foo
                    {
                        /**
                         * @throws \Bar\Baz\Qux
                         * @throws Bar
                         * @throws Foo
                         */
                        public function bar() {}
                    }
                ',
                '<?php
                    class Foo
                    {
                        /**
                         * @throws Bar
                         * @throws \Bar\Baz\Qux
                         * @throws Foo
                         */
                        public function bar() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithUsesCases
     */
    public function testFixWithUses(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'uses',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithUsesCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @uses Foo
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @uses Bar
                     * @uses Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @uses Foo
                     * @uses Bar
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @uses Bar
                     * Comment 3
                     * @uses Foo
                     * Comment 2
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @uses Foo
                     * Comment 2
                     * @uses Bar
                     * Comment 3
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @uses A
                     * @uses c
                     * @uses D
                     * @uses E
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @uses A
                     * @uses E
                     * @uses c
                     * @uses D
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @uses Bar
                         * @dataProvider provide
                         * @uses Foo
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @uses Foo
                         * @dataProvider provide
                         * @uses Bar
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithMultipleConfiguredAnnotationsCases
     */
    public function testFixWithMultipleConfiguredAnnotations(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'annotations' => [
                'covers',
                'uses',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithMultipleConfiguredAnnotationsCases(): array
    {
        return [
            'skip on 1 or 0 occurrences' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase {
                        /**
                         * @uses Foo
                         * @covers Baz
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe() {}

                        /**
                         * @params bool $bool
                         * @return void
                         */
                        public function testMe2() {}
                    }
                ',
            ],
            'base case' => [
                '<?php
                    /**
                     * @uses Bar
                     * @uses Foo
                     * @covers Baz
                     * @covers Qux
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @uses Foo
                     * @uses Bar
                     * @covers Qux
                     * @covers Baz
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'preserve positions if other docblock parts are present' => [
                '<?php
                    /**
                     * Comment 1
                     * @uses Bar
                     * Comment 3
                     * @uses Foo
                     * Comment 2
                     * @covers Baz
                     * Comment 5
                     * @covers Qux
                     * Comment 4
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * Comment 1
                     * @uses Foo
                     * Comment 2
                     * @uses Bar
                     * Comment 3
                     * @covers Qux
                     * Comment 4
                     * @covers Baz
                     * Comment 5
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'case-insensitive' => [
                '<?php
                    /**
                     * @uses A
                     * @uses c
                     * @covers D
                     * @covers e
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /**
                     * @uses c
                     * @uses A
                     * @covers e
                     * @covers D
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'data provider' => [
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @uses Bar
                         * @dataProvider provideOne
                         * @uses Foo
                         * @dataProvider provideTwo
                         * @covers Baz
                         * @dataProvider provideThree
                         * @covers Qux
                         */
                        public function testMe() {}
                    }
                ',
                '<?php
                    class FooTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @uses Foo
                         * @dataProvider provideOne
                         * @uses Bar
                         * @dataProvider provideTwo
                         * @covers Qux
                         * @dataProvider provideThree
                         * @covers Baz
                         */
                        public function testMe() {}
                    }
                ',
            ],
        ];
    }
}
