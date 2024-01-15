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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author HypeMC <hypemc@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AttributeNotation\AttributeEmptyParenthesesFixer
 *
 * @requires PHP 8.0
 */
final class AttributeEmptyParenthesesFixerTest extends AbstractFixerTestCase
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

    public static function provideFixCases(): iterable
    {
        yield 'Without parentheses on various declarations' => [
            <<<'EOD'
                <?php
                            namespace Test;

                            #[\A\B\Foo]
                            #[\Bar      ]
                            #[Baz]
                            #[\Bar, Baz]
                            #[Corge(4, 'baz qux')]
                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[\A\B\Foo, \Bar      , Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[
                                \A\B\Foo,
                                \Bar      ,
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                            ]
                            class X
                            {
                                #[\A\B\Foo]
                                #[\Bar      ]
                                #[Baz]
                                #[\Bar, Baz]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo, \Bar      , Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo,
                                    \Bar      ,
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                const Y = 1;

                                #[\A\B\Foo]
                                #[\Bar      ]
                                #[Baz]
                                #[\Bar, Baz]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo, \Bar      , Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo,
                                    \Bar      ,
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                public $y;

                                #[\A\B\Foo]
                                #[\Bar      ]
                                #[Baz]
                                #[\Bar, Baz]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo, \Bar      , Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo,
                                    \Bar      ,
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                public function y() {}
                            }

                            #[\A\B\Foo]
                            #[\Bar      ]
                            #[Baz]
                            #[\Bar, Baz]
                            #[Corge(4, 'baz qux')]
                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[\A\B\Foo, \Bar      , Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[
                                \A\B\Foo,
                                \Bar      ,
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                            ]
                            function f(
                                #[\A\B\Foo]
                                #[\Bar      ]
                                #[Baz]
                                #[\Bar, Baz]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo, \Bar      , Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo,
                                    \Bar      ,
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                string $param,
                            ) {}

                            $anon = #[\A\B\Foo] #[\Bar      ] #[Baz] #[Corge(4, 'baz qux')] #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')] function () {};
                            $short = #[\A\B\Foo] #[\Bar      ] #[Baz] #[Corge(4, 'baz qux')] #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')] fn () => null;
                EOD."\n            ",
            <<<'EOD'
                <?php
                            namespace Test;

                            #[\A\B\Foo()]
                            #[\Bar(      )]
                            #[Baz]
                            #[\Bar(), Baz()]
                            #[Corge(4, 'baz qux')]
                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[\A\B\Foo(), \Bar(      ), Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[
                                \A\B\Foo(),
                                \Bar(      ),
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                            ]
                            class X
                            {
                                #[\A\B\Foo()]
                                #[\Bar(      )]
                                #[Baz]
                                #[\Bar(), Baz()]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar(      ), Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar(      ),
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                const Y = 1;

                                #[\A\B\Foo()]
                                #[\Bar(      )]
                                #[Baz]
                                #[\Bar(), Baz()]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar(      ), Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar(      ),
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                public $y;

                                #[\A\B\Foo()]
                                #[\Bar(      )]
                                #[Baz]
                                #[\Bar(), Baz()]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar(      ), Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar(      ),
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                public function y() {}
                            }

                            #[\A\B\Foo()]
                            #[\Bar(      )]
                            #[Baz]
                            #[\Bar(), Baz()]
                            #[Corge(4, 'baz qux')]
                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[\A\B\Foo(), \Bar(      ), Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[
                                \A\B\Foo(),
                                \Bar(      ),
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                            ]
                            function f(
                                #[\A\B\Foo()]
                                #[\Bar(      )]
                                #[Baz]
                                #[\Bar(), Baz()]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar(      ), Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar(      ),
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                string $param,
                            ) {}

                            $anon = #[\A\B\Foo()] #[\Bar(      )] #[Baz] #[Corge(4, 'baz qux')] #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')] function () {};
                            $short = #[\A\B\Foo()] #[\Bar(      )] #[Baz] #[Corge(4, 'baz qux')] #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')] fn () => null;
                EOD."\n            ",
        ];

        yield 'With parentheses on various declarations' => [
            <<<'EOD'
                <?php
                            namespace Test;

                            #[\A\B\Foo()]
                            #[\Bar()]
                            #[Baz()]
                            #[\Bar(), Baz()]
                            #[Corge(4, 'baz qux')]
                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[\A\B\Foo(), \Bar(), Baz(), Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[
                                \A\B\Foo(),
                                \Bar(),
                                Baz(),
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                            ]
                            class X
                            {
                                #[\A\B\Foo()]
                                #[\Bar()]
                                #[Baz()]
                                #[\Bar(), Baz()]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar(), Baz(), Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar(),
                                    Baz(),
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                const Y = 1;

                                #[\A\B\Foo()]
                                #[\Bar()]
                                #[Baz()]
                                #[\Bar(), Baz()]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar(), Baz(), Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar(),
                                    Baz(),
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                public $y;

                                #[\A\B\Foo()]
                                #[\Bar()]
                                #[Baz()]
                                #[\Bar(), Baz()]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar(), Baz(), Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar(),
                                    Baz(),
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                public function y() {}
                            }

                            #[\A\B\Foo()]
                            #[\Bar()]
                            #[Baz()]
                            #[\Bar(), Baz()]
                            #[Corge(4, 'baz qux')]
                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[\A\B\Foo(), \Bar(), Baz(), Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[
                                \A\B\Foo(),
                                \Bar(),
                                Baz(),
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                            ]
                            function f(
                                #[\A\B\Foo()]
                                #[\Bar()]
                                #[Baz()]
                                #[\Bar(), Baz()]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar(), Baz(), Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar(),
                                    Baz(),
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                string $param,
                            ) {}

                            $anon = #[\A\B\Foo()] #[\Bar()] #[Baz()] #[Corge(4, 'baz qux')] #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')] function () {};
                            $short = #[\A\B\Foo()] #[\Bar()] #[Baz()] #[Corge(4, 'baz qux')] #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')] fn () => null;
                EOD."\n            ",
            <<<'EOD'
                <?php
                            namespace Test;

                            #[\A\B\Foo()]
                            #[\Bar]
                            #[Baz]
                            #[\Bar, Baz]
                            #[Corge(4, 'baz qux')]
                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[\A\B\Foo(), \Bar, Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[
                                \A\B\Foo(),
                                \Bar,
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                            ]
                            class X
                            {
                                #[\A\B\Foo()]
                                #[\Bar]
                                #[Baz]
                                #[\Bar, Baz]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar, Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar,
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                const Y = 1;

                                #[\A\B\Foo()]
                                #[\Bar]
                                #[Baz]
                                #[\Bar, Baz]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar, Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar,
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                public $y;

                                #[\A\B\Foo()]
                                #[\Bar]
                                #[Baz]
                                #[\Bar, Baz]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar, Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar,
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                public function y() {}
                            }

                            #[\A\B\Foo()]
                            #[\Bar]
                            #[Baz]
                            #[\Bar, Baz]
                            #[Corge(4, 'baz qux')]
                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[\A\B\Foo(), \Bar, Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                            #[
                                \A\B\Foo(),
                                \Bar,
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                            ]
                            function f(
                                #[\A\B\Foo()]
                                #[\Bar]
                                #[Baz]
                                #[\Bar, Baz]
                                #[Corge(4, 'baz qux')]
                                #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[\A\B\Foo(), \Bar, Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')]
                                #[
                                    \A\B\Foo(),
                                    \Bar,
                                    Baz,
                                    Corge(4, 'baz qux'),
                                    A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar'),
                                ]
                                string $param,
                            ) {}

                            $anon = #[\A\B\Foo()] #[\Bar] #[Baz] #[Corge(4, 'baz qux')] #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')] function () {};
                            $short = #[\A\B\Foo()] #[\Bar] #[Baz] #[Corge(4, 'baz qux')] #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar')] fn () => null;
                EOD."\n            ",
            ['use_parentheses' => true],
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

    public static function provideFix81Cases(): iterable
    {
        yield 'Without parentheses' => [
            <<<'EOD'
                <?php
                            namespace Test;

                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R())]
                            #[\A\B\Foo, \Bar      , Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R())]
                            #[
                                \A\B\Foo,
                                \Bar      ,
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R()),
                            ]
                            class X
                            {}
                EOD."\n            ",
            <<<'EOD'
                <?php
                            namespace Test;

                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R())]
                            #[\A\B\Foo(), \Bar(      ), Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R())]
                            #[
                                \A\B\Foo(),
                                \Bar(      ),
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R()),
                            ]
                            class X
                            {}
                EOD."\n            ",
        ];

        yield 'With parentheses' => [
            <<<'EOD'
                <?php
                            namespace Test;

                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R())]
                            #[\A\B\Foo(), \Bar(), Baz(), Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R())]
                            #[
                                \A\B\Foo(),
                                \Bar(),
                                Baz(),
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R()),
                            ]
                            class X
                            {}
                EOD."\n            ",
            <<<'EOD'
                <?php
                            namespace Test;

                            #[A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R())]
                            #[\A\B\Foo(), \Bar, Baz, Corge(4, 'baz qux'), A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R())]
                            #[
                                \A\B\Foo(),
                                \Bar,
                                Baz,
                                Corge(4, 'baz qux'),
                                A\B\Qux(prop1: [1, 2, 4], prop2: true, prop3: 'foo bar', prop4: new P\R()),
                            ]
                            class X
                            {}
                EOD."\n            ",
            ['use_parentheses' => true],
        ];
    }
}
