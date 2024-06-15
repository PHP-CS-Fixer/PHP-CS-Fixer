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

namespace PhpCsFixer\Tests\Fixer\AttributeNotation;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\AttributeNotation\OrderedAttributesFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author HypeMC <hypemc@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AttributeNotation\OrderedAttributesFixer
 *
 * @requires PHP 8.0
 */
final class OrderedAttributesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideInvalidConfigurationCases
     *
     * @param array{sort_algorithm: OrderedAttributesFixer::ORDER_*, order: list<string>} $configuration
     */
    public function testInvalidConfiguration(array $configuration, string $expectedExceptionMessage): void
    {
        self::expectException(InvalidFixerConfigurationException::class);
        self::expectExceptionMessage($expectedExceptionMessage);

        $this->fixer->configure($configuration);
    }

    /**
     * @return iterable<array{0: array{sort_algorithm: OrderedAttributesFixer::ORDER_*, order?: list<string>}, 1: string}>
     */
    public static function provideInvalidConfigurationCases(): iterable
    {
        yield 'Custom order strategy without `order` option' => [
            ['sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM],
            'The custom order strategy requires providing `order` option with a list of attributes\'s FQNs.',
        ];

        yield 'Custom order strategy with empty `order` option' => [
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => [],
            ],
            'The custom order strategy requires providing `order` option with a list of attributes\'s FQNs.',
        ];

        yield 'Non unique attributes throw an exception' => [
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['A\B\Bar', 'Test\Corge', 'A\B\Bar'],
            ],
            'The list includes attributes that are not unique.',
        ];
    }

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
     * @return iterable<array{0: string, 1?: string, 2?: array<string, mixed>}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'Alpha on various declarations' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[AB\Baz(prop: \'baz\')]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[\A\B\Qux()]
            #[BarAlias(3)]
            #[Corge]
            #[Foo(4, \'baz qux\')]
            class X
            {
                #[AB\Baz(prop: \'baz\')]
                #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[\A\B\Qux()]
                #[BarAlias(3)]
                #[Corge]
                #[Foo(4, \'baz qux\')]
                const Y = 1;

                #[AB\Baz(prop: \'baz\')]
                #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[\A\B\Qux()]
                #[BarAlias(3)]
                #[Corge]
                #[Foo(4, \'baz qux\')]
                public $y;

                #[AB\Baz(prop: \'baz\')]
                #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[\A\B\Qux()]
                #[BarAlias(3)]
                #[Corge]
                #[Foo(4, \'baz qux\')]
                public function y() {}
            }

            #[AB\Baz(prop: \'baz\')]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[\A\B\Qux()]
            #[BarAlias(3)]
            #[Corge]
            #[Foo(4, \'baz qux\')]
            function f(
                #[AB\Baz(prop: \'baz\')]
                #[BarAlias(3)]
                #[Foo(4, \'baz qux\')]
                string $param1,
                #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[\A\B\Qux()]
                #[Corge]
                string $param2
            ) {}

            function f2(#[BarAlias(3)] #[Foo(4, \'baz qux\')] string $param1, #[\A\B\Qux()] #[Corge] string $param2) {}

            $anon = #[AB\Baz(prop: \'baz\')] #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')] #[\A\B\Qux()] #[BarAlias(3)] #[Corge] #[Foo(4, \'baz qux\')] function () {};
            $short = #[AB\Baz(prop: \'baz\')] #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')] #[\A\B\Qux()] #[BarAlias(3)] #[Corge] #[Foo(4, \'baz qux\')] fn () => null;
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[Foo(4, \'baz qux\')]
            #[BarAlias(3)]
            #[AB\Baz(prop: \'baz\')]
            #[\A\B\Qux()]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[Corge]
            class X
            {
                #[Foo(4, \'baz qux\')]
                #[BarAlias(3)]
                #[AB\Baz(prop: \'baz\')]
                #[\A\B\Qux()]
                #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[Corge]
                const Y = 1;

                #[Foo(4, \'baz qux\')]
                #[BarAlias(3)]
                #[AB\Baz(prop: \'baz\')]
                #[\A\B\Qux()]
                #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[Corge]
                public $y;

                #[Foo(4, \'baz qux\')]
                #[BarAlias(3)]
                #[AB\Baz(prop: \'baz\')]
                #[\A\B\Qux()]
                #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[Corge]
                public function y() {}
            }

            #[Foo(4, \'baz qux\')]
            #[BarAlias(3)]
            #[AB\Baz(prop: \'baz\')]
            #[\A\B\Qux()]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[Corge]
            function f(
                #[Foo(4, \'baz qux\')]
                #[BarAlias(3)]
                #[AB\Baz(prop: \'baz\')]
                string $param1,
                #[\A\B\Qux()]
                #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[Corge]
                string $param2
            ) {}

            function f2(#[Foo(4, \'baz qux\')] #[BarAlias(3)] string $param1, #[\A\B\Qux()] #[Corge] string $param2) {}

            $anon = #[Foo(4, \'baz qux\')] #[BarAlias(3)] #[AB\Baz(prop: \'baz\')] #[\A\B\Qux()] #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')] #[Corge] function () {};
            $short = #[Foo(4, \'baz qux\')] #[BarAlias(3)] #[AB\Baz(prop: \'baz\')] #[\A\B\Qux()] #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')] #[Corge] fn () => null;
            ',
        ];

        yield 'Explicit in namespace' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[BarAlias(3)]
            #[Corge]
            #[AB\Baz(prop: \'baz\')]
            #[Foo(4, \'baz qux\')]
            #[\A\B\Qux()]
            function f() {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[Foo(4, \'baz qux\')]
            #[BarAlias(3)]
            #[AB\Baz(prop: \'baz\')]
            #[\A\B\Qux()]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[Corge]
            function f() {}
            ',
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['Test\A\B\Quux', 'A\B\Bar', 'Test\Corge', 'A\B\Baz', 'A\B\Foo', 'A\B\Qux'],
            ],
        ];

        yield 'Explicit in global namespace' => [
            '<?php
            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[BarAlias(3)]
            #[Corge]
            #[AB\Baz(prop: \'baz\')]
            #[Foo(4, \'baz qux\')]
            #[\A\B\Qux()]
            function f() {}
            ',
            '<?php
            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[Foo(4, \'baz qux\')]
            #[BarAlias(3)]
            #[AB\Baz(prop: \'baz\')]
            #[\A\B\Qux()]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[Corge]
            function f() {}
            ',
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['A\B\Quux', 'A\B\Bar', 'Corge', 'A\B\Baz', 'A\B\Foo', 'A\B\Qux'],
            ],
        ];

        yield 'Explicit with unconfigured attributes' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[AB\Baz(prop: \'baz\')]
            #[\A\B\Qux()]
            #[Foo(4, \'baz qux\')]
            #[BarAlias(3)]
            #[Corge]
            function f() {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[Foo(4, \'baz qux\')]
            #[BarAlias(3)]
            #[AB\Baz(prop: \'baz\')]
            #[\A\B\Qux()]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[Corge]
            function f() {}
            ',
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['Test\A\B\Quux', 'A\B\Baz', 'A\B\Qux'],
            ],
        ];

        yield 'Multiple namespaces' => [
            '<?php
            namespace Test
            {
                use A\B\Foo;
                use A\B\Bar as BarAlias;

                #[BarAlias(3)]
                #[AB\Baz(prop: \'baz\')]
                #[Foo(4, \'baz qux\')]
                function f() {}
            }

            namespace Test2
            {
                use A\B\Bar as BarAlias;
                use A\B as AB;

                #[BarAlias(3)]
                #[\A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                #[AB\Baz(prop: \'baz\')]
                function f() {}
            }

            namespace Test
            {
                use A\B\Foo;
                use A\B\Bar as BarAlias2;

                #[BarAlias2(3)]
                #[AB\Baz(prop: \'baz\')]
                #[Foo(4, \'baz qux\')]
                function f2() {}
            }

            namespace
            {
                use A\B\Foo;
                use A\B\Bar as BarAlias3;

                #[BarAlias3(3)]
                #[Foo(4, \'baz qux\')]
                #[AB\Baz(prop: \'baz\')]
                function f() {}
            }
            ',
            '<?php
            namespace Test
            {
                use A\B\Foo;
                use A\B\Bar as BarAlias;

                #[AB\Baz(prop: \'baz\')]
                #[Foo(4, \'baz qux\')]
                #[BarAlias(3)]
                function f() {}
            }

            namespace Test2
            {
                use A\B\Bar as BarAlias;
                use A\B as AB;

                #[BarAlias(3)]
                #[AB\Baz(prop: \'baz\')]
                #[\A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
                function f() {}
            }

            namespace Test
            {
                use A\B\Foo;
                use A\B\Bar as BarAlias2;

                #[AB\Baz(prop: \'baz\')]
                #[Foo(4, \'baz qux\')]
                #[BarAlias2(3)]
                function f2() {}
            }

            namespace
            {
                use A\B\Foo;
                use A\B\Bar as BarAlias3;

                #[AB\Baz(prop: \'baz\')]
                #[Foo(4, \'baz qux\')]
                #[BarAlias3(3)]
                function f() {}
            }
            ',
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['A\B\Bar', 'Test\AB\Baz', 'A\B\Quux', 'A\B\Baz', 'A\B\Foo', 'AB\Baz'],
            ],
        ];

        yield 'With whitespaces' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[   AB\Baz   (prop: \'baz\')   ]
            #[   A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')   ]
            #[   \A\B\Qux()   ]
            #[   BarAlias   (3)   ]
            #[   Corge   ]
            #[   Foo   (4, \'baz qux\')   ]
            function f() {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[   Foo   (4, \'baz qux\')   ]
            #[   BarAlias   (3)   ]
            #[   AB\Baz   (prop: \'baz\')   ]
            #[   \A\B\Qux()   ]
            #[   A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')   ]
            #[   Corge   ]
            function f() {}
            ',
        ];

        yield 'With docblock' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            /**
             * Start docblock
             */
            /**
             * AB\Baz docblock
             */
            #[AB\Baz(prop: \'baz\')]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[\A\B\Qux()]
            #[BarAlias(3)]
            /**
             * Corge docblock
             */
            #[Corge]
            #[Foo(4, \'baz qux\')]
            /**
             * End docblock
             */
            class X
            {}

            function f2(/** Start docblock */#[\A\B\Qux()] #[BarAlias(3)] /** Corge docblock */#[Corge] #[Foo(4, \'baz qux\')] /** End docblock */string $param) {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            /**
             * Start docblock
             */
            #[Foo(4, \'baz qux\')]
            #[BarAlias(3)]
            /**
             * AB\Baz docblock
             */
            #[AB\Baz(prop: \'baz\')]
            #[\A\B\Qux()]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            /**
             * Corge docblock
             */
            #[Corge]
            /**
             * End docblock
             */
            class X
            {}

            function f2(/** Start docblock */#[Foo(4, \'baz qux\')] #[BarAlias(3)] #[\A\B\Qux()] /** Corge docblock */#[Corge] /** End docblock */string $param) {}
            ',
        ];

        yield 'With comments' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[/* comment */A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\') /* comment */]
            #[ /* comment */ BarAlias/* comment */(3)/* comment */]
            #[/* comment */ Corge/* comment */]
            #[/* comment */AB\Baz /* comment */ (prop: \'baz\') /* comment */ ]
            #[/* comment */Foo/* comment */(4, \'baz qux\') /* comment */ ]
            #[   /* comment */   \A\B\Qux()/* comment */]
            function f() {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[/* comment */Foo/* comment */(4, \'baz qux\') /* comment */ ]
            #[ /* comment */ BarAlias/* comment */(3)/* comment */]
            #[/* comment */AB\Baz /* comment */ (prop: \'baz\') /* comment */ ]
            #[   /* comment */   \A\B\Qux()/* comment */]
            #[/* comment */A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\') /* comment */]
            #[/* comment */ Corge/* comment */]
            function f() {}
            ',
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['Test\A\B\Quux', 'A\B\Bar', 'Test\Corge', 'A\B\Baz', 'A\B\Foo', 'A\B\Qux'],
            ],
        ];

        yield 'Alpha with multiple attributes' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                AB\Baz(prop: \'baz\'),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                \A\B\Qux(),
                BarAlias(3),
                Corge,
                Foo(4, \'baz qux\'),
            ]
            class X
            {
                #[ AB\Baz(prop: \'baz\'), A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'), \A\B\Qux(), BarAlias(3), Corge,Foo(4, \'baz qux\')]
                public function y() {}
            }
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                Foo(4, \'baz qux\'),
                BarAlias(3),
                AB\Baz(prop: \'baz\'),
                \A\B\Qux(),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                Corge,
            ]
            class X
            {
                #[Foo(4, \'baz qux\'), BarAlias(3), AB\Baz(prop: \'baz\'), \A\B\Qux(), A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'), Corge]
                public function y() {}
            }
            ',
        ];

        yield 'Explicit with multiple attributes' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                BarAlias(3),
                Corge,
                AB\Baz(prop: \'baz\'),
                \A\B\Qux(),
                Foo(4, \'baz qux\'),
            ]
            class X
            {
                #[ A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'), BarAlias(3), Corge, AB\Baz(prop: \'baz\'), \A\B\Qux(),Foo(4, \'baz qux\')]
                public function y() {}
            }
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                Foo(4, \'baz qux\'),
                BarAlias(3),
                AB\Baz(prop: \'baz\'),
                \A\B\Qux(),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                Corge,
            ]
            class X
            {
                #[Foo(4, \'baz qux\'), BarAlias(3), AB\Baz(prop: \'baz\'), \A\B\Qux(), A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'), Corge]
                public function y() {}
            }
            ',
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['Test\A\B\Quux', 'A\B\Bar', 'Test\Corge', 'A\B\Baz', 'A\B\Qux'],
            ],
        ];

        yield 'Multiline with no trailing comma' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                AB\Baz(prop: \'baz\'),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                \A\B\Qux(),
                BarAlias(3),
                Corge,
                Foo(4, \'baz qux\')
            ]
            class X
            {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                Foo(4, \'baz qux\'),
                BarAlias(3),
                AB\Baz(prop: \'baz\'),
                \A\B\Qux(),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                Corge
            ]
            class X
            {}
            ',
        ];

        yield 'Multiple with comments' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                /*
                 * AB\Baz comment
                 */
                AB\Baz(prop: \'baz\'),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                \A\B\Qux(),
                BarAlias(3),
                /*
                 * Corge comment
                 */
                Corge,
                /**
                 * Foo docblock
                 */
                Foo(4, \'baz qux\'),
            ]
            class X
            {
                #[ /* AB\Baz comment */AB\Baz(prop: \'baz\'), A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'), \A\B\Qux(), BarAlias(3), /* Corge comment */Corge,/** Foo docblock */Foo(4, \'baz qux\')]
                public function y() {}
            }
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                /**
                 * Foo docblock
                 */
                Foo(4, \'baz qux\'),
                BarAlias(3),
                /*
                 * AB\Baz comment
                 */
                AB\Baz(prop: \'baz\'),
                \A\B\Qux(),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                /*
                 * Corge comment
                 */
                Corge,
            ]
            class X
            {
                #[/** Foo docblock */Foo(4, \'baz qux\'), BarAlias(3), /* AB\Baz comment */AB\Baz(prop: \'baz\'), \A\B\Qux(), A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'), /* Corge comment */Corge]
                public function y() {}
            }
            ',
        ];

        yield 'Alpha with mixed multiple attribute declarations and attributes' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[AB\Baz(prop: \'baz\')]
            #[ A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),Corge]
            #[ \A\B\Qux(), BarAlias(3),Foo(4, \'baz qux\')]
            function f() {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[Foo(4, \'baz qux\'), \A\B\Qux(), BarAlias(3)]
            #[AB\Baz(prop: \'baz\')]
            #[Corge, A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            function f() {}
            ',
        ];

        yield 'Explicit with mixed multiple attribute declarations and attributes' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[ A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),Corge]
            #[ BarAlias(3), \A\B\Qux(),Foo(4, \'baz qux\')]
            #[AB\Baz(prop: \'baz\')]
            function f() {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[Foo(4, \'baz qux\'), \A\B\Qux(), BarAlias(3)]
            #[AB\Baz(prop: \'baz\')]
            #[Corge, A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            function f() {}
            ',
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['Test\A\B\Quux', 'A\B\Bar', 'Test\Corge', 'A\B\Baz', 'A\B\Qux'],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string, 2?: array<string, mixed>}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'Alpha with nested attribute' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[AB\Baz(prop: new P\R())]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[\A\B\Qux()]
            #[BarAlias(3)]
            #[Corge]
            #[Foo(4, \'baz qux\')]
            function f() {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[Foo(4, \'baz qux\')]
            #[BarAlias(3)]
            #[AB\Baz(prop: new P\R())]
            #[\A\B\Qux()]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[Corge]
            function f() {}
            ',
        ];

        yield 'Explicit multiple with nested attribute' => [
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                BarAlias(3),
                Corge,
                AB\Baz(prop: new P\R()),
                Foo(4, \'baz qux\'),
                \A\B\Qux(),
            ]
            function f() {}
            ',
            '<?php
            namespace Test;

            use A\B\Foo;
            use A\B\Bar as BarAlias;
            use A\B as AB;

            #[
                Foo(4, \'baz qux\'),
                BarAlias(3),
                AB\Baz(prop: new P\R()),
                \A\B\Qux(),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                Corge,
            ]
            function f() {}
            ',
            [
                'sort_algorithm' => OrderedAttributesFixer::ORDER_CUSTOM,
                'order' => ['Test\A\B\Quux', 'A\B\Bar', 'Test\Corge', 'A\B\Baz', 'A\B\Foo', 'A\B\Qux'],
            ],
        ];
    }
}
