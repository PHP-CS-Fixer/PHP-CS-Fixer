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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer
 */
final class SelfAccessorFixerTest extends AbstractFixerTestCase
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

    public function provideCases()
    {
        return array(
            array(
                '<?php class Foo { const BAR = self::BAZ; }',
                '<?php class Foo { const BAR = Foo::BAZ; }',
            ),
            array(
                '<?php class Foo { private $bar = self::BAZ; }',
                '<?php class Foo { private $bar = fOO::BAZ; }', // case insensitive
            ),
            array(
                '<?php class Foo { function bar($a = self::BAR) {} }',
                '<?php class Foo { function bar($a = Foo::BAR) {} }',
            ),
            array(
                '<?php class Foo { function bar() { self::baz(); } }',
                '<?php class Foo { function bar() { Foo::baz(); } }',
            ),
            array(
                '<?php class Foo { function bar() { self::class; } }',
                '<?php class Foo { function bar() { Foo::class; } }',
            ),
            array(
                '<?php class Foo { function bar() { $x instanceof self; } }',
                '<?php class Foo { function bar() { $x instanceof Foo; } }',
            ),
            array(
                '<?php class Foo { function bar() { new self(); } }',
                '<?php class Foo { function bar() { new Foo(); } }',
            ),
            array(
                '<?php interface Foo { const BAR = self::BAZ; function bar($a = self::BAR); }',
                '<?php interface Foo { const BAR = Foo::BAZ; function bar($a = Foo::BAR); }',
            ),

            array(
                '<?php class Foo { const Foo = 1; }',
            ),
            array(
                '<?php class Foo { function foo() { } }',
            ),
            array(
                '<?php class Foo { function bar() { new \Baz\Foo(); } }',
            ),
            array(
                '<?php class Foo { function bar() { new Foo\Baz(); } }',
            ),
            array(
                // PHP < 5.4 compatibility: "self" is not available in closures
                '<?php class Foo { function bar() { function ($a = Foo::BAZ) { new Foo(); }; } }',
            ),
        );
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

    public function provide70Cases()
    {
        return array(
            array(
                '<?php class Foo { function bar() {
                    new class() { function baz() { new Foo(); } };
                } }',
            ),
        );
    }

    /**
     * @requires PHP 5.4
     */
    public function testFix54()
    {
        $expected = '<?php trait Foo { function bar() { self::bar(); } }';
        $input = '<?php trait Foo { function bar() { Foo::bar(); } }';

        $this->doTest($expected, $input);
    }
}
