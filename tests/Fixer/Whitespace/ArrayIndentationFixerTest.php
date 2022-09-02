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

    public function provideFixCases(): array
    {
        return $this->withLongArraySyntaxCases([
            [
                <<<'EXPECTED'
<?php
$foo = [
    'foo',
    'bar' => 'baz',
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  'foo',
        'bar' => 'baz',
 ];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
    $foo = [
        'foo',
        'bar' => 'baz',
    ];
EXPECTED
                ,
                <<<'INPUT'
<?php
    $foo = [
  'foo',
        'bar' => 'baz',
 ];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    ['bar', 'baz'],
    [
        'bar',
        'baz'
    ],
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
        ['bar', 'baz'],
     [
        'bar',
         'baz'
         ],
 ];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    'foo' => ['bar' => [
        'baz',
    ]],
    'qux',
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  'foo' => ['bar' => [
   'baz',
  ]],
  'qux',
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    'foo' => [
        (new Foo())
                   ->foo(),
    ],
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  'foo' => [
     (new Foo())
                ->foo(),
  ],
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    [new Foo(
            )],
    [new Foo(
            )],
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
      [new Foo(
              )],
  [new Foo(
          )],
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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

EXPECTED
                ,
                <<<'INPUT'
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

INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php

$foo = [
    'Foo'.
        foo()
        .bar()
    ,
];
EXPECTED
                ,
                <<<'INPUT'
<?php

$foo = [
  'Foo'.
      foo()
      .bar()
,
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    [new \stdClass()],
    'foo',
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  [new \stdClass()],
 'foo',
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    $bar
        ? 'bar'
        : 'foo'
    ,
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  $bar
      ? 'bar'
      : 'foo'
      ,
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    $bar ?
        'bar' :
        'foo'
    ,
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  $bar ?
      'bar' :
      'foo'
      ,
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php

$foo = [
    [
        'foo',
    ], [
        'bar',
    ],
];
EXPECTED
                ,
                <<<'INPUT'
<?php

$foo = [
      [
               'foo',
 ], [
   'bar',
  ],
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    'foo', // comment
    'bar',
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  'foo', // comment
'bar',
 ];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [[[
    'foo',
    'bar',
],
],
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [[[
  'foo',
'bar',
],
 ],
  ];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    [
        [
            'foo',
            'bar',
        ]]];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
[
[
    'foo',
    'bar',
 ]]];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php if ($foo): ?>
    <?php foo([
        'bar',
        'baz',
    ]) ?>
<?php endif ?>
EXPECTED
                ,
                <<<'INPUT'
<?php if ($foo): ?>
    <?php foo([
          'bar',
      'baz',
   ]) ?>
<?php endif ?>
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$arr = [
    'a' => 'b',

    //  'c' => 'd',
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$arr = [
    'a' => 'b',

//  'c' => 'd',
];
INPUT
                ,
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
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
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
EXPECTED
                ,
                <<<'INPUT'
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
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [[
    new Foo(
        'foo'
    ),
]];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [[
      new Foo(
          'foo'
      ),
]];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$array = [
    'foo' => [
        'bar' => [
            'baz',
        ],
    ], // <- this one
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$array = [
    'foo' => [
        'bar' => [
            'baz',
        ],
], // <- this one
];
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
$foo = [
    ...$foo,
    ...$bar,
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  ...$foo,
        ...$bar,
 ];
INPUT
                ,
            ],
        ]);
    }

    /**
     * @dataProvider provideFixWithTabsCases
     */
    public function testFixWithTabs(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t"));
        $this->doTest($expected, $input);
    }

    public function provideFixWithTabsCases(): array
    {
        return $this->withLongArraySyntaxCases([
            [
                <<<EXPECTED
<?php
\$foo = [
\t'foo',
\t'bar' => 'baz',
];
EXPECTED
                ,
                <<<'INPUT'
<?php
$foo = [
  'foo',
        'bar' => 'baz',
 ];
INPUT
                ,
            ],
            [
                <<<EXPECTED
<?php
\$foo = [
\t'foo',
\t'bar' => 'baz',
];
EXPECTED
                ,
                <<<INPUT
<?php
\$foo = [
\t\t\t'foo',
\t\t'bar' => 'baz',
 ];
INPUT
                ,
            ],
        ]);
    }

    /**
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPhp80Cases(): iterable
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
    private function withLongArraySyntaxCases(array $cases): array
    {
        $longSyntaxCases = [];

        foreach ($cases as $case) {
            $case[0] = $this->toLongArraySyntax($case[0]);
            if (isset($case[1])) {
                $case[1] = $this->toLongArraySyntax($case[1]);
            }

            $longSyntaxCases[] = $case;
        }

        return array_merge($cases, $longSyntaxCases);
    }

    private function toLongArraySyntax(string $php): string
    {
        return strtr($php, [
            '[' => 'array(',
            ']' => ')',
        ]);
    }
}
