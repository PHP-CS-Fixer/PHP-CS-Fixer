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
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoRedundantReadonlyPropertyFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\NoRedundantReadonlyPropertyFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoRedundantReadonlyPropertyFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @requires PHP 8.2
     */
    public function testFix(string $expected, ?string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'normal properties' => [
            <<<'PHP'
                <?php
                readonly class C1
                {
                    private int $bar;
                    private int $baz;
                }

                class C2
                {
                    private readonly int $bar;
                    private readonly int $baz;
                }
                PHP,
            <<<'PHP'
                <?php
                readonly class C1
                {
                    private readonly int $bar;
                    private readonly int $baz;
                }

                class C2
                {
                    private readonly int $bar;
                    private readonly int $baz;
                }
                PHP,
        ];

        yield 'trait with anonymous class' => [
            <<<'PHP'
                <?php
                trait T
                {
                    protected string $foo = 'bar';

                    public function setUp(): void
                    {
                        $x = new class() extends \stdClass {
                            public string $foo;
                        };
                    }
                }
                PHP,
            null,
        ];

        yield 'promoted properties' => [
            <<<'PHP'
                <?php
                readonly class C1
                {
                    public function __construct(
                        private int $bar,
                        private int $baz,
                    ) {
                    }
                }

                class C2
                {
                    public function __construct(
                        private readonly int $bar,
                        private readonly int $baz,
                    ) {
                    }
                }
                PHP,
            <<<'PHP'
                <?php
                readonly class C1
                {
                    public function __construct(
                        private readonly int $bar,
                        private readonly int $baz,
                    ) {
                    }
                }

                class C2
                {
                    public function __construct(
                        private readonly int $bar,
                        private readonly int $baz,
                    ) {
                    }
                }
                PHP,
        ];
    }
}
