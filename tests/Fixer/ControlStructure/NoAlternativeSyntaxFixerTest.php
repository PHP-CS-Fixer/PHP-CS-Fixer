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
 * @author Eddilbert Macharia <edd.cowan@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoAlternativeSyntaxFixer
 */
final class NoAlternativeSyntaxFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
                declare(ticks = 1) {
                }
            ',
            '<?php
                declare(ticks = 1) :
                enddeclare;
            ',
        ];

        yield [
            '<?php
    switch ($foo) {
        case 1:
    }

    switch ($foo)   {
        case 1:
    }    ?>',
            '<?php
    switch ($foo):
        case 1:
    endswitch;

    switch ($foo)   :
        case 1:
    endswitch    ?>',
        ];

        yield [
            '<?php
                if ($some1) {
                    if ($some2) {
                        if ($some3) {
                            $test = true;
                        }
                    }
                }
            ',
            '<?php
                if ($some1) :
                    if ($some2) :
                        if ($some3) :
                            $test = true;
                        endif;
                    endif;
                endif;
            ',
        ];

        yield [
            '<?php if ($some) { $test = true; } else { $test = false; }',
        ];

        yield [
            '<?php if ($some) /* foo */ { $test = true; } else { $test = false; }',
            '<?php if ($some) /* foo */ : $test = true; else :$test = false; endif;',
        ];

        yield [
            '<?php if ($some) { $test = true; } else { $test = false; }',
            '<?php if ($some) : $test = true; else :$test = false; endif;',
        ];

        yield [
            '<?php if ($some) { if($test){echo $test;}$test = true; } else { $test = false; }',
            '<?php if ($some) : if($test){echo $test;}$test = true; else : $test = false; endif;',
        ];

        yield [
            '<?php foreach (array("d") as $item) { echo $item;}',
            '<?php foreach (array("d") as $item):echo $item;endforeach;',
        ];

        yield [
            '<?php foreach (array("d") as $item) { if($item){echo $item;}}',
            '<?php foreach (array("d") as $item):if($item){echo $item;}endforeach;',
        ];

        yield [
            '<?php while (true) { echo "c";}',
            '<?php while (true):echo "c";endwhile;',
        ];

        yield [
            '<?php foreach (array("d") as $item) { while ($item) { echo "dd";}}',
            '<?php foreach (array("d") as $item):while ($item):echo "dd";endwhile;endforeach;',
        ];

        yield [
            '<?php foreach (array("d") as $item) { while ($item) { echo "dd" ; } }',
            '<?php foreach (array("d") as $item): while ($item) : echo "dd" ; endwhile; endforeach;',
        ];

        yield [
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            '<?php if ($some) : $test = true; elseif ($some !== "test") : $test = false; endif;',
        ];

        yield [
            '<?php if ($condition) { ?><p>This is visible.</p><?php } ?>',
            '<?php if ($condition): ?><p>This is visible.</p><?php endif; ?>',
        ];

        yield [
            '<?php if ($condition): ?><p>This is visible.</p><?php endif; ?>',
            null,
            ['fix_non_monolithic_code' => false],
        ];

        yield [
            '<?php if (true) { ?>Text display.<?php } ?>',
            '<?php if (true): ?>Text display.<?php endif; ?>',
            ['fix_non_monolithic_code' => true],
        ];

        yield [
            '<?php if (true): ?>Text display.<?php endif; ?>',
            null,
            ['fix_non_monolithic_code' => false],
        ];

        yield [
            '<?php if ($condition) { ?><?= "xd"; ?><?php } ?>',
            '<?php if ($condition): ?><?= "xd"; ?><?php endif; ?>',
            ['fix_non_monolithic_code' => true],
        ];

        yield [
            '<?php if ($condition): ?><?= "xd"; ?><?php endif; ?>',
            null,
            ['fix_non_monolithic_code' => false],
        ];
    }
}
