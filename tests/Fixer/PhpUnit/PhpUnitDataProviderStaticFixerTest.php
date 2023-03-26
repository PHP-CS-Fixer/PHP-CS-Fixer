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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderStaticFixer
 */
final class PhpUnitDataProviderStaticFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, bool> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: array<string, bool>}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'do not fix when containing dynamic calls by default' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFoo1Cases
     */
    public function testFoo1() {}
    public function provideFoo1Cases() { $this->init(); }
}',
        ];

        yield 'fix single' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public static function provideFooCases() { $x->getData(); }
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public function provideFooCases() { $x->getData(); }
}',
        ];

        yield 'fix multiple' => [
            '<?php
class FooTest extends TestCase {
    /** @dataProvider provider1 */
    public function testFoo1() {}
    /** @dataProvider provider2 */
    public function testFoo2() {}
    /** @dataProvider provider3 */
    public function testFoo3() {}
    /** @dataProvider provider4 */
    public function testFoo4() {}
    public static function provider1() {}
    public function provider2() { $this->init(); }
    public static function provider3() {}
    public static function provider4() {}
}',
            '<?php
class FooTest extends TestCase {
    /** @dataProvider provider1 */
    public function testFoo1() {}
    /** @dataProvider provider2 */
    public function testFoo2() {}
    /** @dataProvider provider3 */
    public function testFoo3() {}
    /** @dataProvider provider4 */
    public function testFoo4() {}
    public function provider1() {}
    public function provider2() { $this->init(); }
    public function provider3() {}
    public static function provider4() {}
}',
        ];

        yield 'fix with multilines' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public
        static function
            provideFooCases() { $x->getData(); }
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public
        function
            provideFooCases() { $x->getData(); }
}',
        ];

        yield 'fix when data provider is abstract' => [
            '<?php
abstract class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    abstract public static function provideFooCases();
}',
            '<?php
abstract class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    abstract public function provideFooCases();
}',
        ];

        yield 'fix when containing dynamic calls and with `force` disabled' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases1
     * @dataProvider provideFooCases2
     */
    public function testFoo() {}
    public function provideFooCases1() { return $this->getFoo(); }
    public static function provideFooCases2() { /* no dynamic calls */ }
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases1
     * @dataProvider provideFooCases2
     */
    public function testFoo() {}
    public function provideFooCases1() { return $this->getFoo(); }
    public function provideFooCases2() { /* no dynamic calls */ }
}',
            ['force' => false],
        ];

        yield 'fix when containing dynamic calls and with `force` enabled' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases1
     * @dataProvider provideFooCases2
     */
    public function testFoo() {}
    public static function provideFooCases1() { return $this->getFoo(); }
    public static function provideFooCases2() { /* no dynamic calls */ }
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases1
     * @dataProvider provideFooCases2
     */
    public function testFoo() {}
    public function provideFooCases1() { return $this->getFoo(); }
    public function provideFooCases2() { /* no dynamic calls */ }
}',
            ['force' => true],
        ];
    }
}
