<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\PhpTag;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Michele Locati <michele@locati.it>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpTag\ShortEchoTagFixer
 */
final class ShortEchoTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     * @param bool        $shortAlways
     *
     * @dataProvider provideLongToShortFormatCases
     */
    public function testLongToShortFormat($expected, $input = null, $shortAlways = false)
    {
        $this->fixer->configure(['format' => 'short', 'short_always' => $shortAlways]);
        $this->doTest($expected, $input);
    }

    public function provideLongToShortFormatCases()
    {
        return [
            ['<?= \'Foo\';', '<?php echo \'Foo\';'],
            ['<?= \'Foo\';', '<?php print \'Foo\';'],
            ['<?= \'Foo\'; ?> PLAIN TEXT', '<?php echo \'Foo\'; ?> PLAIN TEXT'],
            ['<?= \'Foo\'; ?> PLAIN TEXT', '<?php print \'Foo\'; ?> PLAIN TEXT'],
            ['PLAIN TEXT<?= \'Foo\'; ?>', 'PLAIN TEXT<?php echo \'Foo\'; ?>'],
            ['PLAIN TEXT<?= \'Foo\'; ?>', 'PLAIN TEXT<?php print \'Foo\'; ?>'],
            ['<?= \'Foo\'; ?> <?= \'Bar\'; ?>', '<?php echo \'Foo\'; ?> <?php echo \'Bar\'; ?>'],
            ['<?= \'Foo\'; ?> <?= \'Bar\'; ?>', '<?php print \'Foo\'; ?> <?php echo \'Bar\'; ?>'],
            ['<?php echo \'Foo\'; someThingElse();'],
            ['<?= \'Foo\'; someThingElse();', '<?php echo \'Foo\'; someThingElse();', true],
            ['<?=/*this */ /** should be in the result*/ \'Foo\';', '<?php /*this */ /** should be in the result*/ echo \'Foo\';'],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param string      $function
     *
     * @dataProvider provideShortToLongFormatCases
     */
    public function testShortToLongFormat($expected, $input, $function)
    {
        $this->fixer->configure(['format' => 'long', 'long_function' => $function]);
        $this->doTest($expected, $input);
    }

    public function provideShortToLongFormatCases()
    {
        $cases = [
            ['<?php <fn> 1;', '<?= 1;'],
            ['<?php <fn> 1;', '<?=1;'],
            ['<?php <fn> /**/1;', '<?=/**/1;'],
            ['<?php <fn> /**/ 1;', '<?=/**/ 1;'],
            ['<?php <fn> \'Foo\';', '<?= \'Foo\';'],
            ['<?php <fn> \'Foo\'; ?> PLAIN TEXT', '<?= \'Foo\'; ?> PLAIN TEXT'],
            ['PLAIN TEXT<?php <fn> \'Foo\'; ?>', 'PLAIN TEXT<?= \'Foo\'; ?>'],
            ['<?php <fn> \'Foo\'; ?> <?php <fn> \'Bar\'; ?>', '<?= \'Foo\'; ?> <?= \'Bar\'; ?>'],
            ['<?php <fn> foo();', '<?=foo();'],
        ];
        $result = [];
        foreach (['echo', 'print'] as $fn) {
            foreach ($cases as $case) {
                $result[] = [str_replace('<fn>', $fn, $case[0]), str_replace('<fn>', $fn, $case[1]), $fn];
            }
        }

        return $result;
    }
}
