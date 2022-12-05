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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\FirstClassCallableTransformer
 */
final class FirstClassCallableTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases
     *
     * @requires PHP 8.1
     */
    public function testProcess(array $expectedTokens, string $source): void
    {
        $this->doTest($source, $expectedTokens, [CT::T_FIRST_CLASS_CALLABLE]);
    }

    public static function provideProcessCases(): iterable
    {
        yield 'set' => [
            [
                3 => CT::T_FIRST_CLASS_CALLABLE,
                9 => CT::T_FIRST_CLASS_CALLABLE,
                15 => CT::T_FIRST_CLASS_CALLABLE,
                23 => CT::T_FIRST_CLASS_CALLABLE,
                31 => CT::T_FIRST_CLASS_CALLABLE,
                41 => CT::T_FIRST_CLASS_CALLABLE,
                49 => CT::T_FIRST_CLASS_CALLABLE,
                57 => CT::T_FIRST_CLASS_CALLABLE,
                71 => CT::T_FIRST_CLASS_CALLABLE,
                77 => CT::T_FIRST_CLASS_CALLABLE,
                88 => CT::T_FIRST_CLASS_CALLABLE,
                101 => CT::T_FIRST_CLASS_CALLABLE,
            ],
            '<?php
strlen(...);
$closure(...);
$invokableObject(...);
$obj->method(...);
$obj->$methodStr(...);
($obj->property)(...);
Foo::method(...);
$classStr::$methodStr(...);
self::{$complex . $expression}(...);
\'strlen\'(...);
[$obj, \'method\'](...);
[Foo::class, \'method\'](...) ?>',
        ];

        yield 'comments and spacing' => [
            [
                4 => CT::T_FIRST_CLASS_CALLABLE,
                12 => CT::T_FIRST_CLASS_CALLABLE,
                18 => CT::T_FIRST_CLASS_CALLABLE,
                28 => CT::T_FIRST_CLASS_CALLABLE,
                40 => CT::T_FIRST_CLASS_CALLABLE,
            ],
            '<?php
strlen(/* */.../* */);
$closure(   ...);
$invokableObject(...  );
$obj->method(  ...  );
$obj->$methodStr(  /* */  ... /* */  );
            ',
        ];

        yield 'not cases' => [
            [],
            '<?php
                array_unshift($types, ...$nulls);
                array_unshift(...$nulls, 1);
                foo(...$args, named: $arg);
                foo(named: $arg, ...$args);
                foo(...$nulls);
                $fruits = ["banana", "orange", ...$parts, "watermelon"];
                $a = [...$array1, ...$array2];
            ',
        ];
    }
}
