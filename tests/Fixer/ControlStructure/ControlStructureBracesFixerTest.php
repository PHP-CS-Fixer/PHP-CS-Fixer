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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ControlStructure\ControlStructureBracesFixer>
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\ControlStructureBracesFixer
 */
final class ControlStructureBracesFixerTest extends AbstractFixerTestCase
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
        yield 'if' => [
            '<?php if ($foo) { foo(); }',
            '<?php if ($foo) foo();',
        ];

        yield 'else' => [
            '<?php
                if ($foo) { foo(); }
                else { bar(); }',
            '<?php
                if ($foo) { foo(); }
                else bar();',
        ];

        yield 'elseif' => [
            '<?php
                if ($foo) { foo(); }
                elseif ($bar) { bar(); }',
            '<?php
                if ($foo) { foo(); }
                elseif ($bar) bar();',
        ];

        yield 'else if' => [
            '<?php
                if ($foo) { foo(); }
                else if ($bar) { bar(); }',
            '<?php
                if ($foo) { foo(); }
                else if ($bar) bar();',
        ];

        yield 'for' => [
            '<?php for (;;) { foo(); }',
            '<?php for (;;) foo();',
        ];

        yield 'foreach' => [
            '<?php foreach ($foo as $bar) { foo(); }',
            '<?php foreach ($foo as $bar) foo();',
        ];

        yield 'while' => [
            '<?php while ($foo) { foo(); }',
            '<?php while ($foo) foo();',
        ];

        yield 'do while' => [
            '<?php
                do { foo(); }
                while ($foo);',
            '<?php
                do foo();
                while ($foo);',
        ];

        yield 'empty if' => [
            '<?php if ($foo);',
        ];

        yield 'empty else' => [
            '<?php
                if ($foo) { foo(); }
                else;',
        ];

        yield 'empty elseif' => [
            '<?php
                if ($foo) { foo(); }
                elseif ($bar);',
        ];

        yield 'empty else if' => [
            '<?php
                if ($foo) { foo(); }
                else if ($bar);',
        ];

        yield 'empty for' => [
            '<?php for (;;);',
        ];

        yield 'empty foreach' => [
            '<?php foreach ($foo as $bar);',
        ];

        yield 'empty while' => [
            '<?php while ($foo);',
        ];

        yield 'empty do while' => [
            '<?php do; while ($foo);',
        ];

        yield 'nested if using alternative syntax' => [
            '<?php if ($foo) { if ($bar): ?> foo <?php endif; } ?>',
            '<?php if ($foo) if ($bar): ?> foo <?php endif; ?>',
        ];

        yield 'nested for using alternative syntax' => [
            '<?php if ($foo) { for (;;): ?> foo <?php endfor; } ?>',
            '<?php if ($foo) for (;;): ?> foo <?php endfor; ?>',
        ];

        yield 'nested foreach using alternative syntax' => [
            '<?php if ($foo) { foreach ($foo as $bar): ?> foo <?php endforeach; } ?>',
            '<?php if ($foo) foreach ($foo as $bar): ?> foo <?php endforeach; ?>',
        ];

        yield 'nested while using alternative syntax' => [
            '<?php if ($foo) { while ($foo): ?> foo <?php endwhile; } ?>',
            '<?php if ($foo) while ($foo): ?> foo <?php endwhile; ?>',
        ];

        yield 'nested switch using alternative syntax' => [
            '<?php if ($foo) { switch ($foo): case 1: ?> foo <?php endswitch; } ?>',
            '<?php if ($foo) switch ($foo): case 1: ?> foo <?php endswitch; ?>',
        ];

        yield 'declare followed by closing tag' => [
            '<?php declare(strict_types=1) ?>',
        ];
    }
}
