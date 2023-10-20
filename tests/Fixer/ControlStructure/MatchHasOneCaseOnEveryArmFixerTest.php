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
 * @coversNothing
 */
final class MatchHasOneCaseOnEveryArmFixerTest extends AbstractFixerTestCase
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
            '<?php
                match($bar) {
                    1 =>"1",
                    2,
                    3 => "2"
                };
            ',
            '<?php
                match($bar) {
                    1 =>"1",
                    2,3 => "2"
                };
            ',
        ];

        yield [
            '<?php
                match($bar) {
                    1 =>"1",
                    2,
                    3,
                    4 => "2"
                };
            ',
            '<?php
                match($bar) {
                    1 =>"1",
                    2,3,4 => "2"
                };
            ',
        ];

        yield [
            '<?php
                match($bar) {
                    1 =>"1",
                    2,
                    3,
                    4 => "2",
                    5 => "3",
                    6 => "4",
                    7,
                    8 => "5",
                };
            ',
            '<?php
                match($bar) {
                    1 =>"1",
                    2,3,4 => "2",
                    5 => "3",
                    6 => "4",
                    7,8 => "5",
                };
            ',
        ];
    }
}
