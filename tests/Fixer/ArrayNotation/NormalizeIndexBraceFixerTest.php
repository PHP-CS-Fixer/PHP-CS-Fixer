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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer
 *
 * @requires PHP <8.0
 */
final class NormalizeIndexBraceFixerTest extends AbstractFixerTestCase
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
        yield [
            '<?php echo $arr[$index];',
            '<?php echo $arr{$index};',
        ];

        yield [
            '<?php echo $nestedArray[$index][$index2][$index3][$index4];',
            '<?php echo $nestedArray{$index}{$index2}[$index3]{$index4};',
        ];

        yield [
            '<?php echo $array[0]->foo . $collection->items[1]->property;',
            '<?php echo $array{0}->foo . $collection->items{1}->property;',
        ];
    }
}
