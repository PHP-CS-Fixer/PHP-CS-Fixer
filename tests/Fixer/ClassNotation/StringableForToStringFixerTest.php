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
 * @covers \PhpCsFixer\Fixer\ClassNotation\StringableForToStringFixer
 *
 * @requires PHP 8.0
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\StringableForToStringFixer>
 *
 * @author Santiago San Martin <sanmartindev@gmail.com>
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class StringableForToStringFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php class Foo {}'];

        yield ['<?php class Foo { public function toString() { return "Foo"; } }'];

        yield ['<?php class Foo implements STRINGABLE  { public function __toString() { return "Foo"; } }'];

        yield ['<?php class Foo implements Stringable  { public function __toString() { return "Foo"; } }'];

        yield ['<?php class Foo implements \Stringable { public function __toString() { return "Foo"; } }'];

        yield ['<?php
            namespace Foo;
            use Stringable;
            class Bar implements Stringable {
                public function __toString() { return ""; }
            }
        '];

        yield ['<?php
            use Stringable as Stringy;
            class Bar implements Stringy {
                public function __toString() { return ""; }
            }
        '];

        yield ['<?php
            namespace Foo;
            use Stringable as Stringy;
            class Bar implements Stringy {
                public function __toString() { return ""; }
            }
        '];

        yield ['<?php
            namespace Foo;
            use \Stringable;
            class Bar implements Stringable {
                public function __toString() { return ""; }
            }
        '];

        yield ['<?php
            namespace Foo;
            use Bar;
            use STRINGABLE;
            use Baz;
            class Qux implements Stringable {
                public function __toString() { return ""; }
            }
        '];

        yield ['<?php class Foo {
                    public function toString() {
                    function () { return 0; };
                        return "Foo";
                    }
                }'];

        yield ['<?php class Foo
            {
                public function bar() {
                    $ohject->__toString();
                }
            }'];

        yield [
            '<?php class Foo implements \Stringable
            {
                public function __toString() { return "Foo"; }
            }
            ',
            '<?php class Foo
            {
                public function __toString() { return "Foo"; }
            }
            ',
        ];

        yield [
            '<?php namespace FooNamespace;
            class Foo implements \Stringable
            {
                public function __toString() { return "Foo"; }
            }
            ',
            '<?php namespace FooNamespace;
            class Foo
            {
                public function __toString() { return "Foo"; }
            }
            ',
        ];

        yield [
            '<?php namespace FooNamespace;
            use Bar\Stringable;
            class Foo implements \Stringable, Stringable
            {
                public function __toString() { return "Foo"; }
            }
            ',
            '<?php namespace FooNamespace;
            use Bar\Stringable;
            class Foo implements Stringable
            {
                public function __toString() { return "Foo"; }
            }
            ',
        ];

        yield [
            <<<'PHP'
                <?php
                use NotStringable as Stringy;
                class Bar implements \Stringable, Stringy {
                    public function __toString() { return ""; }
                }
                PHP,
            <<<'PHP'
                <?php
                use NotStringable as Stringy;
                class Bar implements Stringy {
                    public function __toString() { return ""; }
                }
                PHP,
        ];

        $template = '<?php
            namespace FooNamespace;
            class Test implements %s
            {
                public function __toString() { return "Foo"; }
            }
        ';
        $implementedInterfacesCases = [
            \Stringable::class,
            'Foo\Stringable',
            '\Foo\Stringable',
            'Foo\Stringable\Bar',
            '\Foo\Stringable\Bar',
            'Foo\Stringable, Bar\Stringable',
            'Stringable\Foo, Stringable\Bar',
            '\Stringable\Foo, Stringable\Bar',
            'Foo\Stringable\Bar',
            '\Foo\Stringable\Bar',
        ];

        foreach ($implementedInterfacesCases as $implementedInterface) {
            yield [
                \sprintf($template, '\Stringable, '.$implementedInterface),
                \sprintf($template, $implementedInterface),
            ];
        }

        yield [
            '<?php class Foo implements \Stringable, FooInterface
            {
                public function __toString() { return "Foo"; }
            }
            ',
            '<?php class Foo implements FooInterface
            {
                public function __toString() { return "Foo"; }
            }
            ',
        ];

        yield [
            '<?php class Foo extends Bar implements \Stringable
            {
                public function __toString() { return "Foo"; }
            }
            ',
            '<?php class Foo extends Bar
            {
                public function __toString() { return "Foo"; }
            }
            ',
        ];

        yield [
            '<?php class Foo implements \Stringable
            {
                public function __TOSTRING() { return "Foo"; }
            }
            ',
            '<?php class Foo
            {
                public function __TOSTRING() { return "Foo"; }
            }
            ',
        ];

        yield [
            '<?php
                namespace Foo;
                use Bar;
                class Baz implements \Stringable, Stringable {
                    public function __toString() { return ""; }
                }
            ',
            '<?php
                namespace Foo;
                use Bar;
                class Baz implements Stringable {
                    public function __toString() { return ""; }
                }
            ',
        ];

        yield [
            '<?php new class implements \Stringable {
                public function __construct() {}
                public function __toString() {}
            };
            ',
            '<?php new class {
                public function __construct() {}
                public function __toString() {}
            };
            ',
        ];

        yield [
            '<?php new class() implements \Stringable {
                public function __construct() {}
                public function __toString() {}
            };
            ',
            '<?php new class() {
                public function __construct() {}
                public function __toString() {}
            };
            ',
        ];

        yield [
            '<?php
            class Foo1 implements \Stringable { public function __toString() { return "1"; } }
            class Foo2 { public function __noString() { return "2"; } }
            class Foo3 implements \Stringable { public function __toString() { return "3"; } }
            class Foo4 { public function __noString() { return "4"; } }
            class Foo5 { public function __noString() { return "5"; } }
            ',
            '<?php
            class Foo1 { public function __toString() { return "1"; } }
            class Foo2 { public function __noString() { return "2"; } }
            class Foo3 { public function __toString() { return "3"; } }
            class Foo4 { public function __noString() { return "4"; } }
            class Foo5 { public function __noString() { return "5"; } }
            ',
        ];

        yield [
            '<?php
                namespace Foo { class C implements \Stringable, I { public function __toString() { return ""; } }}
                namespace Bar { class C implements \Stringable, I { public function __toString() { return ""; } }}
                namespace Baz { class C implements I, \Stringable { public function __toString() { return ""; } }}
                namespace Qux { class C implements \Stringable, I { public function __toString() { return ""; } }}
            ;
            ',
            '<?php
                namespace Foo { class C implements I { public function __toString() { return ""; } }}
                namespace Bar { class C implements \Stringable, I { public function __toString() { return ""; } }}
                namespace Baz { class C implements I, \Stringable { public function __toString() { return ""; } }}
                namespace Qux { class C implements I { public function __toString() { return ""; } }}
            ;
            ',
        ];

        yield [
            '<?php
                namespace Foo { use Stringable as Stringy; class C {} }
                namespace Bar { class C implements \Stringable, Stringy { public function __toString() { return ""; } }}
            ;
            ',
            '<?php
                namespace Foo { use Stringable as Stringy; class C {} }
                namespace Bar { class C implements Stringy { public function __toString() { return ""; } }}
            ;
            ',
        ];

        yield ['<?php
            namespace Foo;
            use Stringable;
            class Bar {
                public function foo() {
                    new class () implements Stringable {
                        public function __toString() { return ""; }
                    };
                }
            }
        '];
    }
}
