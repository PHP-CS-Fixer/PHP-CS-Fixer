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

use PhpCsFixer\FixerConfiguration\DeprecatedFixerOption;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\DeprecatedFixerOption
 */
final class DeprecatedFixerOptionTest extends TestCase
{
    public function testGetName(): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'deprecated'
        );

        self::assertSame('foo', $option->getName());
    }

    public function testGetDescription(): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'deprecated'
        );

        self::assertSame('Foo.', $option->getDescription());
    }

    /**
     * @dataProvider provideHasDefaultCases
     */
    public function testHasDefault(bool $isRequired): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', $isRequired),
            'deprecated'
        );

        self::assertSame(!$isRequired, $option->hasDefault());
    }

    /**
     * @return iterable<int, array{bool}>
     */
    public static function provideHasDefaultCases(): iterable
    {
        yield [true];

        yield [false];
    }

    /**
     * @param mixed $default
     *
     * @dataProvider provideGetDefaultCases
     */
    public function testGetDefault($default): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', false, $default),
            'deprecated'
        );

        self::assertSame($default, $option->getDefault());
    }

    /**
     * @return iterable<int, array{bool|string}>
     */
    public static function provideGetDefaultCases(): iterable
    {
        yield ['foo'];

        yield [true];
    }

    public function testGetAllowedTypes(): void
    {
        $allowedTypes = ['string', 'bool'];

        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', true, null, $allowedTypes),
            'deprecated'
        );

        self::assertSame($allowedTypes, $option->getAllowedTypes());
    }

    public function testGetAllowedValues(): void
    {
        $allowedValues = ['string', 'bool'];

        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', true, null, [], $allowedValues),
            'deprecated'
        );

        self::assertSame($allowedValues, $option->getAllowedValues());
    }

    public function testGetNormalizer(): void
    {
        $normalizer = static fn () => null;

        $option = new DeprecatedFixerOption(
            $this->createFixerOptionDouble($normalizer),
            'deprecated'
        );

        self::assertSame($normalizer, $option->getNormalizer());
    }

    public function testGetDeprecationMessage(): void
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'Use option "bar" instead.'
        );

        self::assertSame('Use option "bar" instead.', $option->getDeprecationMessage());
    }

    private function createFixerOptionDouble(\Closure $normalizer): FixerOptionInterface
    {
        return new class($normalizer) implements FixerOptionInterface {
            private \Closure $normalizer;

            public function __construct(\Closure $normalizer)
            {
                $this->normalizer = $normalizer;
            }

            public function getName(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function getDescription(): string
            {
                throw new \LogicException('Not implemented.');
            }

            public function hasDefault(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            /**
             * @return mixed
             *
             * @throws \LogicException when no default value is defined
             */
            public function getDefault()
            {
                throw new \LogicException('Not implemented.');
            }

            public function getAllowedTypes(): ?array
            {
                throw new \LogicException('Not implemented.');
            }

            public function getAllowedValues(): ?array
            {
                throw new \LogicException('Not implemented.');
            }

            public function getNormalizer(): \Closure
            {
                return $this->normalizer;
            }
        };
    }
}
