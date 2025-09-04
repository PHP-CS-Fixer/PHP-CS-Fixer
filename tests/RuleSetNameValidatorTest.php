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
 * @internal
 *
 * @coversNothing
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RuleSetNameValidatorTest extends TestCase
{
    /**
     * @dataProvider provideIsValidOkCases
     */
    public function testIsValidOk(string $name, bool $isCustom): void
    {
        self::assertTrue(RuleSetNameValidator::isValid($name, $isCustom));
    }

    /**
     * @return iterable<string, array{0: string, 1: bool}>
     */
    public static function provideIsValidOkCases(): iterable
    {
        yield 'Built-in' => ['@Foo', false];

        yield 'Built-in risky' => ['@Foo:risky', false];

        yield 'Built-in with sub-namespace' => ['@PhpCsFixer/testing', false];

        yield 'Built-in with dot-based namespace' => ['@PhpCsFixer.testing', false];

        yield 'Built-in with hyphen' => ['@PER-CS', false];

        yield 'Simple name' => ['@Vendor/MyRules', true];

        yield 'Simple name risky' => ['@Vendor/MyRules:risky', true];

        yield 'Versioned with dash and X.Y' => ['@Vendor/MyRules-1.0', true];

        yield 'Versioned with underscores' => ['@Vendor/MyRules_1_0', true];

        yield 'Short name 1' => ['@Vendor/X', true];

        yield 'Short name 2' => ['@Vendor/y', true];
    }

    /**
     * @dataProvider provideIsValidBadNameCases
     */
    public function testIsValidBadName(string $name, bool $isCustom): void
    {
        self::assertFalse(RuleSetNameValidator::isValid($name, $isCustom));
    }

    /**
     * @return iterable<string, array{0: string, 1: bool}>
     */
    public static function provideIsValidBadNameCases(): iterable
    {
        yield 'Built-in without @' => ['Foo', false];

        yield 'Built-in starting with number' => ['@100rules', false];

        yield 'Built-in containing @ in the middle' => ['@Foo@Bar', false];

        yield 'Does not start with @' => ['Vendor/MyRules', true];

        yield 'Contains comma 1' => ['MyRules,', true];

        yield 'Contains comma 2' => ['@MyRules,', true];

        yield 'Contains hash' => ['@MyRules#', true];
    }
}
