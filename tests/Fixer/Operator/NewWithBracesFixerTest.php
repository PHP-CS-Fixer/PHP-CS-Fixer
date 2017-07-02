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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provide70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return [
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
    }

    public function provide70Cases()
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
        ];
    }
}
