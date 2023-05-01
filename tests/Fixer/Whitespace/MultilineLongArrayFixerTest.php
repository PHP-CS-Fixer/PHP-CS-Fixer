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

/**
 * @author Philippe Bouttereux <philippe.bouttereux@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\RemoveCommentsFixer
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

    public static function provideFixCases(): array
    {
        return [
            // Old style array
            [
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
            ],
            // Old style array with comments
            [
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
            ],
            [
                // Empty array
                <<<'EXPECTED'
            <?php
            $foo = [];
            EXPECTED,
                null,
                ['max_length' => 0],
            ],
            [
                // Empty array
                <<<'EXPECTED'
            <?php
            $foo = [];
            EXPECTED,
                null,
                ['max_length' => -1],
            ],
            [
                // Single-line array
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
            ],
            [
                // Single-line array shorter than max_length
                <<<'EXPECTED'
            <?php
            $foo = ['foo','bar' => 'baz',];
            EXPECTED,
                null,
                ['max_length' => 30],
            ],
            [
                // Single-line array with negative max_length
                <<<'EXPECTED'
            <?php
            $foo = ['foo','bar' => 'baz',];
            EXPECTED,
                null,
                ['max_length' => -1],
            ],
            [
                // Single line array longer than max_length
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
            ],
            [
                // Multi line array shorter than max_length.
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
            ],
            [
                // Multi line array shorter than max_length with tabs
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
            ],
            [
                // Multi line array with negative max_length
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
            ],
            [
                // Multi line array longer than max_length
                <<<'EXPECTED'
            <?php
            $foo = [
            'foo',
            'bar' => 'baz',
            ];
            EXPECTED,
                null,
                ['max_length' => 10],
            ],
            [
                // Single element array shorter than max length
                <<<'EXPECTED'
            <?php
            $foo = ['foo'];
            EXPECTED,
                null,
                ['max_length' => 10],
            ],
            [
                // Single element array longer than max length
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
            ],
            [
                // Space after comma
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
            ],
            [
                // Comma after last element
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
            ],
            [
                // No comma after last element
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
            ],
            // Function and method call in array
            [
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
            ],
            // Operators in array
            [
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
            ],
            [
                // Nested arrays
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
            ],
            [
                // Multiple nested arrays with max_length
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
            ],
            [
                // Nested arrays 2
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
            ],
            [
                // Single line array with brackets inside of a string
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
            ],
            [
                // Single line array with arrow function
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
            ],
            [
                // Single line array with anonymous function
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
            ],
            [
                // Internal short arrays inside long one.
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
            ],
            // don't change anonymous class implements list but change array inside
            [
                <<<'EXPECTED'
            <?php
            $x = [
            1,
            "2",
            "c" => new class implements Foo ,Bar { const FOO = [
            "x",
            "y"
            ]; },
            $y
            ];
            EXPECTED,
                <<<'INPUT'
            <?php
            $x = [1,  "2","c" => new class implements Foo ,Bar { const FOO = ["x","y"]; },$y ];
            INPUT,
            ],
            // don't change anonymous class implements list and don't change small array inside.
            [
                <<<'EXPECTED'
            <?php
            $x = [
            1,
            "2",
            "c" => new class implements Foo ,Bar { const FOO = ["x","y"]; },
            $y
            ];
            EXPECTED,
                <<<'INPUT'
            <?php
            $x = [1,  "2","c" => new class implements Foo ,Bar { const FOO = ["x","y"]; },$y ];
            INPUT,
                ['max_length' => 15],
            ],
        ];
    }
}
