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

namespace PhpCsFixer\Tests;

use PhpCsFixer\FixerNameValidator;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerNameValidator
 */
final class FixerNameValidatorTest extends TestCase
{
    /**
     * @dataProvider provideIsValidCases
     */
    public function testIsValid(string $name, bool $isCustom, bool $isValid): void
    {
        $validator = new FixerNameValidator();

        static::assertSame($isValid, $validator->isValid($name, $isCustom));
    }

    public static function provideIsValidCases(): array
    {
        return [
            ['', true, false],
            ['', false, false],
            ['foo', true, false],
            ['foo', false, true],
            ['foo_bar', false, true],
            ['foo_bar_4', false, true],
            ['Foo', false, false],
            ['fooBar', false, false],
            ['4foo', false, false],
            ['_foo', false, false],
            ['4_foo', false, false],
            ['vendor/foo', false, false],
            ['bendor/foo', true, false],
            ['Vendor/foo', true, true],
            ['Vendor4/foo', true, true],
            ['4vendor/foo', true, false],
            ['Vendor/foo', true, true],
            ['FooBar/foo', true, true],
            ['Foo-Bar/foo', true, false],
            ['Foo_Bar/foo', true, false],
            ['Foo/foo/bar', true, false],
            ['/foo', true, false],
            ['/foo', false, false],
            ['/foo/bar', true, false],
        ];
    }
}
