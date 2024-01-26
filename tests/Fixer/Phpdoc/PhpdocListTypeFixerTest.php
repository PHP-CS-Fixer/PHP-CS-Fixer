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
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocListTypeFixer
 */
final class PhpdocListTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array{style?: string} $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: string, 2?: array{style: string}}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php /** @tagNotSupportingTypes string[] */'];

        yield ['<?php /** @var $variableWithoutType */'];

        yield [
            '<?php /** @var list<int> */',
            '<?php /** @var int[] */',
        ];

        yield [
            '<?php /** @param list<list<list<list<int>>>> $x */',
            '<?php /** @param int[][][][] $x */',
        ];

        yield [
            '<?php /** @return iterable<list<int>> */',
            '<?php /** @return iterable<int[]> */',
        ];

        yield [
            '<?php /** @var list<Foo\Bar> */',
            '<?php /** @var Foo\Bar[] */',
        ];

        yield [
            '<?php /** @var list<Foo_Bar> */',
            '<?php /** @var Foo_Bar[] */',
        ];

        yield [
            '<?php /** @var list<bool>|list<float>|list<int>|list<string> */',
            '<?php /** @var array<bool>|float[]|array<int>|string[] */',
        ];

        yield [
            <<<'PHP'
                <?php
                /** @return list<int> */
                /*  @return int[] */
                PHP,
            <<<'PHP'
                <?php
                /** @return int[] */
                /*  @return int[] */
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php
                /** @var array<int, string> */
                /** @var array<int, array<string, bool>> */
                /** @var array<int, array{string, string, string}> */
                /** @var array{string, string, string} */
                PHP,
        ];

        yield [
            <<<'PHP'
                <?php
                /** @var list<Foo> */
                /** @var list<Foo> */
                /** @var list<Foo> */
                /** @var array<int, Foo> */
                /** @var array{Foo} */
                /** @var array{int, Foo} */
                PHP,
            <<<'PHP'
                <?php
                /** @var Foo[] */
                /** @var array<Foo> */
                /** @var list<Foo> */
                /** @var array<int, Foo> */
                /** @var array{Foo} */
                /** @var array{int, Foo} */
                PHP,
        ];

        yield [
            '<?php /** @var array<int> */',
            '<?php /** @var int[] */',
            ['style' => 'array'],
        ];

        yield [
            '<?php /** @var array<int> */',
            '<?php /** @var list<int> */',
            ['style' => 'array'],
        ];

        yield [
            '<?php /** @var array<array<array<array<int>>>> */',
            '<?php /** @var int[][][][] */',
            ['style' => 'array'],
        ];
    }
}
