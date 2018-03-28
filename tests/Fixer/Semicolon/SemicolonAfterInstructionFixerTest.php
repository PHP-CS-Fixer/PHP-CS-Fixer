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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer
 */
final class SemicolonAfterInstructionFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php $a = [1,2,3]; echo $a{1}; ?>',
                '<?php $a = [1,2,3]; echo $a{1} ?>',
            ],
            [
                '<?php $a++;//a ?>',
                '<?php $a++//a ?>',
            ],
            [
                '<?php $b++; /**/ ?>',
                '<?php $b++ /**/ ?>',
            ],
            [
                '<?php echo 123; ?>',
                '<?php echo 123 ?>',
            ],
            [
                "<?php echo 123;\n\t?>",
                "<?php echo 123\n\t?>",
            ],
            ['<?php ?>'],
            ['<?php if($a){}'],
            ['<?php while($a > $b){}'],
            [
                '<?php if ($a == 5): ?>
A is equal to 5
<?php endif; ?>
<?php switch ($foo): ?>
<?php case 1: ?>
...
<?php endswitch; ?>',
                '<?php if ($a == 5): ?>
A is equal to 5
<?php endif; ?>
<?php switch ($foo): ?>
<?php case 1: ?>
...
<?php endswitch ?>',
            ],
            [
                '<?php if ($a == 5) { ?>
A is equal to 5
<?php } ?>',
            ],
        ];
    }

    public function testOpenWithEcho()
    {
        if (!ini_get('short_open_tag')) {
            $this->markTestSkipped('The short_open_tag option is required to be enabled.');
        }

        $this->doTest("<?= '1_'; ?>", "<?= '1_' ?>");
    }
}
