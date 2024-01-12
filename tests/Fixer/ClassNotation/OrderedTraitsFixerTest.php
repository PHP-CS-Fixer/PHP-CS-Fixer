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
 * @covers \PhpCsFixer\Fixer\ClassNotation\OrderedTraitsFixer
 *
 * @internal
 */
final class OrderedTraitsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param array<string, mixed> $configuration
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: ?string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A;
                    use B;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use B;
                    use A;
                }
                EOD,
        ];

        yield 'in multiple classes' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A;
                    use C;
                }
                class Bar {
                    use B;
                    use D;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use C;
                    use A;
                }
                class Bar {
                    use D;
                    use B;
                }
                EOD,
        ];

        yield 'separated by a property' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A;
                    use C;

                    private $foo;

                    use B;
                    use D;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use C;
                    use A;

                    private $foo;

                    use D;
                    use B;
                }
                EOD,
        ];

        yield 'separated by a method' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A;
                    use C;

                    public function foo() { }

                    use B;
                    use D;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use C;
                    use A;

                    public function foo() { }

                    use D;
                    use B;
                }
                EOD,
        ];

        yield 'grouped statements' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A, C;
                    use B;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use B;
                    use A, C;
                }
                EOD,
        ];

        yield 'with aliases and conflicts' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A {
                        A::foo insteadof B;
                        A::bar as bazA;
                        A::baz as protected;
                    }
                    use B {
                        B::bar as bazB;
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use B {
                        B::bar as bazB;
                    }
                    use A {
                        A::foo insteadof B;
                        A::bar as bazA;
                        A::baz as protected;
                    }
                }
                EOD,
        ];

        yield 'symbol imports' => [
            <<<'EOD'
                <?php
                use C;
                use B;
                use A;
                EOD,
        ];

        yield 'anonymous function with inherited variables' => [
            <<<'EOD'
                <?php
                $foo = function () use ($b, $a) { };
                $bar = function () use ($a, $b) { };
                EOD,
        ];

        yield 'multiple traits in a single statement' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A, B, C, D;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use C, B, D, A;
                }
                EOD,
        ];

        yield 'multiple traits per statement' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A, D;
                    use B, C;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use C, B;
                    use D, A;
                }
                EOD,
        ];

        $uses = [];
        for ($i = 0; $i < 25; ++$i) {
            $uses[] = sprintf('    use A%02d;', $i);
        }

        yield 'simple, multiple I' => [
            sprintf("<?php\nclass Foo {\n%s\n}", implode("\n", $uses)),
            sprintf("<?php\nclass Foo {\n%s\n}", implode("\n", array_reverse($uses))),
        ];

        yield 'simple, length diff. I' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A;
                    use B\B;
                    use C\C\C;
                    use D\D\D\D;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use D\D\D\D;
                    use C\C\C;
                    use B\B;
                    use A;
                }
                EOD,
        ];

        yield 'comments handling' => [
            <<<'EOD'
                <?php
                class Foo {
                    /* A */use A\A\A\A/* A */;
                    /* B */use B\B\B/* B */;
                    /* C */use C\C/* C */;
                    /* D */use D/* D */;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /* D */use D/* D */;
                    /* C */use C\C/* C */;
                    /* B */use B\B\B/* B */;
                    /* A */use A\A\A\A/* A */;
                }
                EOD,
        ];

        yield 'grouped statements II' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A\Z, C\Y;
                    use B\E;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use B\E;
                    use A\Z, C\Y;
                }
                EOD,
        ];

        yield 'simple, leading \\' => [
            <<<'EOD'
                <?php
                class Foo {
                    use \A;
                    use \B;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use \B;
                    use \A;
                }
                EOD,
        ];

        yield 'simple, leading \\ before character order' => [
            <<<'EOD'
                <?php
                class Foo {
                    use A;
                    use \B;
                    use C;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use C;
                    use \B;
                    use A;
                }
                EOD,
        ];

        yield 'with phpdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    // foo 1

                    /** @phpstan-use A<Foo> */
                    use A;
                    /** @phpstan-use B<Foo> */
                    use B;

                    /** @phpstan-use C<Foo> */
                    use C;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @phpstan-use C<Foo> */
                    use C;
                    /** @phpstan-use B<Foo> */
                    use B;

                    // foo 1

                    /** @phpstan-use A<Foo> */
                    use A;
                }
                EOD,
        ];

        yield 'simple and with namespace' => [
            <<<'EOD'
                <?php

                class User
                {
                    use Test\B, TestA;
                }
                EOD,
            <<<'EOD'
                <?php

                class User
                {
                    use TestA, Test\B;
                }
                EOD,
        ];

        yield 'with case sensitive order' => [
            <<<'EOD'
                <?php
                class Foo {
                    use AA;
                    use Aaa;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    use Aaa;
                    use AA;
                }
                EOD,
            [
                'case_sensitive' => true,
            ],
        ];
    }
}
