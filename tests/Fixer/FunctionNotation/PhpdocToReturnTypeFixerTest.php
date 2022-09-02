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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\PhpdocToReturnTypeFixer
 */
final class PhpdocToReturnTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, ?int $versionSpecificFix = null, array $config = []): void
    {
        if (
            null !== $input
            && (null !== $versionSpecificFix && \PHP_VERSION_ID < $versionSpecificFix)
        ) {
            $expected = $input;
            $input = null;
        }

        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield from [
            'no phpdoc return' => [
                '<?php function my_foo() {}',
            ],
            'invalid return' => [
                '<?php /** @return */ function my_foo() {}',
            ],
            'invalid class 1' => [
                '<?php /** @return \9 */ function my_foo() {}',
            ],
            'invalid class 2' => [
                '<?php /** @return \\Foo\\\\Bar */ function my_foo() {}',
            ],
            'invalid class 3' => [
                '<?php /** @return Break */ function my_foo() {}',
            ],
            'invalid class 4' => [
                '<?php /** @return __CLASS__ */ function my_foo() {}',
            ],
            'invalid class 5' => [
                '<?php /** @return I\Want\To\Bre\\\\ak\Free */ function queen() {}',
            ],
            'excluded class methods' => [
                '<?php

                    class Foo
                    {
                        /** @return Bar */
                        function __construct() {}
                        /** @return Bar */
                        function __destruct() {}
                        /** @return Bar */
                        function __clone() {}
                    }
                ',
            ],
            'multiple returns' => [
                '<?php

                    /**
                     * @return Bar
                     * @return Baz
                     */
                    function xyz() {}
                ',
            ],
            'non-root class' => [
                '<?php /** @return Bar */ function my_foo(): Bar {}',
                '<?php /** @return Bar */ function my_foo() {}',
            ],
            'non-root namespaced class' => [
                '<?php /** @return My\Bar */ function my_foo(): My\Bar {}',
                '<?php /** @return My\Bar */ function my_foo() {}',
            ],
            'root class' => [
                '<?php /** @return \My\Bar */ function my_foo(): \My\Bar {}',
                '<?php /** @return \My\Bar */ function my_foo() {}',
            ],
            'interface' => [
                '<?php interface Foo { /** @return Bar */ function my_foo(): Bar; }',
                '<?php interface Foo { /** @return Bar */ function my_foo(); }',
            ],
            'void return on ^7.1' => [
                '<?php /** @return void */ function my_foo(): void {}',
                '<?php /** @return void */ function my_foo() {}',
            ],
            'invalid void return on ^7.1' => [
                '<?php /** @return null|void */ function my_foo() {}',
            ],
            'iterable return on ^7.1' => [
                '<?php /** @return iterable */ function my_foo(): iterable {}',
                '<?php /** @return iterable */ function my_foo() {}',
            ],
            'object return on ^7.2' => [
                '<?php /** @return object */ function my_foo(): object {}',
                '<?php /** @return object */ function my_foo() {}',
                70200,
            ],
            'fix scalar types by default, int' => [
                '<?php /** @return int */ function my_foo(): int {}',
                '<?php /** @return int */ function my_foo() {}',
            ],
            'fix scalar types by default, float' => [
                '<?php /** @return float */ function my_foo(): float {}',
                '<?php /** @return float */ function my_foo() {}',
            ],
            'fix scalar types by default, string' => [
                '<?php /** @return string */ function my_foo(): string {}',
                '<?php /** @return string */ function my_foo() {}',
            ],
            'fix scalar types by default, bool' => [
                '<?php /** @return bool */ function my_foo(): bool {}',
                '<?php /** @return bool */ function my_foo() {}',
            ],
            'fix scalar types by default, bool, make unqualified' => [
                '<?php /** @return \bool */ function my_foo(): bool {}',
                '<?php /** @return \bool */ function my_foo() {}',
            ],
            'fix scalar types by default, false' => [
                '<?php /** @return false */ function my_foo(): bool {}',
                '<?php /** @return false */ function my_foo() {}',
            ],
            'fix scalar types by default, true' => [
                '<?php /** @return true */ function my_foo(): bool {}',
                '<?php /** @return true */ function my_foo() {}',
            ],
            'do not fix scalar types when configured as such' => [
                '<?php /** @return int */ function my_foo() {}',
                null,
                null,
                ['scalar_types' => false],
            ],
            'array native type' => [
                '<?php /** @return array */ function my_foo(): array {}',
                '<?php /** @return array */ function my_foo() {}',
            ],
            'callable type' => [
                '<?php /** @return callable */ function my_foo(): callable {}',
                '<?php /** @return callable */ function my_foo() {}',
            ],
            'self accessor' => [
                '<?php
                    class Foo {
                        /** @return self */ function my_foo(): self {}
                    }
                ',
                '<?php
                    class Foo {
                        /** @return self */ function my_foo() {}
                    }
                ',
            ],
            'nullable self accessor' => [
                '<?php
                    class Foo {
                        /** @return self|null */ function my_foo(): ?self {}
                    }
                ',
                '<?php
                    class Foo {
                        /** @return self|null */ function my_foo() {}
                    }
                ',
            ],
            'skip resource special type' => [
                '<?php /** @return resource */ function my_foo() {}',
            ],
            'null alone cannot be a return type' => [
                '<?php /** @return null */ function my_foo() {}',
            ],
            'skip mixed types' => [
                '<?php /** @return Foo|Bar */ function my_foo() {}',
            ],
            'nullable type' => [
                '<?php /** @return null|Bar */ function my_foo(): ?Bar {}',
                '<?php /** @return null|Bar */ function my_foo() {}',
            ],
            'nullable type reverse order' => [
                '<?php /** @return Bar|null */ function my_foo(): ?Bar {}',
                '<?php /** @return Bar|null */ function my_foo() {}',
            ],
            'nullable native type' => [
                '<?php /** @return null|array */ function my_foo(): ?array {}',
                '<?php /** @return null|array */ function my_foo() {}',
            ],
            'skip primitive or array types' => [
                '<?php /** @return string|string[] */ function my_foo() {}',
            ],
            'skip mixed nullable types' => [
                '<?php /** @return null|Foo|Bar */ function my_foo() {}',
            ],
            'generics' => [
                '<?php /** @return array<int, bool> */ function my_foo(): array {}',
                '<?php /** @return array<int, bool> */ function my_foo() {}',
            ],
            'array of types' => [
                '<?php /** @return Foo[] */ function my_foo(): array {}',
                '<?php /** @return Foo[] */ function my_foo() {}',
            ],
            'array of array of types' => [
                '<?php /** @return Foo[][] */ function my_foo(): array {}',
                '<?php /** @return Foo[][] */ function my_foo() {}',
            ],
            'nullable array of types' => [
                '<?php /** @return null|Foo[] */ function my_foo(): ?array {}',
                '<?php /** @return null|Foo[] */ function my_foo() {}',
            ],
            'comments' => [
                '<?php
                    class A
                    {
                        // comment 0
                        /** @return Foo */ # comment 1
                        final/**/public/**/static/**/function/**/bar/**/(/**/$var/**/=/**/1/**/): Foo/**/{# comment 2
                        } // comment 3
                    }
                ',
                '<?php
                    class A
                    {
                        // comment 0
                        /** @return Foo */ # comment 1
                        final/**/public/**/static/**/function/**/bar/**/(/**/$var/**/=/**/1/**/)/**/{# comment 2
                        } // comment 3
                    }
                ',
            ],
            'array and traversable' => [
                '<?php /** @return array|Traversable */ function my_foo(): iterable {}',
                '<?php /** @return array|Traversable */ function my_foo() {}',
            ],
            'array and traversable with leading slash' => [
                '<?php /** @return array|\Traversable */ function my_foo(): iterable {}',
                '<?php /** @return array|\Traversable */ function my_foo() {}',
            ],
            'array and traversable in a namespace' => [
                '<?php
                     namespace App;
                     /** @return array|Traversable */
                     function my_foo() {}
                ',
            ],
            'array and traversable with leading slash in a namespace' => [
                '<?php
                     namespace App;
                     /** @return array|\Traversable */
                     function my_foo(): iterable {}
                ',
                '<?php
                     namespace App;
                     /** @return array|\Traversable */
                     function my_foo() {}
                ',
            ],
            'array and imported traversable in a namespace' => [
                '<?php
                     namespace App;
                     use Traversable;
                     /** @return array|Traversable */
                     function my_foo(): iterable {}
                ',
                '<?php
                     namespace App;
                     use Traversable;
                     /** @return array|Traversable */
                     function my_foo() {}
                ',
            ],
            'array and object aliased as traversable in a namespace' => [
                '<?php
                     namespace App;
                     use Foo as Traversable;
                     /** @return array|Traversable */
                     function my_foo() {}
                ',
            ],
            'array of object and traversable' => [
                '<?php /** @return Foo[]|Traversable */ function my_foo(): iterable {}',
                '<?php /** @return Foo[]|Traversable */ function my_foo() {}',
            ],
            'array of object and iterable' => [
                '<?php /** @return Foo[]|iterable */ function my_foo(): iterable {}',
                '<?php /** @return Foo[]|iterable */ function my_foo() {}',
            ],
            'array of string and array of int' => [
                '<?php /** @return string[]|int[] */ function my_foo(): array {}',
                '<?php /** @return string[]|int[] */ function my_foo() {}',
            ],
            'intersection types' => [
                '<?php
                    /** @return Bar&Baz */
                    function bar() {}
                ',
            ],
            'very long class name before ampersand' => [
                '<?php
                    /** @return Baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaar&Baz */
                    function bar() {}
                ',
            ],
            'very long class name after ampersand' => [
                '<?php
                    /** @return Bar&Baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaz */
                    function bar() {}
                ',
            ],
            'arrow function' => [
                '<?php /** @return int */ fn(): int => 1;',
                '<?php /** @return int */ fn() => 1;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPre80Cases(): iterable
    {
        yield 'report static as self' => [
            '<?php
                class Foo {
                    /** @return static */ function my_foo(): self {}
                }
            ',
            '<?php
                class Foo {
                    /** @return static */ function my_foo() {}
                }
            ',
        ];

        yield 'skip mixed special type' => [
            '<?php /** @return mixed */ function my_foo() {}',
        ];
    }

    /**
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPhp80Cases(): iterable
    {
        yield 'static' => [
            '<?php
                final class Foo {
                    /** @return static */
                    public function something(): static {}
                }
            ',
            '<?php
                final class Foo {
                    /** @return static */
                    public function something() {}
                }
            ',
        ];

        yield 'nullable static' => [
            '<?php
                final class Foo {
                    /** @return null|static */
                    public function something(): ?static {}
                }
            ',
            '<?php
                final class Foo {
                    /** @return null|static */
                    public function something() {}
                }
            ',
        ];

        yield 'mixed' => [
            '<?php
                final class Foo {
                    /** @return mixed */
                    public function something(): mixed {}
                }
            ',
            '<?php
                final class Foo {
                    /** @return mixed */
                    public function something() {}
                }
            ',
        ];
    }
}
