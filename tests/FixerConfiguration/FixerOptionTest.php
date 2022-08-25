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
 */
final class FixerOptionTest extends TestCase
{
    public function testGetName(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        static::assertSame('foo', $option->getName());
    }

    public function testGetDescription(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        static::assertSame('Bar.', $option->getDescription());
    }

    public function testHasDefault(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        static::assertFalse($option->hasDefault());

        $option = new FixerOption('foo', 'Bar.', false, 'baz');
        static::assertTrue($option->hasDefault());
    }

    public function testGetDefault(): void
    {
        $option = new FixerOption('foo', 'Bar.', false, 'baz');
        static::assertSame('baz', $option->getDefault());
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
        static::assertNull($option->getAllowedTypes());

        $option = new FixerOption('foo', 'Bar.', true, null, ['bool']);
        static::assertSame(['bool'], $option->getAllowedTypes());

        $option = new FixerOption('foo', 'Bar.', true, null, ['bool', 'string']);
        static::assertSame(['bool', 'string'], $option->getAllowedTypes());
    }

    public function testGetAllowedValues(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        static::assertNull($option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, ['baz']);
        static::assertSame(['baz'], $option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, ['baz', 'qux']);
        static::assertSame(['baz', 'qux'], $option->getAllowedValues());

        $option = new FixerOption('foo', 'Bar.', true, null, null, [static fn () => true]);
        $allowedTypes = $option->getAllowedValues();
        static::assertIsArray($allowedTypes);
        static::assertCount(1, $allowedTypes);
        static::assertArrayHasKey(0, $allowedTypes);
        static::assertInstanceOf(\Closure::class, $allowedTypes[0]);
    }

    public function testGetNormalizers(): void
    {
        $option = new FixerOption('foo', 'Bar.');
        static::assertNull($option->getNormalizer());

        $option = new FixerOption('foo', 'Bar.', true, null, null, null, static fn () => null);
        static::assertInstanceOf(\Closure::class, $option->getNormalizer());
    }

    public function testRequiredWithDefaultValue(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Required options cannot have a default value.');

        new FixerOption('foo', 'Bar.', true, false);
    }
}
