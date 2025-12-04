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

namespace PhpCsFixer\Tests\Fixer\AttributeNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AttributeNotation\BlockIndicatorNoSpaceFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\AttributeNotation\BlockIndicatorNoSpaceFixer>
 *
 * @author Albin Kester <albin.kester@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class BlockIndicatorNoSpaceFixerTest extends AbstractFixerTestCase
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
class User
{
    #[ApiProperty(identifier: true)]
    private string $name;
}',
            '<?php
class User
{
    #[
        ApiProperty(identifier: true)
    ]
    private string $name;
}',
        ];

        yield [
            '<?php
class User
{
    #[ApiProperty]
    private string $name;
}',
            '<?php
class User
{
    #[     ApiProperty    ]
    private string $name;
}',
        ];
    }
}
