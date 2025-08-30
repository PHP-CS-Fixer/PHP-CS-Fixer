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

namespace PhpCsFixer\Tests\FixerConfiguration;

use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\FixerOption
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FixerOptionTest extends TestCase
{
    public function testGetName(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        self::assertSame('foo', $option->getName());
    }

    public function testGetDescription(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        self::assertSame('Bar.', $option->getDescription());
    }

    public function testHasDefault(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        self::assertFalse($option->hasDefault());

        $option = new FixerOption('foo', 'Bar.', false, 'baz');
        self::assertTrue($option->hasDefault());
    }

    public function testGetDefault(): void
    {
        $option = new FixerOption('foo', 'Bar.', false, 'baz');
        self::assertSame('baz', $option->getDefault());
    }

    public function testGetUndefinedDefault(): void
    {
        $option = new FixerOption('foo', 'Bar.');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No default value defined.');
        $option->getDefault();
    }

    public function testGetAllowedTypes(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        self::assertNull($option->getAllowedTypes());

        $option = new FixerOption('foo', 'Bar.', true, null, ['bool']);
        self::assertSame(['bool'], $option->getAllowedTypes());

        $option = new FixerOption('foo', 'Bar.', true, null, ['bool', 'string']);
        self::assertSame(['bool', 'string'], $option->getAllowedTypes());
    }

    public function testGetAllowedValues(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        self::assertNull($option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, ['baz']);
        self::assertSame(['baz'], $option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, ['baz', 'qux']);
        self::assertSame(['baz', 'qux'], $option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, [static fn () => true]);
        $allowedTypes = $option->getAllowedValues();
        self::assertIsArray($allowedTypes);
        self::assertCount(1, $allowedTypes);
        self::assertArrayHasKey(0, $allowedTypes);
        self::assertInstanceOf(\Closure::class, $allowedTypes[0]);
    }

    public function testGetNormalizers(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        self::assertNull($option->getNormalizer());

        $option = new FixerOption('foo', 'Bar.', true, null, null, null, static fn () => null);
        self::assertInstanceOf(\Closure::class, $option->getNormalizer());
    }

    public function testRequiredWithDefaultValue(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Required options cannot have a default value.');

        new FixerOption('foo', 'Bar.', true, false);
    }
}
