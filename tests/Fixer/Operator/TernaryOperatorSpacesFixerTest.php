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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer
 */
final class TernaryOperatorSpacesFixerTest extends AbstractFixerTestCase
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
        yield 'handle goto labels 1' => [
            '<?php
beginning:
echo $guard ? 1 : 2;',
            '<?php
beginning:
echo $guard?1:2;',
        ];

        yield 'handle goto labels 2' => [
            '<?php
function A(){}
beginning:
echo $guard ? 1 : 2;',
            '<?php
function A(){}
beginning:
echo $guard?1:2;',
        ];

        yield 'handle goto labels 3' => [
            '<?php
;
beginning:
echo $guard ? 1 : 2;',
            '<?php
;
beginning:
echo $guard?1:2;',
        ];

        yield 'handle goto labels 4' => [
            '<?php
{
beginning:
echo $guard ? 1 : 2;}',
            '<?php
{
beginning:
echo $guard?1:2;}',
        ];

        yield [
            '<?php $a = $a ? 1 : 0;',
            '<?php $a = $a  ? 1 : 0;',
        ];

        yield [
            '<?php $a = $a ?
#
: $b;',
        ];

        yield [
            '<?php $a = $a#
 ? '.'
#
1 : 0;',
        ];

        yield [
            '<?php $val = (1===1) ? true : false;',
            '<?php $val = (1===1)?true:false;',
        ];

        yield [
            '<?php $val = 1===1 ? true : false;',
            '<?php $val = 1===1?true:false;',
        ];

        yield [
            '<?php
$a = $b ? 2 : ($bc ? 2 : 3);
$a = $bc ? 2 : 3;',
            '<?php
$a = $b   ?   2  :    ($bc?2:3);
$a = $bc?2:3;',
        ];

        yield [
            '<?php $config = $config ?: new Config();',
            '<?php $config = $config ? : new Config();',
        ];

        yield [
            '<?php
$a = $b ? (
        $c + 1
    ) : (
        $d + 1
    );',
        ];

        yield [
            '<?php
$a = $b
    ? $c
    : $d;',
            '<?php
$a = $b
    ?$c
    :$d;',
        ];

        yield [
            '<?php
$a = $b  //
    ? $c  /**/
    : $d;',
            '<?php
$a = $b  //
    ?$c  /**/
    :$d;',
        ];

        yield [
            '<?php
$a = ($b
    ? $c
    : ($d
        ? $e
        : $f
    )
);',
        ];

        yield [
            '<?php
$a = ($b
    ? ($c1 ? $c2 : ($c3a ?: $c3b))
    : ($d1 ? $d2 : $d3)
);',
            '<?php
$a = ($b
    ? ($c1?$c2:($c3a? :$c3b))
    : ($d1?$d2:$d3)
);',
        ];

        yield [
            '<?php
                $foo = $isBar ? 1 : 2;
                switch ($foo) {
                    case 1: return 3;
                    case 2: return 4;
                }
                ',
            '<?php
                $foo = $isBar? 1 : 2;
                switch ($foo) {
                    case 1: return 3;
                    case 2: return 4;
                }
                ',
        ];

        yield [
            '<?php
                return $isBar ? array_sum(array_map(function ($x) { switch ($x) { case 1: return $y ? 2 : 3; case 4: return 5; } }, [1, 2, 3])) : 128;
                ',
            '<?php
                return $isBar?array_sum(array_map(function ($x) { switch ($x) { case 1: return $y? 2 : 3; case 4: return 5; } }, [1, 2, 3])):128;
                ',
        ];

        yield [
            '<?php
                declare(ticks=1):enddeclare;
                for ($i = 0; $i < 100; $i++): echo "."; endfor;
                foreach ($foo as $bar): $i++; endforeach;
                if ($x === 1): echo "One"; elseif ($x === 2): echo "Two"; else: echo "Three"; endif;
                switch (true): default: return 0; endswitch;
                while ($i > 10): $i--; endwhile;
                /* ternary operator to make the file a candidate for fixing */ true ? 1 : 0;
                ',
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
        yield 'nullable types in constructor property promotion' => [
            '<?php

class Foo
{
    public function __construct(
        private ?string $foo = null,
        protected ?string $bar = null,
        public ?string $xyz = null,
    ) {
        /* ternary operator to make the file a candidate for fixing */ true ? 1 : 0;
    }
}',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            <<<'PHP'
                <?php

                enum TaskType: int
                {
                    public function foo(bool $value): string
                    {
                        return $value ? 'foo' : 'bar';
                    }
                }
                PHP,
        ];
    }
}
