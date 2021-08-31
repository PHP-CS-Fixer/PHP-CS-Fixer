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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\ClassKeywordRemoveFixer
 */
final class ClassKeywordRemoveFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            [
                "<?php
                use Foo\\Bar\\Thing;

                echo 'Foo\\Bar\\Thing';
                ",
                '<?php
                use Foo\Bar\Thing;

                echo Thing::class;
                ',
            ],
            [
                '<?php
                use Foo\\Bar;
            '."
                echo 'Foo\\Bar\\Thing';
                ",
                '<?php
                use Foo\Bar;
            '.'
                echo Bar\Thing::class;
                ',
            ],
            [
                "<?php
                namespace Foo;
                use Foo\\Bar;
                echo 'Foo\\Bar\\Baz';
                ",
                '<?php
                namespace Foo;
                use Foo\\Bar;
                echo \\Foo\\Bar\\Baz::class;
                ',
            ],
            [
                "<?php
                use Foo\\Bar\\Thing as Alias;

                echo 'Foo\\Bar\\Thing';
                ",
                '<?php
                use Foo\Bar\Thing as Alias;

                echo Alias::class;
                ',
            ],
            [
                "<?php
                use Foo\\Bar\\Dummy;
                use Foo\\Bar\\Thing as Alias;

                echo 'Foo\\Bar\\Dummy';
                echo 'Foo\\Bar\\Thing';
                ",
                '<?php
                use Foo\Bar\Dummy;
                use Foo\Bar\Thing as Alias;

                echo Dummy::class;
                echo Alias::class;
                ',
            ],
            [
                "<?php
                echo 'DateTime';
                ",
                '<?php
                echo \DateTime::class;
                ',
            ],
            [
                "<?php
                echo 'Thing';
                ",
                '<?php
                echo Thing::class;
                ',
            ],
            [
                "<?php
                class Foo {
                    public function amazingFunction() {
                        echo 'Thing';
                    }
                }
                ",
                '<?php
                class Foo {
                    public function amazingFunction() {
                        echo Thing::class;
                    }
                }
                ',
            ],
            [
                "<?php
                namespace A\\B;

                use Foo\\Bar;

                echo 'Foo\\Bar';
                ",
                '<?php
                namespace A\B;

                use Foo\Bar;

                echo Bar::class;
                ',
            ],
            [
                "<?php

                namespace A\\B {

                    class D {

                    }
                }

                namespace B\\B {
                    class D {

                    }
                }

                namespace C {
                    use A\\B\\D;
                    var_dump('A\\B\\D');
                }

                namespace C1 {
                    use B\\B\\D;
                    var_dump('B\\B\\D');
                }
                ",
                '<?php

                namespace A\B {

                    class D {

                    }
                }

                namespace B\B {
                    class D {

                    }
                }

                namespace C {
                    use A\B\D;
                    var_dump(D::class);
                }

                namespace C1 {
                    use B\B\D;
                    var_dump(D::class);
                }
                ',
            ],
            [
                '<?php
                namespace Foo;
                class Bar extends Baz {
                    public function a() {
                        return self::class;
                    }
                    public function b() {
                        return static::class;
                    }
                    public function c() {
                        return parent::class;
                    }
                }
                ',
            ],
            [
                "<?php
                namespace Foo;
                var_dump('Foo\\Bar\\Baz');
                ",
                '<?php
                namespace Foo;
                var_dump(Bar\\Baz::class);
                ',
            ],
            [
                "<?php
                namespace Foo\\Bar;
                var_dump('Foo\\Bar\\Baz');
                ",
                '<?php
                namespace Foo\\Bar;
                var_dump(Baz::class);
                ',
            ],
            [
                "<?php
                use Foo\\Bar\\{ClassA, ClassB, ClassC as C};
                use function Foo\\Bar\\{fn_a, fn_b, fn_c};
                use const Foo\\Bar\\{ConstA, ConstB, ConstC};

                echo 'Foo\\Bar\\ClassB';
                echo 'Foo\\Bar\\ClassC';
                ",
                '<?php
                use Foo\Bar\{ClassA, ClassB, ClassC as C};
                use function Foo\Bar\{fn_a, fn_b, fn_c};
                use const Foo\Bar\{ConstA, ConstB, ConstC};

                echo ClassB::class;
                echo C::class;
                ',
                "<?php
                namespace {
                    var_dump('Foo');
                }
                namespace A {
                    use B\\C;
                    var_dump('B\\C');
                }
                namespace {
                    var_dump('Bar\\Baz');
                }
                namespace B {
                    use A\\C\\D;
                    var_dump('A\\C\\D');
                }
                namespace {
                    var_dump('Qux\\Quux');
                }
                ",
                '<?php
                namespace {
                    var_dump(Foo::class);
                }
                namespace A {
                    use B\\C;
                    var_dump(C::class);
                }
                namespace {
                    var_dump(Bar\\Baz::class);
                }
                namespace B {
                    use A\\C\\D;
                    var_dump(D::class);
                }
                namespace {
                    var_dump(Qux\\Quux::class);
                }
                ',
            ],
        ];
    }

    /**
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(): void
    {
        $this->doTest(
            "<?php echo 'DateTime'
# a
 /* b */?>
",
            '<?php echo \
DateTime:: # a
 /* b */ class?>
'
        );
    }

    /**
     * @requires PHP 8.0
     */
    public function testNotFixPHP8(): void
    {
        $this->doTest(
            "<?php
            echo 'Thing';
            echo \$thing::class;
            ",
            '<?php
            echo Thing::class;
            echo $thing::class;
            '
        );
    }
}
