<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\MultilineEndingArgumentWithCommaFixer
 */
final class MultilineEndingArgumentWithCommaFixerTest extends AbstractFixerTestCase
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
        yield 'test last argument without comma' => [
            "<?php abc(\n\$a,\n);",
            "<?php abc(\n\$a\n);",
        ];
    }
}
