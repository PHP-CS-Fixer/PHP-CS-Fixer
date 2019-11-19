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
    public function testConfigureRejectsInvalidControlStatement($annotation)
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure([
            'annotations' => [
                $annotation,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function provideInvalidAnnotationCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithCoversCases
     */
    public function testFixWithDefaultConfiguration($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithAuthorCases
     */
    public function testFixWithAuthor($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'author',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAuthorCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithCoversCases
     */
    public function testFixWithCovers($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'covers',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithCoversCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithCoversNothingCases
     */
    public function testFixWithCoversNothing($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'coversNothing',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithCoversNothingCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithDataProviderCases
     */
    public function testFixWithDataProvider($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'dataProvider',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithDataProviderCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithDependsCases
     */
    public function testFixWithDepends($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'depends',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithDependsCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithGroupCases
     */
    public function testFixWithGroup($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'group',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithGroupCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithInternalCases
     */
    public function testFixWithInternal($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'internal',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithInternalCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithRequiresCases
     */
    public function testFixWithRequires($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'requires',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithRequiresCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithUsesCases
     */
    public function testFixWithUses($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'uses',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithUsesCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithMultipleConfiguredAnnotationsCases
     */
    public function testFixWithMultipleConfiguredAnnotations($expected, $input = null)
    {
        $this->fixer->configure([
            'annotations' => [
                'covers',
                'uses',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithMultipleConfiguredAnnotationsCases()
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
