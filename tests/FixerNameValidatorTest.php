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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FixerNameValidatorTest extends TestCase
{
    /**
     * @dataProvider provideIsValidCases
     */
    public function testIsValid(string $name, bool $isCustom, bool $isValid): void
    {
        $validator = new FixerNameValidator();

        self::assertSame($isValid, $validator->isValid($name, $isCustom));
    }

    /**
     * @return iterable<int, array{string, bool, bool}>
     */
    public static function provideIsValidCases(): iterable
    {
        yield ['', true, false];

        yield ['', false, false];

        yield ['foo', true, false];

        yield ['foo', false, true];

        yield ['foo_bar', false, true];

        yield ['foo_bar_4', false, true];

        yield ['Foo', false, false];

        yield ['fooBar', false, false];

        yield ['4foo', false, false];

        yield ['_foo', false, false];

        yield ['4_foo', false, false];

        yield ['vendor/foo', false, false];

        yield ['bendor/foo', true, false];

        yield ['Vendor/foo', true, true];

        yield ['Vendor4/foo', true, true];

        yield ['4vendor/foo', true, false];

        yield ['FooBar/foo', true, true];

        yield ['Foo-Bar/foo', true, false];

        yield ['Foo_Bar/foo', true, false];

        yield ['Foo/foo/bar', true, false];

        yield ['/foo', true, false];

        yield ['/foo', false, false];

        yield ['/foo/bar', true, false];
    }
}
