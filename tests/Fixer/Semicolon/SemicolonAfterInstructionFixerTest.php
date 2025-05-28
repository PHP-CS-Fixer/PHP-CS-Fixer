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
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer>
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

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'comment' => [
            '<?php $a++;//a ?>',
            '<?php $a++//a ?>',
        ];

        yield 'comment II' => [
            '<?php $b++; /**/ ?>',
            '<?php $b++ /**/ ?>',
        ];

        yield 'no space' => [
            '<?php $b++;?>',
            '<?php $b++?>',
        ];

        yield [
            '<?php echo 123; ?>',
            '<?php echo 123 ?>',
        ];

        yield [
            "<?php echo 123;\n\t?>",
            "<?php echo 123\n\t?>",
        ];

        yield ['<?php ?>'];

        yield ['<?php ; ?>'];

        yield ['<?php if($a){}'];

        yield ['<?php while($a > $b){}'];

        yield [
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
        ];

        yield [
            '<?php if ($a == 5) { ?>
A is equal to 5
<?php } ?>',
        ];

        yield 'open tag with echo' => [
            "<?= '1_'; ?> <?php ?><?= 1; ?>",
            "<?= '1_' ?> <?php ?><?= 1; ?>",
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php $a = [1,2,3]; echo $a{1}; ?>',
            '<?php $a = [1,2,3]; echo $a{1} ?>',
        ];
    }
}
