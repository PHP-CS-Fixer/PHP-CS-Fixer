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
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php function foo() { return [1, 2, 3]; }'];

        yield ['<?php function foo(): iterable { if (true) { return [1]; } else { return [2]; } }'];

        yield ['<?php
            abstract class Foo {
                abstract public function bar(): iterable;
                public function baz(): array { return []; }
            }
        '];

        yield [
            '<?php function foo(): iterable { yield from [1, 2, 3]; }',
            '<?php function foo(): iterable { return [1, 2, 3]; }',
        ];

        yield [
            '<?php function foo(): ITERABLE { yield from [1, 2, 3]; }',
            '<?php function foo(): ITERABLE { return [1, 2, 3]; }',
        ];

        yield [
            '<?php
                function foo(): iterable { yield from [1, 2]; }
                function bar(): array { return [3, 4]; }
                function baz(): int { return 5; }
            ',
            '<?php
                function foo(): iterable { return [1, 2]; }
                function bar(): array { return [3, 4]; }
                function baz(): int { return 5; }
            ',
        ];
    }
}
