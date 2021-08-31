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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            [
                '<?php
                    declare(ticks = 1) {
                    }
                ',
                '<?php
                    declare(ticks = 1) :
                    enddeclare;
                ',
            ],
            [
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
            ],
            [
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
            ],
            ['<?php if ($some) { $test = true; } else { $test = false; }'],
            [
                '<?php if ($some) /* foo */ { $test = true; } else { $test = false; }',
                '<?php if ($some) /* foo */ : $test = true; else :$test = false; endif;',
            ],
            [
                '<?php if ($some) { $test = true; } else { $test = false; }',
                '<?php if ($some) : $test = true; else :$test = false; endif;',
            ],
            [
                '<?php if ($some) { if($test){echo $test;}$test = true; } else { $test = false; }',
                '<?php if ($some) : if($test){echo $test;}$test = true; else : $test = false; endif;',
            ],
            [
                '<?php foreach (array("d") as $item) { echo $item;}',
                '<?php foreach (array("d") as $item):echo $item;endforeach;',
            ],
            [
                '<?php foreach (array("d") as $item) { if($item){echo $item;}}',
                '<?php foreach (array("d") as $item):if($item){echo $item;}endforeach;',
            ],
            [
                '<?php while (true) { echo "c";}',
                '<?php while (true):echo "c";endwhile;',
            ],
            [
                '<?php foreach (array("d") as $item) { while ($item) { echo "dd";}}',
                '<?php foreach (array("d") as $item):while ($item):echo "dd";endwhile;endforeach;',
            ],
            [
                '<?php foreach (array("d") as $item) { while ($item) { echo "dd" ; } }',
                '<?php foreach (array("d") as $item): while ($item) : echo "dd" ; endwhile; endforeach;',
            ],
            [
                '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
                '<?php if ($some) : $test = true; elseif ($some !== "test") : $test = false; endif;',
            ],
        ];
    }
}
