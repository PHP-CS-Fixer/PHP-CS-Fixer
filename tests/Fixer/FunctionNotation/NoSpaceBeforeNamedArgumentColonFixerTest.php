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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author HypeMC <hypemc@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NoSpaceBeforeNamedArgumentColonFixer
 *
 * @requires PHP 8.0
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\FunctionNotation\NoSpaceBeforeNamedArgumentColonFixer>
 */
final class NoSpaceBeforeNamedArgumentColonFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php foo(bar: \'baz\');',
            '<?php foo(bar   : \'baz\');',
        ];

        yield [
            '<?php foo(bar/* qux */: \'baz\');',
            '<?php foo(bar  /* qux */  : \'baz\');',
        ];

        yield [
            '<?php
                foo(
                    bar: \'baz\',
                    qux/* corge */: 3,
                );
            ',
            '<?php
                foo(
                    bar   : \'baz\',
                    qux /* corge */ : 3,
                );
            ',
        ];

        yield [
            '<?php foo(bar/* a *//* b *//* c */: \'baz\');',
            '<?php foo(bar  /* a *//* b *//* c */  : \'baz\');',
        ];

        yield [
            '<?php foo(bar//x
: 1);',
            '<?php foo(bar//x
                : 1);',
        ];
    }
}
