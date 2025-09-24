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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocListTypeFixer>
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocListTypeFixer
 */
final class PhpdocListTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php /** @tagNotSupportingTypes string[] */'];

        yield ['<?php /** @var array<string, int> */'];

        yield ['<?php /** @var array<int, array<string, bool>> */'];

        yield ['<?php /** @var array<class-string<Foo>, bool> */'];

        yield ['<?php /** @var array<int<1, 10>, bool> */'];

        yield ['<?php /** @var array<Foo::BAR_*, bool> */'];

        yield ['<?php /** @var array<\'foo\'|\'bar\', bool> */'];

        yield ['<?php /** @var array{} */'];

        yield ['<?php /** @var array{string, string, string} */'];

        yield ['<?php /** @var array{"a", "b[]", "c"} */'];

        yield ["<?php /** @var array{'a', 'b[]', 'c'} */"];

        yield [
            '<?php /** @var list<Foo> */',
            '<?php /** @var array<Foo> */',
        ];

        yield [
            '<?php /** @var list<Foo> */',
            '<?php /** @var ARRAY<Foo> */',
        ];

        yield [
            '<?php /** @var ?list<Foo> */',
            '<?php /** @var ?array<Foo> */',
        ];

        yield [
            '<?php /** @var list<bool>|list<float>|list<int>|list<string> */',
            '<?php /** @var array<bool>|list<float>|array<int>|list<string> */',
        ];

        yield [
            '<?php /** @var non-empty-list<string> */',
            '<?php /** @var non-empty-array<string> */',
        ];

        yield [
            '<?php /** @var array{string, list<array{Foo, list<int>, Bar}>} */',
            '<?php /** @var array{string, array<array{Foo, array<int>, Bar}>} */',
        ];

        yield [
            '<?php /** @var list<int<1, 10>> */',
            '<?php /** @var array<int<1, 10>> */',
        ];

        yield [<<<'EOD'
            <?php
            /** @var list<Foo> */
            /** @var \SplFixedArray<Foo> */
            /** @var \KůňArray<Foo> */
            /** @var \My_Array<Foo> */
            /** @var \My2Array<Foo> */
            EOD, <<<'EOD'
            <?php
            /** @var array<Foo> */
            /** @var \SplFixedArray<Foo> */
            /** @var \KůňArray<Foo> */
            /** @var \My_Array<Foo> */
            /** @var \My2Array<Foo> */
            EOD,
        ];
    }
}
