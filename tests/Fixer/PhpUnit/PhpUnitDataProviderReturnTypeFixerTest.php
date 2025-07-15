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
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderReturnTypeFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderReturnTypeFixer>
 */
final class PhpUnitDataProviderReturnTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'data provider with iterable return type' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public function provideFooCases() : iterable {}
}',
        ];

        yield 'data provider without return type' => self::mapToTemplate(
            ': iterable',
            '',
        );

        yield 'data provider with array return type' => self::mapToTemplate(
            ': iterable',
            ': array',
        );

        yield 'data provider with return type and comment' => self::mapToTemplate(
            ': /* foo */ iterable',
            ': /* foo */ array',
        );

        yield 'data provider with return type namespaced class' => self::mapToTemplate(
            ': iterable',
            ': Foo\Bar',
        );

        yield 'data provider with iterable return type in different case' => self::mapToTemplate(
            ': iterable',
            ': Iterable',
        );

        yield 'multiple data providers' => [
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider4
                 * @dataProvider provider1
                 * @dataProvider provider5
                 * @dataProvider provider6
                 * @dataProvider provider2
                 * @dataProvider provider3
                 */
                public function testFoo() {}
                public function provider1(): iterable {}
                public function provider2(): iterable {}
                public function provider3(): iterable {}
                public function provider4(): iterable {}
                public function provider5(): iterable {}
                public function provider6(): iterable {}
            }',
            '<?php class FooTest extends TestCase {
                /**
                 * @dataProvider provider4
                 * @dataProvider provider1
                 * @dataProvider provider5
                 * @dataProvider provider6
                 * @dataProvider provider2
                 * @dataProvider provider3
                 */
                public function testFoo() {}
                public function provider1() {}
                public function provider2() {}
                public function provider3() {}
                public function provider4() {}
                public function provider5() {}
                public function provider6() {}
            }',
        ];

        yield 'advanced case' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     * @dataProvider provideFooCases2
     */
    public function testFoo()
    {
        /**
         * @dataProvider someFunction
         */
        $foo = /** foo */ function ($x) { return $x + 1; };
        /**
         * @dataProvider someFunction2
         */
        /* foo */someFunction2();
    }
    /**
     * @dataProvider provideFooCases3
     */
    public function testBar() {}

    public function provideFooCases(): iterable {}
    public function provideFooCases2(): iterable {}
    public function provideFooCases3(): iterable {}
    public function someFunction() {}
    public function someFunction2() {}
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     * @dataProvider provideFooCases2
     */
    public function testFoo()
    {
        /**
         * @dataProvider someFunction
         */
        $foo = /** foo */ function ($x) { return $x + 1; };
        /**
         * @dataProvider someFunction2
         */
        /* foo */someFunction2();
    }
    /**
     * @dataProvider provideFooCases3
     */
    public function testBar() {}

    public function provideFooCases() {}
    public function provideFooCases2() {}
    public function provideFooCases3() {}
    public function someFunction() {}
    public function someFunction2() {}
}',
        ];

        foreach (['abstract', 'final', 'private', 'protected', 'static', '/* private */'] as $modifier) {
            yield \sprintf('test function with %s modifier', $modifier) => [
                \sprintf('<?php
                    abstract class FooTest extends TestCase {
                        /**
                         * @dataProvider provideFooCases
                         */
                        %s function testFoo() %s
                        public function provideFooCases(): iterable {}
                    }
                ', $modifier, 'abstract' === $modifier ? ';' : '{}'),
                \sprintf('<?php
                    abstract class FooTest extends TestCase {
                        /**
                         * @dataProvider provideFooCases
                         */
                        %s function testFoo() %s
                        public function provideFooCases() {}
                    }
                ', $modifier, 'abstract' === $modifier ? ';' : '{}'),
            ];
        }
    }

    /**
     * @requires PHP <8.0
     *
     * @dataProvider provideFixPre80Cases
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield 'data provider with return type namespaced class starting with iterable' => self::mapToTemplate(
            ': iterable \ Foo',
        );

        yield 'data provider with return type namespaced class and comments' => self::mapToTemplate(
            ': iterable/* Some info */\/* More info */Bar',
        );
    }

    /**
     * @requires PHP ^8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'with an attribute between PHPDoc and test method' => [
            <<<'PHP'
                <?php
                class FooTest extends TestCase {
                    /**
                     * @dataProvider provideFooCases
                     */
                    #[CustomAttribute]
                    public function testFoo(): void {}
                    public function provideFooCases(): iterable {}
                }
                PHP,
            <<<'PHP'
                <?php
                class FooTest extends TestCase {
                    /**
                     * @dataProvider provideFooCases
                     */
                    #[CustomAttribute]
                    public function testFoo(): void {}
                    public function provideFooCases() {}
                }
                PHP,
        ];

        yield 'with data provider as an attribute' => [
            <<<'PHP'
                <?php
                class FooTest extends TestCase {
                    #[\PHPUnit\Framework\Attributes\DataProvider('addTypeToMe')]
                    public function testFoo(): void {}
                    public function addTypeToMe(): iterable {}
                }
                PHP,
            <<<'PHP'
                <?php
                class FooTest extends TestCase {
                    #[\PHPUnit\Framework\Attributes\DataProvider('addTypeToMe')]
                    public function testFoo(): void {}
                    public function addTypeToMe() {}
                }
                PHP,
        ];

        $withAttributesTemplate = <<<'PHP'
            <?php
            namespace N;
            use PHPUnit\Framework as PphUnitAlias;
            use PHPUnit\Framework\Attributes;
            class FooTest extends TestCase {
                #[\PHPUnit\Framework\Attributes\DataProvider('provider1')]
                #[\PHPUnit\Framework\Attributes\DataProvider('doNotGetFooledByConcatenation' . 'notProvider1')]
                #[PHPUnit\Framework\Attributes\DataProvider('notProvider2')]
                #[
                    \PHPUnit\Framework\Attributes\BackupGlobals(true),
                    \PHPUnit\Framework\Attributes\DataProvider('provider2'),
                    \PHPUnit\Framework\Attributes\Group('foo'),
                ]
                #[Attributes\DataProvider('provider3')]
                #[PphUnitAlias\Attributes\DataProvider('provider4')]
                #[\PHPUnit\Framework\Attributes\DataProvider]
                #[\PHPUnit\Framework\Attributes\DataProvider('provider5')]
                #[\PHPUnit\Framework\Attributes\DataProvider(123)]
                public function testSomething(int $x): void {}
                public function provider1()%1$s {}
                public function provider2()%1$s {}
                public function provider3()%1$s {}
                public function provider4()%1$s {}
                public function provider5()%1$s {}
                public function notProvider1() {}
                public function notProvider2() {}
            }
            PHP;

        yield 'with multiple data providers as an attributes' => [
            \sprintf($withAttributesTemplate, ': iterable'),
            \sprintf($withAttributesTemplate, ''),
        ];
    }

    /**
     * @param array{string, 1?: string} ...$types
     *
     * @return array{string, 1?: string}
     */
    private static function mapToTemplate(string ...$types): array
    {
        // @phpstan-ignore-next-line return.type
        return array_map(
            static fn (string $type): string => \sprintf(
                <<<'PHP'
                    <?php
                    class FooTest extends TestCase {
                        /**
                         * @dataProvider provideFooCases
                         */
                        public function testFoo() {}
                        /**
                         * @dataProvider provider
                         */
                        public function testBar() {}
                        public function provideFooCases()%1$s {}
                        public function provider()%1$s {}
                        public function notProvider(): array {}
                    }
                    PHP,
                $type
            ),
            $types
        );
    }
}
