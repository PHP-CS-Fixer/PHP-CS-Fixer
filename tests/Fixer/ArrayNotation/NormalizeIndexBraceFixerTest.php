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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer>
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NormalizeIndexBraceFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP <8.0
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
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

    /**
     * @requires PHP 8.4
     *
     * @dataProvider provideFix84Cases
     */
    public function testFix84(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield 'property hooks: property without default value' => [
            <<<'PHP'
                <?php

                class PropertyHooks
                {
                    public string $bar {
                        set(string $value) {
                            $this -> foo = strtolower($value);
                        }
                    }
                }
                PHP,
        ];
    }
}
