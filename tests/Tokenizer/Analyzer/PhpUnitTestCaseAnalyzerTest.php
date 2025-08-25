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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\PhpUnitTestCaseAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\PhpUnitTestCaseAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpUnitTestCaseAnalyzerTest extends TestCase
{
    /**
     * @param list<array{0: int, 1: int}> $expectedIndexes
     *
     * @dataProvider provideFindPhpUnitClassesCases
     */
    public function testFindPhpUnitClasses(array $expectedIndexes, string $code): void
    {
        $tokens = Tokens::fromCode($code);

        $classes = (new PhpUnitTestCaseAnalyzer())->findPhpUnitClasses($tokens);
        $classes = iterator_to_array($classes);

        self::assertSame($expectedIndexes, $classes);
    }

    /**
     * @return iterable<string, array{list<array{int, int}>, string}>
     */
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

        yield 'single Test class' => [
            [
                [10, 11],
            ],
            '<?php
                class MyTest extends Foo {}
            ',
        ];

        yield 'single TestCase class' => [
            [
                [11, 12],
            ],
            '<?php final class SomeTestCase extends A {}',
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

        yield 'mixed classes that all extends' => [
            [
                [50, 51],
                [21, 22],
            ],
            '<?php
                class A extends Foo {}
                class B extends TestCase {}
                class C extends Bar {}
                class D extends Foo implements TestCaseInterface, Bar {}
                class E extends Baz {}
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

        yield 'anonymous class extending TestCase' => [
            [
                [17, 18],
            ],
            <<<'PHP'
                <?php
                $myTest = new class () extends \PHPUnit_Framework_TestCase {};
                PHP,
        ];

        yield 'extends Test' => [
            [
                [11, 12],
            ],
            '<?php final class Foo extends Test {}',
        ];

        yield 'extends TestCase' => [
            [
                [11, 12],
            ],
            '<?php final class bar extends TestCase {}',
        ];

        yield 'implements AbstractFixerTest' => [
            [
                [21, 22],
            ],
            '<?php class A extends Foo implements PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest {}',
        ];

        yield 'extends TestCase implements Foo' => [
            [
                [13, 14],
            ],
            '<?php class A extends TestCase implements Foo {}',
        ];

        yield 'implements TestInterface' => [
            [
                [13, 14],
            ],
            '<?php class Foo extends A implements SomeTestInterface {}',
        ];

        yield 'implements TestInterface, SomethingElse' => [
            [
                [16, 17],
            ],
            '<?php class Foo extends A implements TestInterface, SomethingElse {}',
        ];

        yield 'anonymous class' => [
            [],
            '<?php $a = new class {};',
        ];

        yield 'test class that does not extends' => [
            [],
            '<?php final class MyTest {}',
        ];

        yield 'testCase class that does not extends' => [
            [],
            '<?php final class SomeTestCase implements PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest {}',
        ];
    }
}
