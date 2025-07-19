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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\ReturnToYieldFromFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ArrayNotation\ReturnToYieldFromFixer>
 */
final class ReturnToYieldFromFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php return [function() { return [1, 2, 3]; }];'];

        yield ['<?php return [fn() => [1, 2, 3]];'];

        yield [
            '<?php
                function foo(): iterable { return $z; }

                return [1,2] ?>  X  <?php { echo 2; }',
        ];

        yield ['<?php function foo() { return [1, 2, 3]; }'];

        yield ['<?php function foo(): MyAwesomeIterableType { return [1, 2, 3]; }'];

        yield ['<?php function foo(): iterable { if (true) { return [1]; } else { return [2]; } }'];

        yield ['<?php function foo(): ?iterable { return [1, 2, 3]; }'];

        yield ['<?php
            abstract class Foo {
                abstract public function bar(): iterable;
                public function baz(): array { return []; }
            }
        '];

        yield [
            '<?php return [function(): iterable { yield from [1, 2, 3]; }];',
            '<?php return [function(): iterable { return [1, 2, 3]; }];',
        ];

        yield [
            '<?php class Foo {
                function bar(): iterable { yield from [1, 2, 3]; }
            }',
            '<?php class Foo {
                function bar(): iterable { return [1, 2, 3]; }
            }',
        ];

        yield [
            '<?php function foo(): iterable { yield from [1, 2, 3];;;;;;;; }',
            '<?php function foo(): iterable { return [1, 2, 3];;;;;;;; }',
        ];

        yield [
            '<?php function foo(): iterable { yield from array(1, 2, 3); }',
            '<?php function foo(): iterable { return array(1, 2, 3); }',
        ];

        yield [
            '<?php function foo(): iterable { $x = 0; yield from [1, 2, 3]; }',
            '<?php function foo(): iterable { $x = 0; return [1, 2, 3]; }',
        ];

        yield [
            '<?php function foo(): iterable { $x = 0; yield from array(1, 2, 3); }',
            '<?php function foo(): iterable { $x = 0; return array(1, 2, 3); }',
        ];

        yield [
            '<?php function foo(): ITERABLE { yield from [1, 2, 3]; }',
            '<?php function foo(): ITERABLE { return [1, 2, 3]; }',
        ];

        yield [
            '<?php $f = function(): iterable { yield from [1, 2, 3]; };',
            '<?php $f = function(): iterable { return [1, 2, 3]; };',
        ];

        yield [
            '<?php
                function foo(): array { return [3, 4]; }
                function bar(): iterable { yield from [1, 2]; }
                function baz(): int { return 5; }
            ',
            '<?php
                function foo(): array { return [3, 4]; }
                function bar(): iterable { return [1, 2]; }
                function baz(): int { return 5; }
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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php function foo(): null|iterable { return [1, 2, 3]; }',
        ];

        yield [
            '<?php function foo(): iterable|null { return [1, 2, 3]; }',
        ];

        yield [
            '<?php function foo(): ITERABLE|null { return [1, 2, 3]; }',
        ];

        yield [
            '<?php function foo(): Bar|iterable { return [1, 2, 3]; }',
        ];
    }
}
