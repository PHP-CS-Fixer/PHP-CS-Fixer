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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer
 */
final class ArrayIndentationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield from self::withLongArraySyntaxCases([
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        'foo',
                        'bar' => 'baz',
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      'foo',
                            'bar' => 'baz',
                     ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                        $foo = [
                            'foo',
                            'bar' => 'baz',
                        ];
                    EOD,
                <<<'EOD'
                    <?php
                        $foo = [
                      'foo',
                            'bar' => 'baz',
                     ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        ['bar', 'baz'],
                        [
                            'bar',
                            'baz'
                        ],
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                            ['bar', 'baz'],
                         [
                            'bar',
                             'baz'
                             ],
                     ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        ['foo',
                            'bar',
                            ['foo',
                                'bar',
                                ['foo',
                                    'bar',
                                    'baz']],
                            'baz'],
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                     ['foo',
                      'bar',
                      ['foo',
                       'bar',
                       ['foo',
                        'bar',
                        'baz']],
                      'baz'],
                     ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    class Foo
                    {
                        public $foo = [
                            ['bar', 'baz'],
                            [
                                'bar',
                                'baz'
                            ],
                        ];

                        public function bar()
                        {
                            return [
                                ['bar', 'baz'],
                                [
                                    'bar',
                                    'baz'
                                ],
                            ];
                        }
                    }
                    EOD,
                <<<'EOD'
                    <?php
                    class Foo
                    {
                        public $foo = [
                            ['bar', 'baz'],
                         [
                            'bar',
                             'baz'
                             ],
                     ];

                        public function bar()
                        {
                            return [
                                    ['bar', 'baz'],
                                 [
                                    'bar',
                                     'baz'
                                     ],
                             ];
                        }
                    }
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        'foo' => foo(
                                   1,
                                    2
                                 ),
                        'bar' => bar(
                                   1,
                                    2
                                 ),
                        'baz' => baz(
                                   1,
                                    2
                                 ),
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                       'foo' => foo(
                                  1,
                                   2
                                ),
                          'bar' => bar(
                                     1,
                                      2
                                   ),
                             'baz' => baz(
                                        1,
                                         2
                                      ),
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        'foo' => ['bar' => [
                            'baz',
                        ]],
                        'qux',
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      'foo' => ['bar' => [
                       'baz',
                      ]],
                      'qux',
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        'foo' => [
                            (new Foo())
                                       ->foo(),
                        ],
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      'foo' => [
                         (new Foo())
                                    ->foo(),
                      ],
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        [new Foo(
                                )],
                        [new Foo(
                                )],
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                          [new Foo(
                                  )],
                      [new Foo(
                              )],
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = new Foo([
                        (new Bar())
                            ->foo([
                                'foo',
                                'foo',
                            ])
                             ->bar(['bar', 'bar'])
                              ->baz([
                                  'baz',
                                  'baz',
                              ])
                        ,
                    ]);
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = new Foo([
                                   (new Bar())
                                       ->foo([
                                               'foo',
                                'foo',
                                   ])
                                        ->bar(['bar', 'bar'])
                                         ->baz([
                                               'baz',
                                'baz',
                                   ])
                             ,
                    ]);
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php

                    class Foo
                    {
                        public function bar()
                        {
                            $foo = [
                                'foo',
                                'foo',
                            ];

                            $bar = [
                                'bar',
                                'bar',
                            ];
                        }
                    }
                    EOD,
                <<<'EOD'
                    <?php

                    class Foo
                    {
                        public function bar()
                        {
                            $foo = [
                                  'foo',
                             'foo',
                        ];

                            $bar = [
                      'bar',
                        'bar',
                                     ];
                        }
                    }
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php

                    class Foo
                    {
                        public function bar()
                        {
                            return new Bar([
                                (new Baz())
                                    ->qux([function ($a) {
                                        foreach ($a as $b) {
                                            if ($b) {
                                                throw new Exception(sprintf(
                                                    'Oops: %s',
                                                    $b
                                                ));
                                            }
                                        }
                                    }]),
                            ]);
                        }
                    }

                    EOD,
                <<<'EOD'
                    <?php

                    class Foo
                    {
                        public function bar()
                        {
                            return new Bar([
                    (new Baz())
                        ->qux([function ($a) {
                            foreach ($a as $b) {
                                if ($b) {
                                    throw new Exception(sprintf(
                                        'Oops: %s',
                                        $b
                                    ));
                                }
                            }
                        }]),
                            ]);
                        }
                    }

                    EOD,
            ],
            [
                <<<'EOD'
                    <?php

                    $foo = [
                        'Foo'.
                            foo()
                            .bar()
                        ,
                    ];
                    EOD,
                <<<'EOD'
                    <?php

                    $foo = [
                      'Foo'.
                          foo()
                          .bar()
                    ,
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        [new \stdClass()],
                        'foo',
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      [new \stdClass()],
                     'foo',
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        $bar
                            ? 'bar'
                            : 'foo'
                        ,
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      $bar
                          ? 'bar'
                          : 'foo'
                          ,
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        $bar ?
                            'bar' :
                            'foo'
                        ,
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      $bar ?
                          'bar' :
                          'foo'
                          ,
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php

                    $foo = [
                        [
                            'foo',
                        ], [
                            'bar',
                        ],
                    ];
                    EOD,
                <<<'EOD'
                    <?php

                    $foo = [
                          [
                                   'foo',
                     ], [
                       'bar',
                      ],
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        'foo', // comment
                        'bar',
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      'foo', // comment
                    'bar',
                     ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [[[
                        'foo',
                        'bar',
                    ],
                    ],
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [[[
                      'foo',
                    'bar',
                    ],
                     ],
                      ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        [
                            [
                                'foo',
                                'bar',
                            ]]];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                    [
                    [
                        'foo',
                        'bar',
                     ]]];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        [
                            [[
                                [[[
                                    'foo',
                                    'bar',
                                ]
                                ]]
                            ]
                            ]
                        ]];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                    [
                    [[
                    [[[
                        'foo',
                        'bar',
                     ]
                    ]]
                    ]
                     ]
                    ]];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [[
                        [
                            [[[],[[
                                [[[
                                    'foo',
                                    'bar',
                                ],[[],[]]]
                                ]],[
                                    'baz',
                                ]]
                            ],[]],[]]
                    ]
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [[
                    [
                    [[[],[[
                    [[[
                    'foo',
                    'bar',
                    ],[[],[]]]
                    ]],[
                    'baz',
                    ]]
                    ],[]],[]]
                    ]
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php if ($foo): ?>
                        <?php foo([
                            'bar',
                            'baz',
                        ]) ?>
                    <?php endif ?>
                    EOD,
                <<<'EOD'
                    <?php if ($foo): ?>
                        <?php foo([
                              'bar',
                          'baz',
                       ]) ?>
                    <?php endif ?>
                    EOD,
            ],
            [
                <<<'EOD'
                    <div>
                        <a
                            class="link"
                            href="<?= Url::to([
                                '/site/page',
                                'id' => 123,
                            ]); ?>"
                        >
                            Link text
                        </a>
                    </div>
                    EOD,
                <<<'EOD'
                    <div>
                        <a
                            class="link"
                            href="<?= Url::to([
                                  '/site/page',
                              'id' => 123,
                        ]); ?>"
                        >
                            Link text
                        </a>
                    </div>
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $arr = [
                        'a' => 'b',

                        //  'c' => 'd',
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $arr = [
                        'a' => 'b',

                    //  'c' => 'd',
                    ];
                    EOD,
            ],
            [
                '<?php
    '.'
$foo = [
    "foo",
    "bar",
];',
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        'foo' =>
                              'Some'
                               .' long'
                                .' string',
                        'bar' =>
                            'Another'
                             .' long'
                              .' string'
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      'foo' =>
                            'Some'
                             .' long'
                              .' string',
                            'bar' =>
                                'Another'
                                 .' long'
                                  .' string'
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        $test
                              ? [
                                  123,
                              ]
                              : [
                                  321,
                              ],
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                        $test
                              ? [
                                     123,
                              ]
                              : [
                                       321,
                              ],
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [[
                        new Foo(
                            'foo'
                        ),
                    ]];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [[
                          new Foo(
                              'foo'
                          ),
                    ]];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $array = [
                        'foo' => [
                            'bar' => [
                                'baz',
                            ],
                        ], // <- this one
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $array = [
                        'foo' => [
                            'bar' => [
                                'baz',
                            ],
                    ], // <- this one
                    ];
                    EOD,
            ],
            [
                <<<'EOD'
                    <?php
                    $foo = [
                        ...$foo,
                        ...$bar,
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      ...$foo,
                            ...$bar,
                     ];
                    EOD,
            ],
        ]);

        yield 'array destructuring' => [
            <<<'EOD'
                    <?php
                    [
                        $foo,
                        $bar,
                        $baz
                    ] = $arr;
                EOD,
            <<<'EOD'
                    <?php
                    [
                    $foo,
                                $bar,
                      $baz
                    ] = $arr;
                EOD,
        ];

        yield 'array destructuring using list' => [
            <<<'EOD'
                    <?php
                    list(
                        $foo,
                        $bar,
                        $baz
                    ) = $arr;
                EOD,
            <<<'EOD'
                    <?php
                    list(
                    $foo,
                                $bar,
                      $baz
                    ) = $arr;
                EOD,
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t"));
        $this->doTest($expected, $input);
    }

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield from self::withLongArraySyntaxCases([
            [
                <<<EOD
                    <?php
                    \$foo = [
                    \t'foo',
                    \t'bar' => 'baz',
                    ];
                    EOD,
                <<<'EOD'
                    <?php
                    $foo = [
                      'foo',
                            'bar' => 'baz',
                     ];
                    EOD,
            ],
            [
                <<<EOD
                    <?php
                    \$foo = [
                    \t'foo',
                    \t'bar' => 'baz',
                    ];
                    EOD,
                <<<EOD
                    <?php
                    \$foo = [
                    \t\t\t'foo',
                    \t\t'bar' => 'baz',
                     ];
                    EOD,
            ],
        ]);

        yield 'array destructuring' => [
            <<<EOD
                    <?php
                    [
                    \t\$foo,
                    \t\$bar,
                    \t\$baz
                    ] = \$arr;
                EOD,
            <<<'EOD'
                    <?php
                    [
                    $foo,
                                $bar,
                      $baz
                    ] = $arr;
                EOD,
        ];

        yield 'array destructuring using list' => [
            <<<EOD
                    <?php
                    list(
                    \t\$foo,
                    \t\$bar,
                    \t\$baz
                    ) = \$arr;
                EOD,
            <<<'EOD'
                    <?php
                    list(
                    $foo,
                                $bar,
                      $baz
                    ) = $arr;
                EOD,
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'attribute' => [
            '<?php
class Foo {
 #[SimpleAttribute]
#[ComplexAttribute(
 foo: true,
    bar: [
        1,
        2,
        3,
    ]
 )]
  public function bar()
     {
     }
}',
            '<?php
class Foo {
 #[SimpleAttribute]
#[ComplexAttribute(
 foo: true,
    bar: [
                1,
                    2,
              3,
     ]
 )]
  public function bar()
     {
     }
}',
        ];
    }

    /**
     * @param list<array{0: string, 1?: string}> $cases
     *
     * @return list<array{0: string, 1?: string}>
     */
    private static function withLongArraySyntaxCases(array $cases): array
    {
        $longSyntaxCases = [];

        foreach ($cases as $case) {
            $case[0] = self::toLongArraySyntax($case[0]);
            if (isset($case[1])) {
                $case[1] = self::toLongArraySyntax($case[1]);
            }

            $longSyntaxCases[] = $case;
        }

        return [...$cases, ...$longSyntaxCases];
    }

    private static function toLongArraySyntax(string $php): string
    {
        return strtr($php, [
            '[' => 'array(',
            ']' => ')',
        ]);
    }
}
