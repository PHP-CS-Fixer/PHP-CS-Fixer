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

use PhpCsFixer\Fixer\PhpTag\InlineTagFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Michele Locati <michele@locati.it>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpTag\InlineTagFixer
 */
final class InlineTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestCases
     */
    public function testInlineFixer($expected, $input = null, array $configuration = [])
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideTestCases()
    {
        $keepAll = [InlineTagFixer::OPTION_SPACEBEFORE => InlineTagFixer::SPACE_KEEP, InlineTagFixer::OPTION_SPACEAFTER => InlineTagFixer::SPACE_KEEP, InlineTagFixer::OPTION_SEMICOLON => null];
        $removeAll = [InlineTagFixer::OPTION_SPACEBEFORE => InlineTagFixer::SPACE_MINIMUM, InlineTagFixer::OPTION_SPACEAFTER => InlineTagFixer::SPACE_MINIMUM, InlineTagFixer::OPTION_SEMICOLON => false];
        $oneSpace = [InlineTagFixer::OPTION_SPACEBEFORE => InlineTagFixer::SPACE_ONE, InlineTagFixer::OPTION_SPACEAFTER => InlineTagFixer::SPACE_ONE];

        return [
            ['<?= 2 ?> <?= 2 ?> <?= 2 ?>', '<?=2;?> <?= 2; ?> <?=  2;  ?>'],
            ['<?= 3 ?> <?= 3 ?> <?= 3 ?> <?= 3 ?>', '<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>'],
            ['<?php ', null, $keepAll],
            ['<?php ', null, $removeAll],
            ['<?php    ?>', null, $keepAll],
            ['<?php    ?>', null, [InlineTagFixer::OPTION_SEMICOLON => false] + $keepAll],
            ['<?php    ?>', null, [InlineTagFixer::OPTION_SEMICOLON => true] + $keepAll],
            ['<?php ?>', '<?php    ?>', $removeAll],
            ['<?php   ;   ?>', null, $keepAll],
            ['<?php ?>', '<?php   ;   ?>', $removeAll],
            ['<?php   ;?>', '<?php   ;   ?>', [InlineTagFixer::OPTION_SPACEAFTER => InlineTagFixer::SPACE_MINIMUM] + $keepAll],
            ['<?php ;   ?>', '<?php   ;   ?>', [InlineTagFixer::OPTION_SPACEBEFORE => InlineTagFixer::SPACE_MINIMUM] + $keepAll],
            ['<?php echo 1?>', '<?php   echo 1   ;   ?>', $removeAll],
            ['<?php echo 1 ?>', '<?php   echo 1  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => false] + $oneSpace],
            ['<?php echo 1; ?>', '<?php   echo 1  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => true] + $oneSpace],
            ['<?php echo 1; ?>', '<?php   echo 1  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => null] + $oneSpace],
            ['<?=1?>', '<?=    1   ;   ?>', $removeAll],
            ['<?= 1 ?>', '<?=    1  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => false] + $oneSpace],
            ['<?= 1; ?>', '<?=    1  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => true] + $oneSpace],
            ['<?= 1; ?>', '<?=   1  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => null] + $oneSpace],
            ['<?php {}?>', '<?php   {}   ;   ?>', $removeAll],
            ['<?php {} ?>', '<?php   {}  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => false] + $oneSpace],
            ['<?php {} ?>', '<?php   {}  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => true] + $oneSpace],
            ['<?php {}; ?>', '<?php   {}  ;   ?>', [InlineTagFixer::OPTION_SEMICOLON => null] + $oneSpace],
            ["\n<?= 2 ?>\n", "\n<?=2;?>\n"],
            ["<?=\n 2 ?>"],
            ["<?= 2 \n?>"],
        ];
    }
}
