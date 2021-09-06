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

namespace PhpCsFixer\Tests\Fixer\ListNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tests\Test\TestCaseUtils;

/**
 * @requires PHP 7.1
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer
 */
final class ListSyntaxFixerTest extends AbstractFixerTestCase
{
    public function testFixWithDefaultConfiguration(): void
    {
        $this->fixer->configure([]);
        $this->doTest(
            '<?php $a = [$a, $b] = $a; [$b] = $a;',
            '<?php $a = list($a, $b) = $a; [$b] = $a;'
        );
    }

    /**
     * @dataProvider provideFixToLongSyntaxCases
     */
    public function testFixToLongSyntax(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['syntax' => 'long']);
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixToShortSyntaxCases
     */
    public function testFixToShortSyntax(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['syntax' => 'short']);
        $this->doTest($expected, $input);
    }

    public function provideFixToLongSyntaxCases(): array
    {
        // reverse testing
        $shortCases = $this->provideFixToShortSyntaxCases();
        $cases = [];
        foreach ($shortCases as $label => $shortCase) {
            $cases[$label] = [$shortCase[1], $shortCase[0]];
        }

        // the reverse of this is different because of all the comments and white space,
        // therefore we override with a similar case here
        $cases['comment case'] = [
            '<?php
#
list(#
$a#
)#
=#
$a#
;#',
            '<?php
#
[#
$a#
]#
=#
$a#
;#',
        ];

        $cases[] = ['<?php

class Test
{
    public function updateAttributeKey($key, $value)
    {
        $this->{camel_case($attributes)}[$key] = $value;
    }
}',
        ];

        $cases[] = ['<?php [$b[$a]] = $foo();'];

        return $cases;
    }

    public function provideFixToShortSyntaxCases(): array
    {
        return [
            [
                '<?php [$x] = $a;',
                '<?php list($x) = $a;',
            ],
            [
                '<?php [$a, $b, $c] = $array;',
                '<?php list($a, $b, $c) = $array;',
            ],
            [
                '<?php ["a" => $a, "b" => $b, "c" => $c] = $array;',
                '<?php list("a" => $a, "b" => $b, "c" => $c) = $array;',
            ],
            [
                '<?php
#
[//
    $x] =/**/$a?>',
                '<?php
#
list(//
    $x) =/**/$a?>',
            ],
            'comment case' => [
                '<?php
#a
#g
[#h
#f
$a#
#e
]#
#
=#c
#
$a;#
#
',
                '<?php
#a
list#g
(#h
#f
$a#
#e
)#
#
=#c
#
$a;#
#
',
            ],
            [
                '<?php [$a, $b,, [$c, $d]] = $a;',
                '<?php list($a, $b,, list($c, $d)) = $a;',
            ],
            [
                '<?php [[$a, $b], [$c, $d]] = $a;',
                '<?php list(list($a, $b), list($c, $d)) = $a;',
            ],
            [
                '<?php [[$a, [$b]], [[$c, [$d]]]] = $a;',
                '<?php list(list($a, list($b)), list(list($c, list($d)))) = $a;',
            ],
            [
                '<?php [[$a]] = $foo();',
                '<?php list(list($a)) = $foo();',
            ],
        ];
    }

    /**
     * @requires PHP 7.2
     * @dataProvider provideFixToShortSyntaxPhp72Cases
     */
    public function testFixToShortSyntaxPhp72(string $expected, string $input): void
    {
        $this->fixer->configure(['syntax' => 'short']);
        $this->doTest($expected, $input);
    }

    /**
     * @requires PHP 7.2
     * @dataProvider provideFixToLongSyntaxPhp72Cases
     */
    public function testFixToLongSyntaxPhp72(string $expected, string $input): void
    {
        $this->fixer->configure(['syntax' => 'long']);
        $this->doTest($expected, $input);
    }

    public function provideFixToShortSyntaxPhp72Cases(): \Generator
    {
        yield [
            '<?php [$a, $b,, [$c, $d]] = $a;',
            '<?php list($a, $b,, list($c, $d)) = $a;',
        ];
    }

    public function provideFixToLongSyntaxPhp72Cases(): iterable
    {
        return TestCaseUtils::swapExpectedInputTestCases($this->provideFixToShortSyntaxPhp72Cases());
    }

    /**
     * @requires PHP 7.3
     * @dataProvider provideFixToShortSyntaxPhp73Cases
     */
    public function testFixToShortSyntaxPhp73(string $expected, string $input): void
    {
        $this->fixer->configure(['syntax' => 'short']);
        $this->doTest($expected, $input);
    }

    /**
     * @requires PHP 7.3
     * @dataProvider provideFixToLongSyntaxPhp73Cases
     */
    public function testFixToLongSyntaxPhp73(string $expected, string $input): void
    {
        $this->fixer->configure(['syntax' => 'long']);
        $this->doTest($expected, $input);
    }

    public function provideFixToShortSyntaxPhp73Cases(): \Generator
    {
        yield [
            '<?php [&$a, $b] = $a;',
            '<?php list(&$a, $b) = $a;',
        ];

        yield [
            '<?php [&$a,/* */&$b] = $a;',
            '<?php list(&$a,/* */&$b) = $a;',
        ];

        yield [
            '<?php [&$a, $b,, [&$c, $d]] = $a;',
            '<?php list(&$a, $b,, list(&$c, $d)) = $a;',
        ];
    }

    public function provideFixToLongSyntaxPhp73Cases(): iterable
    {
        return TestCaseUtils::swapExpectedInputTestCases($this->provideFixToShortSyntaxPhp73Cases());
    }

    /**
     * @dataProvider provideFix81Cases
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): \Generator
    {
        yield 'simple 8.1' => [
            '<?php $a = _list(...);',
        ];
    }
}
