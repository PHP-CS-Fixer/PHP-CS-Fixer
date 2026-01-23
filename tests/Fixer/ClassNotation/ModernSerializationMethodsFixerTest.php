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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ModernSerializationMethodsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\ModernSerializationMethodsFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ModernSerializationMethodsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'rename when only old methods exists' => [
            <<<'PHP'
                <?php class Foo {
                    public function __serialize() {}
                    public function __unserialize(array $data) {}
                }
                PHP,
            <<<'PHP'
                <?php class Foo {
                    public function __sleep() {}
                    public function __wakeup() {}
                }
                PHP,
        ];

        yield 'do not rename when new methods already exists' => [
            <<<'PHP'
                <?php class Foo {
                    public function __serialize() {}
                    public function __unserialize(array $data) {}
                    public function __sleep() {}
                    public function __wakeup() {}
                }
                PHP,
        ];

        yield 'rename only when only new methods do not exist' => [
            <<<'PHP'
                <?php
                class Foo {
                    public function __serialize() {}
                    public function __sleep() {}
                    public function __unserialize(array $data) {}
                }
                class Bar {
                    public function __serialize() {}
                    public function __unserialize(array $data) {}
                    public function __wakeup() {}
                }
                PHP,
            <<<'PHP'
                <?php
                class Foo {
                    public function __serialize() {}
                    public function __sleep() {}
                    public function __wakeup() {}
                }
                class Bar {
                    public function __sleep() {}
                    public function __unserialize(array $data) {}
                    public function __wakeup() {}
                }
                PHP,
        ];

        yield 'do not touch constants' => [
            <<<'PHP'
                <?php class Foo {
                    public const __sleep = 'nice try';
                    public function __unserialize(array $data) {}
                }
                PHP,
            <<<'PHP'
                <?php class Foo {
                    public const __sleep = 'nice try';
                    public function __wakeup() {}
                }
                PHP,
        ];
    }
}
