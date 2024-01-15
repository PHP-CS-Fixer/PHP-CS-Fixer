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

    public static function provideFixCases(): iterable
    {
        yield 'if' => [
            '<?php if ($foo) { foo(); }',
            '<?php if ($foo) foo();',
        ];

        yield 'else' => [
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                else { bar(); }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                else bar();
                EOD,
        ];

        yield 'elseif' => [
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                elseif ($bar) { bar(); }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                elseif ($bar) bar();
                EOD,
        ];

        yield 'else if' => [
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                else if ($bar) { bar(); }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                else if ($bar) bar();
                EOD,
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
            <<<'EOD'
                <?php
                                do { foo(); }
                                while ($foo);
                EOD,
            <<<'EOD'
                <?php
                                do foo();
                                while ($foo);
                EOD,
        ];

        yield 'empty if' => [
            '<?php if ($foo);',
        ];

        yield 'empty else' => [
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                else;
                EOD,
        ];

        yield 'empty elseif' => [
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                elseif ($bar);
                EOD,
        ];

        yield 'empty else if' => [
            <<<'EOD'
                <?php
                                if ($foo) { foo(); }
                                else if ($bar);
                EOD,
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
