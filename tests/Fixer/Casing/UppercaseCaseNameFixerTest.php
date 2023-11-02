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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Torben Nordtorp <torben.nordtorp@icloud.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\UppercaseCaseNameFixer
 */
final class UppercaseCaseNameFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP 8.1
     *
     * @dataProvider provideFix81Cases
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input, self::getTestFile(__FILE__));
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'normal enum' => [
            '<?php enum Enum { case HELLO; }',
            '<?php enum Enum { case hello; }',
        ];

        yield 'enum with default case' => [
            '<?php enum Enum: string { case BYE = "str"; }',
            '<?php enum Enum: string { case bYe = "str"; }',
        ];
    }
}
