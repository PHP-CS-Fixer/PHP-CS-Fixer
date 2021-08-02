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

use PhpCsFixer\RuleSetNameValidator;

/**
 * @author Krystian Marcisz <simivar@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\RuleSetNameValidator
 */
final class RuleSetNameValidatorTest extends TestCase
{
    /**
     * @dataProvider provideIsValidCases
     */
    public function testIsValid(string $name, bool $isCustom, bool $isValid): void
    {
        $validator = new RuleSetNameValidator();

        static::assertSame($isValid, $validator->isValid($name, $isCustom));
    }

    public function provideIsValidCases()
    {
        return [
            ['', true, false],
            ['', false, false],
            ['foo', true, false],
            ['Foo', true, false],
            ['@Foo', false, true],
            ['@FooBar', false, true],
            ['@Foo_Bar', false, false],
            ['@FooBar4', false, true],
            ['@Foo_Bar_4', false, false],
            ['@fooBar', false, false],
            ['@4foo', false, false],
            ['@_foo', false, false],
            ['@4_foo', false, false],
            ['@vendor/foo', false, false],
            ['@bendor/foo', true, false],
            ['@Vendor/foo', true, false],
            ['@Vendor/Foo', true, true],
            ['@Vendor4/foo', true, false],
            ['@Vendor4/Foo', true, true],
            ['@4vendor/foo', true, false],
            ['@4vendor/Foo', true, false],
            ['@FooBar/foo', true, false],
            ['@FooBar/Foo', true, true],
            ['@Foo-Bar/Foo', true, false],
            ['@Foo_Bar/Foo', true, false],
            ['@Foo/Foo/bar', true, false],
            ['@Foo/Foo/Bar', true, false],
            ['@/Foo', true, false],
            ['@/Foo', false, false],
            ['@/Foo/Bar', true, false],
        ];
    }
}
