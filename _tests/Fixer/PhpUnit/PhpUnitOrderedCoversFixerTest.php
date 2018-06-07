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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitOrderedCoversFixer
 */
final class PhpUnitOrderedCoversFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
}
