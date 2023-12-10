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

namespace PhpCsFixer\Tests\Indicator;

use PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Indicator\PhpUnitTestCaseIndicator
 */
final class PhpUnitTestCaseIndicatorTest extends TestCase
{
    private ?PhpUnitTestCaseIndicator $indicator;

    protected function setUp(): void
    {
        $this->indicator = new PhpUnitTestCaseIndicator();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->indicator = null;

        parent::tearDown();
    }

    /**
     * @dataProvider provideIsPhpUnitClassCases
     */
    public function testIsPhpUnitClass(bool $expected, Tokens $tokens, int $index): void
    {
        self::assertSame($expected, $this->indicator->isPhpUnitClass($tokens, $index));
    }

    public static function provideIsPhpUnitClassCases(): iterable
    {
        yield 'Test class' => [
            true,
            Tokens::fromCode('<?php final class MyTest extends A {}'),
            3,
        ];

        yield 'TestCase class' => [
            true,
            Tokens::fromCode('<?php final class SomeTestCase extends A {}'),
            3,
        ];

        yield 'Extends Test' => [
            true,
            Tokens::fromCode('<?php final class foo extends Test {}'),
            3,
        ];

        yield 'Extends TestCase' => [
            true,
            Tokens::fromCode('<?php final class bar extends TestCase {}'),
            3,
        ];

        yield 'Implements AbstractFixerTest' => [
            true,
            Tokens::fromCode('<?php
class A extends Foo implements PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest
{
}
'),
            1,
        ];

        yield 'Extends TestCase implements Foo' => [
            true,
            Tokens::fromCode('<?php
class A extends TestCase implements Foo
{
}
'),
            1,
        ];

        yield 'Implements TestInterface' => [
            true,
            Tokens::fromCode('<?php
class Foo extends A implements SomeTestInterface
{
}
'),
            1,
        ];

        yield 'Implements TestInterface, SomethingElse' => [
            true,
            Tokens::fromCode('<?php
class Foo extends A implements TestInterface, SomethingElse
{
}
'),
            1,
        ];

        yield [
            false,
            Tokens::fromCode('<?php final class MyClass {}'),
            3,
        ];

        yield 'Anonymous class' => [
            false,
            Tokens::fromCode('<?php $a = new class {};'),
            7,
        ];

        yield 'Test class that does not extends' => [
            false,
            Tokens::fromCode('<?php final class MyTest {}'),
            3,
        ];

        yield 'TestCase class that does not extends' => [
            false,
            Tokens::fromCode('<?php final class SomeTestCase implements PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest {}'),
            3,
        ];
    }

    public function testThrowsExceptionIfNotClass(): void
    {
        $tokens = Tokens::fromCode('<?php echo 1;');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/^No "T_CLASS" at given index 1, got "T_ECHO"\.$/');

        $this->indicator->isPhpUnitClass($tokens, 1);
    }

    /**
     * @param array<array{0: int, 1: int}> $expectedIndexes
     *
     * @dataProvider provideFindPhpUnitClassesCases
     */
    public function testFindPhpUnitClasses(array $expectedIndexes, string $code): void
    {
        $tokens = Tokens::fromCode($code);

        $classes = $this->indicator->findPhpUnitClasses($tokens);
        $classes = iterator_to_array($classes);

        self::assertSame($expectedIndexes, $classes);
    }

    public static function provideFindPhpUnitClassesCases(): iterable
    {
        yield 'empty' => [
            [],
            '',
        ];

        yield 'empty script' => [
            [],
            "<?php\n",
        ];

        yield 'no test class' => [
            [],
            '<?php class Foo{}',
        ];

        yield 'single test class' => [
            [
                [10, 11],
            ],
            '<?php
                class MyTest extends Foo {}
            ',
        ];

        yield 'two PHPUnit classes' => [
            [
                [21, 34],
                [10, 11],
            ],
            '<?php
                class My1Test extends Foo1 {}
                class My2Test extends Foo2 { public function A8() {} }
            ',
        ];

        yield 'mixed classes' => [
            [
                [71, 84],
                [29, 42],
            ],
            '<?php
                class Foo1 { public function A1() {} }
                class My1Test extends Foo1 { public function A2() {} }
                class Foo2 { public function A3() {} }
                class My2Test extends Foo2 { public function A4() {} }
                class Foo3 { public function A5() { return function (){}; } }
            ',
        ];

        yield 'class with anonymous class inside' => [
            [],
            '<?php
                class Foo
                {
                    public function getClass()
                    {
                        return new class {};
                    }
                }
            ',
        ];
    }
}
