<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\NewFixer
 */
final class NewFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideInvalidWithBracesCases
     *
     * @param mixed $withBraces
     */
    public function testConfigureRejectsInvalidWithBraces($withBraces)
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure([
            'with_braces' => $withBraces,
        ]);
    }

    /**
     * @return array
     */
    public function provideInvalidWithBracesCases()
    {
        return [
            'array' => [[]],
            'float' => [3.14],
            'int' => [0],
            'null' => [null],
            'object' => [new \stdClass()],
            'string' => ['foo'],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesTrueCases
     */
    public function testFixWithDefaultConfiguration($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesTrueCases70
     * @requires PHP 7.0
     */
    public function testFixWithDefaultConfiguration70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesTrueCases80
     * @requires PHP 8.0
     */
    public function testFixWithDefaultConfiguration80($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesTrueCases
     */
    public function testFixWithWithBracesTrue($expected, $input = null)
    {
        $this->fixer->configure([
            'with_braces' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithWithBracesTrueCases()
    {
        $tests = [
            [
                '<?php class A { public function B(){ $static = new static(new \SplFileInfo(__FILE__)); }}',
            ],
            [
                '<?php $static = new self(new \SplFileInfo(__FILE__));',
            ],
            [
                '<?php $x = new X/**/ /**/ /**//**//**/ /**//**/   (/**/ /**/ /**//**//**/ /**//**/)/**/ /**/ /**//**//**/ /**//**/;/**/ /**/ /**//**//**/ /**//**/',
            ],
            [
                '<?php $x = new X();',
                '<?php $x = new X;',
            ],
            [
                '<?php $y = new Y() ;',
                '<?php $y = new Y ;',
            ],
            [
                '<?php $x = new Z() /**/;//',
                '<?php $x = new Z /**/;//',
            ],
            [
                '<?php $foo = new $foo();',
                '<?php $foo = new $foo;',
            ],
            [
                '<?php
                    $bar1 = new $foo[0]->bar();
                    $bar2 = new $foo[0][1]->bar();
                ',
            ],
            [
                '<?php $xyz = new X(new Y(new Z()));',
                '<?php $xyz = new X(new Y(new Z));',
            ],
            [
                '<?php $foo = (new $bar())->foo;',
                '<?php $foo = (new $bar)->foo;',
            ],
            [
                '<?php $foo = (new $bar((new Foo())->bar))->foo;',
                '<?php $foo = (new $bar((new Foo)->bar))->foo;',
            ],
            [
                '<?php $self = new self();',
                '<?php $self = new self;',
            ],
            [
                '<?php $static = new static();',
                '<?php $static = new static;',
            ],
            [
                '<?php $a = array( "key" => new DateTime(), );',
                '<?php $a = array( "key" => new DateTime, );',
            ],
            [
                '<?php $a = array( "key" => new DateTime() );',
                '<?php $a = array( "key" => new DateTime );',
            ],
            [
                '<?php $a = new $b[$c]();',
                '<?php $a = new $b[$c];',
            ],
            [
                '<?php $a = new $b[$c][0]();',
                '<?php $a = new $b[$c][0];',
            ],
            [
                '<?php $a = new $b[$c[$d ? foo() : bar("bar[...]") - 1]]();',
                '<?php $a = new $b[$c[$d ? foo() : bar("bar[...]") - 1]];',
            ],
            [
                '<?php $a = new $b[\'class\']();',
                '<?php $a = new $b[\'class\'];',
            ],
            [
                '<?php $a = new $b[\'class\'] ($foo[\'bar\']);',
            ],
            [
                '<?php $a = new $b[\'class\'] () ;',
            ],
            [
                '<?php $a = new $b[$c] ($hello[$world]) ;',
            ],
            [
                "<?php \$a = new \$b['class']()\r\n\t ;",
                "<?php \$a = new \$b['class']\r\n\t ;",
            ],
            [
                '<?php $a = $b ? new DateTime() : $b;',
                '<?php $a = $b ? new DateTime : $b;',
            ],
            [
                '<?php new self::$adapters[$name]["adapter"]();',
                '<?php new self::$adapters[$name]["adapter"];',
            ],
            [
                '<?php $a = new \Exception()?> <?php echo 1;',
                '<?php $a = new \Exception?> <?php echo 1;',
            ],
            [
                '<?php $b = new \StdClass() /**/?>',
                '<?php $b = new \StdClass /**/?>',
            ],
            [
                '<?php $a = new Foo() instanceof Foo;',
                '<?php $a = new Foo instanceof Foo;',
            ],
            [
                '<?php
                    $a = new Foo() + 1;
                    $a = new Foo() - 1;
                    $a = new Foo() * 1;
                    $a = new Foo() / 1;
                    $a = new Foo() % 1;
                ',
                '<?php
                    $a = new Foo + 1;
                    $a = new Foo - 1;
                    $a = new Foo * 1;
                    $a = new Foo / 1;
                    $a = new Foo % 1;
                ',
            ],
            [
                '<?php
                    $a = new Foo() & 1;
                    $a = new Foo() | 1;
                    $a = new Foo() ^ 1;
                    $a = new Foo() << 1;
                    $a = new Foo() >> 1;
                ',
                '<?php
                    $a = new Foo & 1;
                    $a = new Foo | 1;
                    $a = new Foo ^ 1;
                    $a = new Foo << 1;
                    $a = new Foo >> 1;
                ',
            ],
            [
                '<?php
                    $a = new Foo() and 1;
                    $a = new Foo() or 1;
                    $a = new Foo() xor 1;
                    $a = new Foo() && 1;
                    $a = new Foo() || 1;
                ',
                '<?php
                    $a = new Foo and 1;
                    $a = new Foo or 1;
                    $a = new Foo xor 1;
                    $a = new Foo && 1;
                    $a = new Foo || 1;
                ',
            ],
            [
                '<?php
                    if (new DateTime() > $this->startDate) {}
                    if (new DateTime() >= $this->startDate) {}
                    if (new DateTime() < $this->startDate) {}
                    if (new DateTime() <= $this->startDate) {}
                    if (new DateTime() == $this->startDate) {}
                    if (new DateTime() != $this->startDate) {}
                    if (new DateTime() <> $this->startDate) {}
                    if (new DateTime() === $this->startDate) {}
                    if (new DateTime() !== $this->startDate) {}
                ',
                '<?php
                    if (new DateTime > $this->startDate) {}
                    if (new DateTime >= $this->startDate) {}
                    if (new DateTime < $this->startDate) {}
                    if (new DateTime <= $this->startDate) {}
                    if (new DateTime == $this->startDate) {}
                    if (new DateTime != $this->startDate) {}
                    if (new DateTime <> $this->startDate) {}
                    if (new DateTime === $this->startDate) {}
                    if (new DateTime !== $this->startDate) {}
                ',
            ],
            [
                '<?php $a = new \stdClass() ? $b : $c;',
                '<?php $a = new \stdClass ? $b : $c;',
            ],
            [
                '<?php foreach (new Collection() as $x) {}',
                '<?php foreach (new Collection as $x) {}',
            ],
            [
                '<?php $a = [(string) new Foo() => 1];',
                '<?php $a = [(string) new Foo => 1];',
            ],
            [
                '<?php $a = [ "key" => new DateTime(), ];',
                '<?php $a = [ "key" => new DateTime, ];',
            ],
            [
                '<?php $a = [ "key" => new DateTime() ];',
                '<?php $a = [ "key" => new DateTime ];',
            ],
            [
                '<?php
                    $a = new Foo() ** 1;
                ',
                '<?php
                    $a = new Foo ** 1;
                ',
            ],
        ];

        foreach ($tests as $index => $test) {
            yield $index => $test;
        }

        if (\PHP_VERSION_ID < 80000) {
            yield [
                '<?php $a = new $b{$c}();',
                '<?php $a = new $b{$c};',
            ];

            yield [
                '<?php $a = new $b{$c}{0}{1}() ?>',
                '<?php $a = new $b{$c}{0}{1} ?>',
            ];

            yield [
                '<?php $a = new $b{$c}[1]{0}[2]();',
                '<?php $a = new $b{$c}[1]{0}[2];',
            ];
        }
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesTrueCases70
     * @requires PHP 7.0
     */
    public function testFixWithWithBracesTrue70($expected, $input = null)
    {
        $this->fixer->configure([
            'with_braces' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithWithBracesTrueCases70()
    {
        return [
            [
                '<?php
                    $a = new Foo() <=> 1;
                ',
                '<?php
                    $a = new Foo <=> 1;
                ',
            ],
            [
                '<?php
                    $a = new class() {use SomeTrait;};
                    $a = new class() implements Foo{};
                    $a = new class() /**/ extends Bar1{};
                    $a = new class()  extends Bar2 implements Foo{};
                    $a = new class()    extends Bar3 implements Foo, Foo2{};
                    $a = new class() {}?>
                ',
                '<?php
                    $a = new class {use SomeTrait;};
                    $a = new class implements Foo{};
                    $a = new class /**/ extends Bar1{};
                    $a = new class  extends Bar2 implements Foo{};
                    $a = new class    extends Bar3 implements Foo, Foo2{};
                    $a = new class {}?>
                ',
            ],
            [
                '<?php
                    class A {
                        public function B() {
                            $static = new static(new class(){});
                        }
                    }
                ',
                '<?php
                    class A {
                        public function B() {
                            $static = new static(new class{});
                        }
                    }
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesTrueCases80
     * @requires PHP 8.0
     */
    public function testFixWithWithBracesTrue80($expected, $input = null)
    {
        $this->fixer->configure([
            'with_braces' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithWithBracesTrueCases80()
    {
        yield [
            '<?php $a = new (foo());',
        ];

        yield [
            '<?php

class Bar {
    public function __construct(int $a = null) {
        echo $a;
    }
};

$foo = "B";

$a = new ($foo."ar");',
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesFalseCases
     */
    public function testFixWithWithBracesFalse($expected, $input = null)
    {
        $this->fixer->configure([
            'with_braces' => false,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithWithBracesFalseCases()
    {
        $tests = [
            [
                '<?php class A { public function B(){ $static = new static(new \SplFileInfo(__FILE__)); }}',
            ],
            [
                '<?php $static = new self(new \SplFileInfo(__FILE__));',
            ],
            [
                '<?php $x = new X/**/ /**/ /**//**//**/ /**//**/   (/**/ /**/ /**//**//**/ /**//**/)/**/ /**/ /**//**//**/ /**//**/;/**/ /**/ /**//**//**/ /**//**/',
            ],
            [
                '<?php $x = new X(/* foo */);',
            ],
            [
                '<?php $x = new X(// foo
);',
            ],
            [
                '<?php $x = new X;',
                '<?php $x = new X();',
            ],
            [
                '<?php $x = new X;',
                '<?php $x = new X(
);',
            ],
            [
                '<?php $y = new Y ;',
                '<?php $y = new Y() ;',
            ],
            [
                '<?php $y = new Y  ;',
                '<?php $y = new Y () ;',
            ],
            [
                '<?php $y = new Y  ;',
                '<?php $y = new Y (
) ;',
            ],
            [
                "<?php \$y = new Y\t  \r;",
                "<?php \$y = new Y\t () \r;",
            ],
            [
                "<?php \$y = new Y\t  \r;",
                "<?php \$y = new Y\t (
) \r;",
            ],
            [
                '<?php $y = new Y ;',
                '<?php $y = new Y(
) ;',
            ],
            [
                '<?php $x = new Z /**/;//',
                '<?php $x = new Z() /**/;//',
            ],
            [
                '<?php $foo = new $foo(/* foo */);',
            ],
            [
                '<?php $foo = new $foo(// foo
);',
            ],
            [
                '<?php $foo = new $foo;',
                '<?php $foo = new $foo();',
            ],
            [
                '<?php
                    $bar1 = new $foo[0]->bar();
                    $bar2 = new $foo[0][1]->bar();
                ',
            ],
            [
                '<?php $xyz = new X(new Y(new Z));',
                '<?php $xyz = new X(new Y(new Z()));',
            ],
            [
                '<?php $foo = (new $bar)->foo;',
                '<?php $foo = (new $bar())->foo;',
            ],
            [
                '<?php $foo = (new $bar((new Foo)->bar))->foo;',
                '<?php $foo = (new $bar((new Foo())->bar))->foo;',
            ],
            [
                '<?php $self = new self;',
                '<?php $self = new self();',
            ],
            [
                '<?php $static = new static;',
                '<?php $static = new static();',
            ],
            [
                '<?php $a = array( "key" => new DateTime, );',
                '<?php $a = array( "key" => new DateTime(), );',
            ],
            [
                '<?php $a = array( "key" => new DateTime );',
                '<?php $a = array( "key" => new DateTime() );',
            ],
            [
                '<?php $a = new $b[$c];',
                '<?php $a = new $b[$c]();',
            ],
            [
                '<?php $a = new $b[$c][0];',
                '<?php $a = new $b[$c][0]();',
            ],
            [
                '<?php $a = new $b[$c[$d ? foo() : bar("bar[...]") - 1]];',
                '<?php $a = new $b[$c[$d ? foo() : bar("bar[...]") - 1]]();',
            ],
            [
                '<?php $a = new $b[\'class\'];',
                '<?php $a = new $b[\'class\']();',
            ],
            [
                '<?php $a = new $b[\'class\'] ($foo[\'bar\']);',
            ],
            [
                '<?php $a = new $b[\'class\']  ;',
                '<?php $a = new $b[\'class\'] () ;',
            ],
            [
                '<?php $a = new $b[$c] ($hello[$world]) ;',
            ],
            [
                "<?php \$a = new \$b['class']\r\n\t ;",
                "<?php \$a = new \$b['class']()\r\n\t ;",
            ],
            [
                '<?php $a = $b ? new DateTime : $b;',
                '<?php $a = $b ? new DateTime() : $b;',
            ],
            [
                '<?php new self::$adapters[$name]["adapter"];',
                '<?php new self::$adapters[$name]["adapter"]();',
            ],
            [
                '<?php $a = new \Exception?> <?php echo 1;',
                '<?php $a = new \Exception()?> <?php echo 1;',
            ],
            [
                '<?php $b = new \StdClass /**/?>',
                '<?php $b = new \StdClass() /**/?>',
            ],
            [
                '<?php $a = new Foo instanceof Foo;',
                '<?php $a = new Foo() instanceof Foo;',
            ],
            [
                '<?php
                    $a = new Foo + 1;
                    $a = new Foo - 1;
                    $a = new Foo * 1;
                    $a = new Foo / 1;
                    $a = new Foo % 1;
                ',
                '<?php
                    $a = new Foo() + 1;
                    $a = new Foo() - 1;
                    $a = new Foo() * 1;
                    $a = new Foo() / 1;
                    $a = new Foo() % 1;
                ',
            ],
            [
                '<?php
                    $a = new Foo & 1;
                    $a = new Foo | 1;
                    $a = new Foo ^ 1;
                    $a = new Foo << 1;
                    $a = new Foo >> 1;
                ',
                '<?php
                    $a = new Foo() & 1;
                    $a = new Foo() | 1;
                    $a = new Foo() ^ 1;
                    $a = new Foo() << 1;
                    $a = new Foo() >> 1;
                ',
            ],
            [
                '<?php
                    $a = new Foo and 1;
                    $a = new Foo or 1;
                    $a = new Foo xor 1;
                    $a = new Foo && 1;
                    $a = new Foo || 1;
                ',
                '<?php
                    $a = new Foo() and 1;
                    $a = new Foo() or 1;
                    $a = new Foo() xor 1;
                    $a = new Foo() && 1;
                    $a = new Foo() || 1;
                ',
            ],
            [
                '<?php
                    if (new DateTime > $this->startDate) {}
                    if (new DateTime >= $this->startDate) {}
                    if (new DateTime < $this->startDate) {}
                    if (new DateTime <= $this->startDate) {}
                    if (new DateTime == $this->startDate) {}
                    if (new DateTime != $this->startDate) {}
                    if (new DateTime <> $this->startDate) {}
                    if (new DateTime === $this->startDate) {}
                    if (new DateTime !== $this->startDate) {}
                ',
                '<?php
                    if (new DateTime() > $this->startDate) {}
                    if (new DateTime() >= $this->startDate) {}
                    if (new DateTime() < $this->startDate) {}
                    if (new DateTime() <= $this->startDate) {}
                    if (new DateTime() == $this->startDate) {}
                    if (new DateTime() != $this->startDate) {}
                    if (new DateTime() <> $this->startDate) {}
                    if (new DateTime() === $this->startDate) {}
                    if (new DateTime() !== $this->startDate) {}
                ',
            ],
            [
                '<?php $a = new \stdClass ? $b : $c;',
                '<?php $a = new \stdClass() ? $b : $c;',
            ],
            [
                '<?php foreach (new Collection as $x) {}',
                '<?php foreach (new Collection() as $x) {}',
            ],
            [
                '<?php $a = [(string) new Foo => 1];',
                '<?php $a = [(string) new Foo() => 1];',
            ],
            [
                '<?php $a = [ "key" => new DateTime, ];',
                '<?php $a = [ "key" => new DateTime(), ];',
            ],
            [
                '<?php $a = [ "key" => new DateTime ];',
                '<?php $a = [ "key" => new DateTime() ];',
            ],
            [
                '<?php
                    $a = new Foo ** 1;
                ',
                '<?php
                    $a = new Foo() ** 1;
                ',
            ],
        ];

        foreach ($tests as $index => $test) {
            yield $index => $test;
        }

        if (\PHP_VERSION_ID < 80000) {
            yield [
                '<?php $a = new $b{$c};',
                '<?php $a = new $b{$c}();',
            ];

            yield [
                '<?php $a = new $b{$c}{0}{1} ?>',
                '<?php $a = new $b{$c}{0}{1}() ?>',
            ];

            yield [
                '<?php $a = new $b{$c}[1]{0}[2];',
                '<?php $a = new $b{$c}[1]{0}[2]();',
            ];
        }
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesFalseCases70
     * @requires PHP 7.0
     */
    public function testFixWithWithBracesFalse70($expected, $input = null)
    {
        $this->fixer->configure([
            'with_braces' => false,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithWithBracesFalseCases70()
    {
        return [
            [
                '<?php
                    $a = new Foo <=> 1;
                ',
                '<?php
                    $a = new Foo() <=> 1;
                ',
            ],
            [
                '<?php
                    $a = new class(/* foo */) {use SomeTrait;};
                    $a = new class(/* foo */) implements Foo{};
                    $a = new class(/* foo */) /**/ extends Bar1{};
                    $a = new class(/* foo */)  extends Bar2 implements Foo{};
                    $a = new class(/* foo */)    extends Bar3 implements Foo, Foo2{};
                    $a = new class(/* foo */) {}?>
                ',
            ],
            [
                '<?php
                    $a = new class(//foo
) {use SomeTrait;};
                    $a = new class(//foo
) implements Foo{};
                    $a = new class(//foo
) /**/ extends Bar1{};
                    $a = new class(//foo
)  extends Bar2 implements Foo{};
                    $a = new class(//foo
)    extends Bar3 implements Foo, Foo2{};
                    $a = new class(//foo
) {}?>
                ',
            ],
            [
                '<?php
                    $a = new class {use SomeTrait;};
                    $a = new class implements Foo{};
                    $a = new class /**/ extends Bar1{};
                    $a = new class  extends Bar2 implements Foo{};
                    $a = new class    extends Bar3 implements Foo, Foo2{};
                    $a = new class {}?>
                ',
                '<?php
                    $a = new class() {use SomeTrait;};
                    $a = new class() implements Foo{};
                    $a = new class() /**/ extends Bar1{};
                    $a = new class()  extends Bar2 implements Foo{};
                    $a = new class()    extends Bar3 implements Foo, Foo2{};
                    $a = new class() {}?>
                ',
            ],
            [
                '<?php
                    $a = new class {use SomeTrait;};
                    $a = new class implements Foo{};
                    $a = new class /**/ extends Bar1{};
                    $a = new class  extends Bar2 implements Foo{};
                    $a = new class    extends Bar3 implements Foo, Foo2{};
                    $a = new class {}?>
                ',
                '<?php
                    $a = new class(

) {use SomeTrait;};
                    $a = new class(

) implements Foo{};
                    $a = new class(

) /**/ extends Bar1{};
                    $a = new class(

)  extends Bar2 implements Foo{};
                    $a = new class(

)    extends Bar3 implements Foo, Foo2{};
                    $a = new class(

) {}?>
                ',
            ],
            [
                '<?php
                    class A {
                        public function B() {
                            $static = new static(new class{});
                        }
                    }
                ',
                '<?php
                    class A {
                        public function B() {
                            $static = new static(new class(){});
                        }
                    }
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithWithBracesFalseCases80
     * @requires PHP 8.0
     */
    public function testFixWithWithBracesFalse80($expected, $input = null)
    {
        $this->fixer->configure([
            'with_braces' => false,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithWithBracesFalseCases80()
    {
        yield [
            '<?php $a = new (foo(/* foo */));',
        ];

        yield [
            '<?php $a = new (foo(// foo
));',
        ];

        yield [
            '<?php $a = new (foo);',
            '<?php $a = new (foo());',
        ];
    }
}
