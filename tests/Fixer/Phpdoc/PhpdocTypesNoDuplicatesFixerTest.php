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
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesNoDuplicatesFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocTypesNoDuplicatesFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpdocTypesNoDuplicatesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php /** @var string|null|int */',
            '<?php /** @var string|string|null|int */',
        ];

        yield [
            '<?php /** @var list<int> */',
            '<?php /** @var list<int|int> */',
        ];

        yield [
            '<?php /** @var string|array<string, string>|array<int, int> */',
            '<?php /** @var string|string|array<string, string|string>|array<int, int|int> */',
        ];

        yield [
            '<?php /** @var list<float|int> */',
            '<?php /** @var list<float|int>|list<int|float> */',
        ];

        yield [
            '<?php /** @var array<int|float, int|string|float|null>|string */',
            '<?php /** @var array<int|float, int|int|string|float|null>|array<float|int, string|null|int|int|float>|string|string */',
        ];

        yield [
            '<?php /** @var array{foo: string} */',
            '<?php /** @var array{foo: string|string} */',
        ];

        yield [
            '<?php /** @var Bar|Baz */',
            '<?php /** @var Bar|Bar|Baz */',
        ];

        yield [
            '<?php /** @var Bar|\Bar */',
        ];

        yield [
            '<?php /** @var string|\Foo\Baz|\Foo\Bar|Foo\Baz */',
            '<?php /** @var string|\Foo\Baz|\Foo\Bar|\Foo\Baz|Foo\Baz */',
        ];
    }
}
