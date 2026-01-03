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

use PhpCsFixer\ComposerJsonReader;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\ComposerJsonReader
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ComposerJsonReaderTest extends TestCase
{
    public function testCreateSingleton(): void
    {
        $instance = ComposerJsonReader::createSingleton();

        self::assertSame($instance, ComposerJsonReader::createSingleton());
    }

    /**
     * @dataProvider provideGetPhpUnitCases
     */
    public function testGetPhpUnit(?string $expected, string $inputJson): void
    {
        self::assertJson($inputJson);

        $instance = new ComposerJsonReader();

        \Closure::bind(
            static fn ($instance) => $instance->processJson($inputJson),
            null,
            \get_class($instance),
        )($instance);

        self::assertSame($expected, $instance->getPhpUnit());
    }

    /**
     * @return iterable<string, array{0: ?string, 1: string}>
     */
    public static function provideGetPhpUnitCases(): iterable
    {
        yield 'no version' => [
            null,
            '{
    "require": {},
    "require-dev": {}
}',
        ];

        yield 'dev-master' => [
            null,
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "dev-master" }
}',
        ];

        yield 'dev-master or normal version' => [
            null,
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^10 || dev-master" }
}',
        ];

        yield 'regular version' => [
            '9.6',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "9.6" }
}',
        ];

        yield 'version with ^' => [
            '9.6',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^9.6.25" }
}',
        ];

        yield 'version with ~' => [
            '9.6',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "~9.6.25" }
}',
        ];

        yield 'version with >' => [
            '9.6',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": ">9.6.25" }
}',
        ];

        yield 'version with >=' => [
            '9.6',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": ">=9.6.25" }
}',
        ];

        yield 'version with >= and a space' => [
            '9.6',
            '{
    "require-dev": { "phpunit/phpunit": ">= 9.6.25" }
}',
        ];

        yield 'version with <' => [
            null, // not supported !
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^8 || <9.6.25" }
}',
        ];

        yield 'version with < but combined with normal version' => [
            '9.1',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^10 || ^9.1 <9.6.25" }
}',
        ];

        yield 'version with range separated by a space' => [
            '9.1',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": ">=9.1 <9.6.25" }
}',
        ];

        yield 'version with range separated by a comma' => [
            '9.1',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": ">=9.1,<9.6.25" }
}',
        ];

        yield 'version with range separated by a hyphen' => [
            '9.1',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "9.1-9.6.25" }
}',
        ];

        yield 'version with <=' => [
            null, // not supported !
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^8 || <=9.6.25" }
}',
        ];

        yield 'version with !=' => [
            null, // not supported !
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^8 || !=9.6.25" }
}',
        ];

        yield 'version with @dev' => [
            '9.6',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^9.6.25@dev" }
}',
        ];

        yield 'version with asterisk' => [
            '8.1',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "  8.1.*  ||  8.2.*  ||  8.3.*  ||  8.4.*  " }
}',
        ];

        yield 'alternation' => [
            '9.6',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^9.6.25 || ^10.5.53 || ^11.5.34" }
}',
        ];

        yield 'alternation in non-dev require' => [
            '9.6',
            '{
    "require": { "phpunit/phpunit": "^9.6.25 || ^10.5.53 || ^11.5.34" },
    "require-dev": {}
}',
        ];

        yield 'alternation with deprecated single |' => [
            '9.6',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^9.6.25 | ^10.5.53 | ^11.5.34" }
}',
        ];

        yield 'alternation but oldest version not on start' => [
            '8.0',
            '{
    "require": {},
    "require-dev": { "phpunit/phpunit": "^9.6.25 || ~8 || ^11.5.34" }
}',
        ];

        yield 'merge require with require-dev A' => [
            '7.0',
            '{
    "require": { "phpunit/phpunit": "^8.1 || ~9 || ^10" },
    "require-dev": { "phpunit/phpunit": "^8.0 || ^7 || 11" }
}',
        ];

        yield 'merge require with require-dev B' => [
            '6.0',
            '{
    "require": { "phpunit/phpunit": "^8.1 || ~6 || ^10" },
    "require-dev": { "phpunit/phpunit": "^8.0 || ^7 || 11" }
}',
        ];
    }

    /**
     * @dataProvider provideGetPhpCases
     */
    public function testGetPhp(?string $expected, string $inputJson): void
    {
        self::assertJson($inputJson);

        $instance = new ComposerJsonReader();

        \Closure::bind(
            static fn ($instance) => $instance->processJson($inputJson),
            null,
            \get_class($instance),
        )($instance);

        self::assertSame($expected, $instance->getPhp());
    }

    /**
     * @return iterable<string, array{0: ?string, 1: string}>
     */
    public static function provideGetPhpCases(): iterable
    {
        yield 'all missing' => [
            null,
            '{}',
        ];

        yield 'standard usage' => [
            '8.4',
            '{
    "require": {
        "php": "^8.4"
    }
}',
        ];

        yield 'all mixed with prio for require' => [
            '7.0',
            '{
    "require": {
        "php": "^7.4 || ^7.0 || >=8"
    },
    "require-dev": {
        "php": "^7.4 || ^7.2 || >=8"
    },
    "config": {
        "platform": {
            "php": "7.3"
        }
    }
}',
        ];

        yield 'all mixed with prio for require-dev' => [
            '7.0',
            '{
    "require": {
        "php": "^7.4 || ^7.1 || >=8"
    },
    "require-dev": {
        "php": "^7.4 || ^7.0 || >=8"
    },
    "config": {
        "platform": {
            "php": "7.3"
        }
    }
}',
        ];

        yield 'all mixed with prio for platform' => [
            '7.0',
            '{
    "require": {
        "php": "^7.4 || ^7.1 || >=8"
    },
    "require-dev": {
        "php": "^7.4 || ^7.2 || >=8"
    },
    "config": {
        "platform": {
            "php": "7.0"
        }
    }
}',
        ];

        yield 'version with asterisk' => [
            '8.1',
            '{
    "require": { "php": "  8.1.*  ||  8.2.*  ||  8.3.*  ||  8.4.*  " }
}',
        ];

        yield 'version with >= and a space' => [
            '8.2',
            '{
    "require": { "php": ">= 8.2" }
}',
        ];
    }
}
