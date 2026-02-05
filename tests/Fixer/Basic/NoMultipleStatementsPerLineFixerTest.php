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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\NoMultipleStatementsPerLineFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Basic\NoMultipleStatementsPerLineFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoMultipleStatementsPerLineFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple' => [
            '<?php
                foo();
                bar();',
            '<?php
                foo(); bar();',
        ];

        yield 'for loop' => [
            '<?php
                for ($i = 0; $i < 10; ++$i) {
                    foo();
                }',
        ];

        yield 'mixed `;` and close tag' => [
            '<?php ++$a;
++$b ?>',
            '<?php ++$a; ++$b ?>',
        ];

        yield 'followed by closing brace' => [
            '<?php if ($foo) { foo(); }',
        ];

        yield 'followed by closing tag' => [
            '<?php foo(); ?>',
        ];

        yield 'if alternative syntax' => [
            '<?php if ($foo): foo(); endif;',
        ];

        yield 'for alternative syntax' => [
            '<?php for (;;): foo(); endfor;',
        ];

        yield 'foreach alternative syntax' => [
            '<?php foreach ($foo as $bar): foo(); endforeach;',
        ];

        yield 'while alternative syntax' => [
            '<?php while ($foo): foo(); endwhile;',
        ];

        yield 'switch alternative syntax' => [
            '<?php switch ($foo): case true: foo(); endswitch;',
        ];
    }

    /**
     * @dataProvider provideFix84Cases
     *
     * @requires PHP 8.4
     */
    public function testFix84(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield "don't touch property hooks" => [
            '<?php interface I {
    public string $readable { get; }
    public string $writeable { set; }
    public string $both { get; set; }
    public string $differentCasing { GET; Set; }
}',
        ];
    }
}
