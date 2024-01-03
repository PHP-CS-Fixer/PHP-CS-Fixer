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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\NewWithBracesFixer
 */
final class NewWithBracesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixNamedWithDefaultConfigurationCases
     */
    public function testFixNamedWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixNamedWithDefaultConfigurationCases(): iterable
    {
        yield ['<?php $x = new X(foo(/**/));'];

        yield ['<?php $xyz = new X(new Y(new Z(/**/ foo())));'];

        yield ['<?php $self = new self(a);'];

        yield [
            '<?php class A { public function B(){ $static = new static(new \SplFileInfo(__FILE__)); }}',
        ];

        yield [
            '<?php $static = new self(new \SplFileInfo(__FILE__));',
        ];

        yield [
            '<?php $x = new X/**/ /**/ /**//**//**/ /**//**/   (/**/ /**/ /**//**//**/ /**//**/)/**/ /**/ /**//**//**/ /**//**/;/**/ /**/ /**//**//**/ /**//**/',
        ];

        yield [
            '<?php $x = new X();',
            '<?php $x = new X;',
        ];

        yield [
            '<?php $y = new Y() ;',
            '<?php $y = new Y ;',
        ];

        yield [
            '<?php $x = new Z() /**/;//',
            '<?php $x = new Z /**/;//',
        ];

        yield [
            '<?php $foo = new $foo();',
            '<?php $foo = new $foo;',
        ];

        yield [
            '<?php
                    $bar1 = new $foo[0]->bar();
                    $bar2 = new $foo[0][1]->bar();
                ',
        ];

        yield [
            '<?php $xyz = new X(new Y(new Z()));',
            '<?php $xyz = new X(new Y(new Z));',
        ];

        yield [
            '<?php $foo = (new $bar())->foo;',
            '<?php $foo = (new $bar)->foo;',
        ];

        yield [
            '<?php $foo = (new $bar((new Foo())->bar))->foo;',
            '<?php $foo = (new $bar((new Foo)->bar))->foo;',
        ];

        yield [
            '<?php $self = new self();',
            '<?php $self = new self;',
        ];

        yield [
            '<?php $static = new static();',
            '<?php $static = new static;',
        ];

        yield [
            '<?php $a = array( "key" => new DateTime(), );',
            '<?php $a = array( "key" => new DateTime, );',
        ];

        yield [
            '<?php $a = array( "key" => new DateTime() );',
            '<?php $a = array( "key" => new DateTime );',
        ];

        yield [
            '<?php $a = new $b[$c]();',
            '<?php $a = new $b[$c];',
        ];

        yield [
            '<?php $a = new $b[$c][0]();',
            '<?php $a = new $b[$c][0];',
        ];

        yield [
            '<?php $a = new $b[$c[$d ? foo() : bar("bar[...]") - 1]]();',
            '<?php $a = new $b[$c[$d ? foo() : bar("bar[...]") - 1]];',
        ];

        yield [
            '<?php $a = new $b[\'class\']();',
            '<?php $a = new $b[\'class\'];',
        ];

        yield [
            '<?php $a = new $b[\'class\'] ($foo[\'bar\']);',
        ];

        yield [
            '<?php $a = new $b[\'class\'] () ;',
        ];

        yield [
            '<?php $a = new $b[$c] ($hello[$world]) ;',
        ];

        yield [
            "<?php \$a = new \$b['class']()\r\n\t ;",
            "<?php \$a = new \$b['class']\r\n\t ;",
        ];

        yield [
            '<?php $a = $b ? new DateTime() : $b;',
            '<?php $a = $b ? new DateTime : $b;',
        ];

        yield [
            '<?php new self::$adapters[$name]["adapter"]();',
            '<?php new self::$adapters[$name]["adapter"];',
        ];

        yield [
            '<?php $a = new \Exception()?> <?php echo 1;',
            '<?php $a = new \Exception?> <?php echo 1;',
        ];

        yield [
            '<?php $b = new \StdClass() /**/?>',
            '<?php $b = new \StdClass /**/?>',
        ];

        yield [
            '<?php $a = new Foo() instanceof Foo;',
            '<?php $a = new Foo instanceof Foo;',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php $a = new \stdClass() ? $b : $c;',
            '<?php $a = new \stdClass ? $b : $c;',
        ];

        yield [
            '<?php foreach (new Collection() as $x) {}',
            '<?php foreach (new Collection as $x) {}',
        ];

        yield [
            '<?php $a = [(string) new Foo() => 1];',
            '<?php $a = [(string) new Foo => 1];',
        ];

        yield [
            '<?php $a = [ "key" => new DateTime(), ];',
            '<?php $a = [ "key" => new DateTime, ];',
        ];

        yield [
            '<?php $a = [ "key" => new DateTime() ];',
            '<?php $a = [ "key" => new DateTime ];',
        ];

        yield [
            '<?php
                    $a = new Foo() ** 1;
                ',
            '<?php
                    $a = new Foo ** 1;
                ',
        ];

        yield [
            '<?php
                    $a = new Foo() <=> 1;
                ',
            '<?php
                    $a = new Foo <=> 1;
                ',
        ];

        yield [
            "<?php \$a = new \$b['class']/* */()\r\n\t ;",
        ];

        yield [
            "<?php \$a = new \$b['class'] /* */()\r\n\t ;",
        ];

        yield [
            "<?php \$a = new \$b['class']()/* */;",
            "<?php \$a = new \$b['class']/* */;",
        ];

        yield [
            "<?php \$a = new \$b['class']() /* */;",
            "<?php \$a = new \$b['class'] /* */;",
        ];
    }

    /**
     * @dataProvider provideFixNamedWithoutBracesCases
     */
    public function testFixNamedWithoutBraces(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['named_class' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixNamedWithoutBracesCases(): iterable
    {
        yield ['<?php $x = new X(foo(/**/));'];

        yield ['<?php $xyz = new X(new Y(new Z(/**/ foo())));'];

        yield ['<?php $self = new self(a);'];

        yield [
            '<?php $bar1 = new $foo->bar["baz"];',
            '<?php $bar1 = new $foo->bar["baz"]();',
        ];

        yield [
            '<?php class A { public function B(){ $static = new static(new \SplFileInfo(__FILE__)); }}',
        ];

        yield [
            '<?php $static = new self(new \SplFileInfo(__FILE__));',
        ];

        yield [
            '<?php $x = new X/**/ /**/ /**//**//**/ /**//**/   /**/ /**/ /**//**//**/ /**//**//**/ /**/ /**//**//**/ /**//**/;/**/ /**/ /**//**//**/ /**//**/',
            '<?php $x = new X/**/ /**/ /**//**//**/ /**//**/   (/**/ /**/ /**//**//**/ /**//**/)/**/ /**/ /**//**//**/ /**//**/;/**/ /**/ /**//**//**/ /**//**/',
        ];

        yield [
            '<?php $x = new X;',
            '<?php $x = new X();',
        ];

        yield [
            '<?php $y = new Y ;',
            '<?php $y = new Y() ;',
        ];

        yield [
            '<?php $x = new Z /**/;//',
            '<?php $x = new Z() /**/;//',
        ];

        yield [
            '<?php $foo = new $foo;',
            '<?php $foo = new $foo();',
        ];

        yield [
            '<?php $xyz = new X(new Y(new Z));',
            '<?php $xyz = new X(new Y(new Z()));',
        ];

        yield [
            '<?php $foo = (new $bar)->foo;',
            '<?php $foo = (new $bar())->foo;',
        ];

        yield [
            '<?php $foo = (new $bar((new Foo)->bar))->foo;',
            '<?php $foo = (new $bar((new Foo())->bar))->foo;',
        ];

        yield [
            '<?php $self = new self;',
            '<?php $self = new self();',
        ];

        yield [
            '<?php $static = new static;',
            '<?php $static = new static();',
        ];

        yield [
            '<?php $a = array( "key" => new DateTime, );',
            '<?php $a = array( "key" => new DateTime(), );',
        ];

        yield [
            '<?php $a = array( "key" => new DateTime );',
            '<?php $a = array( "key" => new DateTime() );',
        ];

        yield [
            '<?php $a = new $b[$c];',
            '<?php $a = new $b[$c]();',
        ];

        yield [
            '<?php $a = new $b[$c][0];',
            '<?php $a = new $b[$c][0]();',
        ];

        yield [
            '<?php $a = new $b[$c[$d ? foo() : bar("bar[...]") - 1]];',
            '<?php $a = new $b[$c[$d ? foo() : bar("bar[...]") - 1]]();',
        ];

        yield [
            '<?php $a = new $b[\'class\'];',
            '<?php $a = new $b[\'class\']();',
        ];

        yield [
            '<?php $a = new $b[\'class\'] ($foo[\'bar\']);',
        ];

        yield [
            '<?php $a = new $b[\'class\']  ;',
            '<?php $a = new $b[\'class\'] () ;',
        ];

        yield [
            '<?php $a = new $b[$c] ($hello[$world]) ;',
        ];

        yield [
            "<?php \$a = new \$b['class']\r\n\t ;",
            "<?php \$a = new \$b['class']()\r\n\t ;",
        ];

        yield [
            '<?php $a = $b ? new DateTime : $b;',
            '<?php $a = $b ? new DateTime() : $b;',
        ];

        yield [
            '<?php new self::$adapters[$name]["adapter"];',
            '<?php new self::$adapters[$name]["adapter"]();',
        ];

        yield [
            '<?php $a = new \Exception?> <?php echo 1;',
            '<?php $a = new \Exception()?> <?php echo 1;',
        ];

        yield [
            '<?php $b = new \StdClass /**/?>',
            '<?php $b = new \StdClass() /**/?>',
        ];

        yield [
            '<?php $a = new Foo instanceof Foo;',
            '<?php $a = new Foo() instanceof Foo;',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php $a = new \stdClass ? $b : $c;',
            '<?php $a = new \stdClass() ? $b : $c;',
        ];

        yield [
            '<?php foreach (new Collection as $x) {}',
            '<?php foreach (new Collection() as $x) {}',
        ];

        yield [
            '<?php $a = [(string) new Foo => 1];',
            '<?php $a = [(string) new Foo() => 1];',
        ];

        yield [
            '<?php $a = [ "key" => new DateTime, ];',
            '<?php $a = [ "key" => new DateTime(), ];',
        ];

        yield [
            '<?php $a = [ "key" => new DateTime ];',
            '<?php $a = [ "key" => new DateTime() ];',
        ];

        yield [
            '<?php
                    $a = new Foo ** 1;
                ',
            '<?php
                    $a = new Foo() ** 1;
                ',
        ];

        yield [
            '<?php
                    $a = new Foo <=> 1;
                ',
            '<?php
                    $a = new Foo() <=> 1;
                ',
        ];

        yield [
            "<?php \$a = new \$b['class']/* */\r\n\t ;",
            "<?php \$a = new \$b['class']/* */()\r\n\t ;",
        ];

        yield [
            "<?php \$a = new \$b['class'] /* */\r\n\t ;",
            "<?php \$a = new \$b['class'] /* */()\r\n\t ;",
        ];

        yield [
            "<?php \$a = new \$b['class']/* */;",
            "<?php \$a = new \$b['class']()/* */;",
        ];

        yield [
            "<?php \$a = new \$b['class'] /* */;",
            "<?php \$a = new \$b['class']() /* */;",
        ];
    }

    /**
     * @dataProvider provideFixAnonymousWithDefaultConfigurationCases
     */
    public function testFixAnonymousWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixAnonymousWithDefaultConfigurationCases(): iterable
    {
        yield ['<?php $a = new class($a) {use SomeTrait;};'];

        yield ['<?php $a = new class(foo(/**/)) implements Foo{};'];

        yield ['<?php $a = new class($c["d"]) /**/ extends Bar1{};'];

        yield ['<?php $a = new class($e->f  )  extends Bar2 implements Foo{};'];

        yield ['<?php $a = new class( /**/ $g )    extends Bar3 implements Foo, Foo2{};'];

        yield ['<?php $a = new class( $h  /**/) {}?>'];

        yield [
            '<?php
                    $a = new Foo() <=> 1;
                ',
            '<?php
                    $a = new Foo <=> 1;
                ',
        ];

        yield [
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
        ];

        yield [
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
        ];
    }

    /**
     * @dataProvider provideFixAnonymousWithoutBracesCases
     */
    public function testFixAnonymousWithoutBraces(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['anonymous_class' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixAnonymousWithoutBracesCases(): iterable
    {
        yield ['<?php $a = new class($a) {use SomeTrait;};'];

        yield ['<?php $a = new class(foo(/**/)) implements Foo{};'];

        yield ['<?php $a = new class($c["d"]) /**/ extends Bar1{};'];

        yield ['<?php $a = new class($e->f  )  extends Bar2 implements Foo{};'];

        yield ['<?php $a = new class( /**/ $g )    extends Bar3 implements Foo, Foo2{};'];

        yield ['<?php $a = new class( $h  /**/) {}?>'];

        yield [
            '<?php
                    $a = new class {use SomeTrait;};
                    $a = new class implements Foo{};
                    $a = new class /**/ extends Bar1{};
                    $a = new class  extends Bar2 implements Foo{};
                    $a = new class    extends Bar3 implements Foo, Foo2{};
                    $a = new class    {}?>
                ',
            '<?php
                    $a = new class() {use SomeTrait;};
                    $a = new class() implements Foo{};
                    $a = new class() /**/ extends Bar1{};
                    $a = new class()  extends Bar2 implements Foo{};
                    $a = new class()    extends Bar3 implements Foo, Foo2{};
                    $a = new class ( )  {}?>
                ',
        ];

        yield [
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
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPre80Cases(): iterable
    {
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

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
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

        yield [
            '<?php
                $bar1 = new $foo[0]?->bar();
                $bar2 = new $foo[0][1]?->bar();
            ',
        ];

        yield [
            '<?php $a = new
                #[Internal]
                class(){};
            ',
            '<?php $a = new
                #[Internal]
                class{};
            ',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php
function test(
    $foo = new A(),
    $baz = new C(x: 2),
) {
}

class Test {
    public function __construct(
        public $prop = new Foo(),
    ) {}
}

static $x = new Foo();

const C = new Foo();

function test2($param = new Foo()) {}
',
            '<?php
function test(
    $foo = new A,
    $baz = new C(x: 2),
) {
}

class Test {
    public function __construct(
        public $prop = new Foo,
    ) {}
}

static $x = new Foo;

const C = new Foo;

function test2($param = new Foo) {}
',
        ];
    }
}
