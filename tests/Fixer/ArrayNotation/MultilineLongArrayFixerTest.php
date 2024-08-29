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
            '<?php
$foo = array(
"foo",
"bar" => "baz"
);',
            '<?php
$foo = array("foo","bar" => "baz");',
        ];

        yield 'Old style array with comments' => [
            '<?php
$foo = array /* comment */ (
"foo",
"bar" => "baz"
);',
            '<?php
$foo = array /* comment */ ("foo","bar" => "baz");',
        ];

        yield 'Empty array with zero max length' => [
            '<?php
$foo = [];',
            null,
            ['max_length' => 0],
        ];

        yield 'Empty array with negative max length' => [
            '<?php
$foo = [];',
            null,
            ['max_length' => -1],
        ];

        yield 'Single-line array' => [
            '<?php
$foo = [
"foo",
"bar" => "baz",
];',
            '<?php
$foo = ["foo","bar" => "baz",];',
        ];

        yield 'Single-line array shorter than max_length' => [
            '<?php
$foo = ["foo","bar" => "baz",];',
            null,
            ['max_length' => 30],
        ];

        yield 'Single line array longer than max_length' => [
            '<?php
$foo = [
"foo",
"bar" => "baz",
];',
            '<?php
$foo = ["foo","bar" => "baz",];',
            ['max_length' => 10],
        ];

        yield 'Multi line array shorter than max_length.' => [
            '<?php
$foo = ["foo","bar" => "baz",];',
            '<?php
$foo = [
"foo",
"bar" => "baz",
];',
            ['max_length' => 30],
        ];

        yield 'Multi line array shorter than max_length with tabs' => [
            '<?php
$foo = ["foo","bar" => "baz",];',
            '<?php
$foo = [
    "foo",
    "bar" => "baz",
];',
            ['max_length' => 30],
        ];

        yield 'Multi line array with negative max_length' => [
            '<?php
$foo = ["foo","bar" => "baz",];',
            '<?php
$foo = [
"foo",
"bar" => "baz",
];',
            ['max_length' => -1],
        ];

        yield 'Multi line array longer than max_length' => [
            '<?php
$foo = [
"foo",
"bar" => "baz",
];',
            null,
            ['max_length' => 10],
        ];

        yield 'Single element array shorter than max length' => [
            '<?php
$foo = ["foo"];',
            null,
            ['max_length' => 10],
        ];

        yield 'Single element array longer than max length' => [
            '<?php
$foo = [
"foobarbaz"
];',
            '<?php
$foo = ["foobarbaz"];',
            ['max_length' => 10],
        ];

        yield 'Space after comma' => [
            '<?php
$foo = [
"foo",
"bar" => "baz",
];',
            '<?php
$foo = ["foo", "bar" => "baz",];',
        ];

        yield 'Comma after last element' => [
            '<?php
$foo = [
"foo",
"bar" => 2,
];',
            '<?php
$foo = ["foo","bar" => 2,];',
        ];

        yield 'No comma after last element' => [
            '<?php
$foo = [
"foo",
"bar" => 2
];',
            '<?php
$foo = ["foo","bar" => 2];',
        ];

        yield 'Function and method call in array' => [
            '<?php
$foo = [
"foo",
"bar" => getFoo(),
"baz" => $this->getFoo(1)
];',
            '<?php
$foo = ["foo", "bar" => getFoo(), "baz" => $this->getFoo(1)];',
        ];

        yield 'Operators in array' => [
            '<?php
$foo = [
"foo",
"bar" => $a ?? $b,
$a === 1
];',
            '<?php
$foo = ["foo", "bar" => $a ?? $b, $a === 1];',
        ];

        yield 'Nested arrays' => [
            '<?php
$foo = [
"foo",
"bar" => [
"baz" => "foo"
],
];',
            '<?php
$foo = ["foo","bar" => ["baz" => "foo"],];',
        ];

        yield 'Multiple nested arrays with max_length' => [
            '<?php
$foo = [
"foo",
"bar" => ["baz" => ["foo"]],
["baj"]
];',
            '<?php
$foo = ["foo","bar" => ["baz" => ["foo"]], ["baj"]];',
            ['max_length' => 15],
        ];

        yield 'Nested arrays 2' => [
            '<?php
$foo = [
"foo",
"bar" => [
"baz" => [
"foo"
]
],
];',
            '<?php
$foo = ["foo","bar" => ["baz" => ["foo"]],];',
        ];

        yield 'Single line array with brackets inside of a string' => [
            '<?php
$foo = [
"foo",
"bar" => "foo is [baz]",
];',
            '<?php
$foo = ["foo","bar" => "foo is [baz]",];',
        ];

        yield 'Single line array with arrow function' => [
            '<?php
$foo = [
"foo",
"bar" => fn($i) => "baz"
];',
            '<?php
$foo = ["foo","bar" => fn($i) => "baz"];',
        ];

        yield 'Single line array with anonymous function' => [
            '<?php
$foo = [
"foo",
"bar" => function( $x ,$y) { return $x + $y; }
];',
            '<?php
$foo = ["foo","bar" => function( $x ,$y) { return $x + $y; }];',
        ];

        yield 'Internal short arrays inside long one.' => [
            '<?php
$foo = [
    "foo" => ["short" => $array,],
    "bar" => ["other" => "short","array" => 3,],
];',
            '<?php
$foo = [
    "foo" => [
        "short" => $array,
    ],
    "bar" => [
        "other" => "short",
        "array" => 3,
    ],
];',
            ['max_length' => 40],
        ];

        yield 'don\'t change anonymous class implements list but change array inside' => [
            '<?php
$x = [
1,
"2",
"c" => new class implements Foo, Bar { const FOO = [
"x",
"y"
]; },
$y
];',
            '<?php
$x = [1,  "2","c" => new class implements Foo, Bar { const FOO = ["x","y"]; },$y ];',
        ];

        yield 'don\'t change anonymous class implements list and don\'t change small array inside.' => [
            '<?php
$x = [
1,
"2",
"c" => new class implements Foo, Bar { const FOO = ["x","y"]; },
$y
];',
            '<?php
$x = [1, "2", "c" => new class implements Foo, Bar { const FOO = ["x","y"]; },$y ];',
            ['max_length' => 15],
        ];

        yield 'Comment in single-line array' => [
            '<?php
$letters = [
"a",
/* @todo: add some other letters one day */ "z"
];',
            '<?php
$letters = ["a", /* @todo: add some other letters one day */ "z"];',
            ['max_length' => 20],
        ];

        yield 'Comments in multi-line array' => [
            '<?php
$letters = [
"a",
// This is the letter a
"z",
/** This is not the letter a */
];',
            '<?php
$letters = [
"a", // This is the letter a
"z", /** This is not the letter a */
];',
            ['max_length' => 20],
        ];
    }
}
