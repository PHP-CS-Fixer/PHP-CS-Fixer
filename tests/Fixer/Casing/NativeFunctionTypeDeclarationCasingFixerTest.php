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

use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer>
 *
 * @covers \PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer
 */
final class NativeFunctionTypeDeclarationCasingFixerTest extends AbstractFixerTestCase
{
    public function testFunctionIsDeprecatedProperly(): void
    {
        $fixer = $this->fixer;

        self::assertInstanceOf(DeprecatedFixerInterface::class, $fixer);
        self::assertSame(
            ['native_type_declaration_casing'],
            $fixer->getSuccessorsNames(),
        );
    }

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
        yield from NativeTypeDeclarationCasingFixerTest::provideFixCases();
    }
}
