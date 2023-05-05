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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Philippe Bouttereux <philippe.bouttereux@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\MultilineLongArrayFixer
 */
final class MultilineLongArrayFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'Old style array' => [
            <<<'EXPECTED'
            <?php
            $foo = array(
            'foo',
            'bar' => 'baz'
            );
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = array('foo','bar' => 'baz');
            INPUT,
        ];

        yield 'Old style array with comments' => [
            <<<'EXPECTED'
            <?php
            $foo = array /* comment */ (
            'foo',
            'bar' => 'baz'
            );
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = array /* comment */ ('foo','bar' => 'baz');
            INPUT,
        ];

        yield 'Empty array with zero max length' => [
            <<<'EXPECTED'
            <?php
            $foo = [];
            EXPECTED,
            null,
            ['max_length' => 0],
        ];

        yield 'Empty array with negative max length' => [
            <<<'EXPECTED'
            <?php
            $foo = [];
            EXPECTED,
            null,
            ['max_length' => -1],
        ];

        yield 'Single-line array' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => 'baz',
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => 'baz',];
            INPUT,
        ];

        yield 'Single-line array shorter than max_length' => [
            <<<'EXPECTED'
            <?php
            $foo = ['foo','bar' => 'baz',];
            EXPECTED,
            null,
            ['max_length' => 30],
        ];

        yield 'Single-line array with negative max_length' => [
            <<<'EXPECTED'
            <?php
            $foo = ['foo','bar' => 'baz',];
            EXPECTED,
            null,
            ['max_length' => -1],
        ];

        yield 'Single line array longer than max_length' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => 'baz',
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => 'baz',];
            INPUT,
            ['max_length' => 10],
        ];

        yield 'Multi line array shorter than max_length.' => [
            <<<'EXPECTED'
            <?php
            $foo = ['foo','bar' => 'baz',];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = [
            'foo',
            'bar' => 'baz',
            ];
            INPUT,
            ['max_length' => 30],
        ];

        yield 'Multi line array shorter than max_length with tabs' => [
            <<<'EXPECTED'
            <?php
            $foo = ['foo','bar' => 'baz',];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = [
                'foo',
                'bar' => 'baz',
            ];
            INPUT,
            ['max_length' => 30],
        ];

        yield 'Multi line array with negative max_length' => [
            <<<'EXPECTED'
            <?php
            $foo = ['foo','bar' => 'baz',];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = [
            'foo',
            'bar' => 'baz',
            ];
            INPUT,
            ['max_length' => -1],
        ];

        yield 'Multi line array longer than max_length' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => 'baz',
            ];
            EXPECTED,
            null,
            ['max_length' => 10],
        ];

        yield 'Single element array shorter than max length' => [
            <<<'EXPECTED'
            <?php
            $foo = ['foo'];
            EXPECTED,
            null,
            ['max_length' => 10],
        ];

        yield 'Single element array longer than max length' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foobarbaz'
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foobarbaz'];
            INPUT,
            ['max_length' => 10],
        ];

        yield 'Space after comma' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => 'baz',
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo', 'bar' => 'baz',];
            INPUT,
        ];

        yield 'Comma after last element' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => 2,
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => 2,];
            INPUT,
        ];

        yield 'No comma after last element' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => 2
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => 2];
            INPUT,
        ];

        yield 'Function and method call in array' => [
            <<<'EXPECTED'
            <?php $foo = [
            'foo',
            'bar' => getFoo(),
            'baz' => $this->getFoo(1)
            ];
            EXPECTED,
            <<<'INPUT'
            <?php $foo = ['foo', 'bar' => getFoo(), 'baz' => $this->getFoo(1)];
            INPUT,
        ];

        yield 'Operators in array' => [
            <<<'EXPECTED'
            <?php $foo = [
            'foo',
            'bar' => $a ?? $b,
            $a === 1
            ];
            EXPECTED,
            <<<'INPUT'
            <?php $foo = ['foo', 'bar' => $a ?? $b, $a === 1];
            INPUT,
        ];

        yield 'Nested arrays' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => [
            'baz' => 'foo'
            ],
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => ['baz' => 'foo'],];
            INPUT,
        ];

        yield 'Multiple nested arrays with max_length' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => ['baz' => ['foo']],
            ['baj']
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => ['baz' => ['foo']], ['baj']];
            INPUT,
            ['max_length' => 15],
        ];

        yield 'Nested arrays 2' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => [
            'baz' => [
            'foo'
            ]
            ],
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => ['baz' => ['foo']],];
            INPUT,
        ];

        yield 'Single line array with brackets inside of a string' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => 'foo is [baz]',
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => 'foo is [baz]',];
            INPUT,
        ];

        yield 'Single line array with arrow function' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => fn($i) => 'baz'
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => fn($i) => 'baz'];
            INPUT,
        ];

        yield 'Single line array with anonymous function' => [
            <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => function( $x ,$y) { return $x + $y; }
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = ['foo','bar' => function( $x ,$y) { return $x + $y; }];
            INPUT,
        ];

        yield 'Internal short arrays inside long one.' => [
            <<<'EXPECTED'
            <?php
            $foo = [
                'foo' => ['short' => $array,],
                'bar' => ['other' => 'short','array' => 3,],
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $foo = [
                'foo' => [
                    'short' => $array,
                ],
                'bar' => [
                    'other' => 'short',
                    'array' => 3,
                ],
            ];
            INPUT,
            ['max_length' => 40],
        ];

        yield 'don\'t change anonymous class implements list but change array inside' => [
            <<<'EXPECTED'
            <?php
            $x = [
            1,
            "2",
            "c" => new class implements Foo, Bar { const FOO = [
            "x",
            "y"
            ]; },
            $y
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $x = [1,  "2","c" => new class implements Foo, Bar { const FOO = ["x","y"]; },$y ];
            INPUT,
        ];

        yield 'don\'t change anonymous class implements list and don\'t change small array inside.' => [
            <<<'EXPECTED'
            <?php
            $x = [
            1,
            "2",
            "c" => new class implements Foo, Bar { const FOO = ["x","y"]; },
            $y
            ];
            EXPECTED,
            <<<'INPUT'
            <?php
            $x = [1, "2", "c" => new class implements Foo, Bar { const FOO = ["x","y"]; },$y ];
            INPUT,
            ['max_length' => 15],
        ];
    }
}
