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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer
 */
final class SemicolonAfterInstructionFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield from [
            'comment' => [
                '<?php $a++;//a ?>',
                '<?php $a++//a ?>',
            ],
            'comment II' => [
                '<?php $b++; /**/ ?>',
                '<?php $b++ /**/ ?>',
            ],
            'no space' => [
                '<?php $b++;?>',
                '<?php $b++?>',
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
            ['<?php ; ?>'],
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

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php $a = [1,2,3]; echo $a{1}; ?>',
            '<?php $a = [1,2,3]; echo $a{1} ?>',
        ];
    }

    public function testOpenWithEcho(): void
    {
        if (!\ini_get('short_open_tag')) {
            static::markTestSkipped('The short_open_tag option is required to be enabled.');
        }

        $this->doTest(
            "<?= '1_'; ?> <?php ?><?= 1; ?>",
            "<?= '1_' ?> <?php ?><?= 1; ?>"
        );
    }
}
