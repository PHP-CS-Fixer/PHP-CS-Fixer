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
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer
 */
final class NoNullPropertyInitializationFixerTest extends AbstractFixerTestCase
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
        return [
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public $bar = null; }',
            ],
            [
                '<?php class Foo { protected $bar; }',
                '<?php class Foo { protected $bar = null; }',
            ],
            [
                '<?php class Foo { private $bar; }',
                '<?php class Foo { private $bar = null; }',
            ],
            [
                '<?php class Foo { var $bar; }',
                '<?php class Foo { var $bar = null; }',
            ],
            [
                '<?php class Foo { VAR $bar; }',
                '<?php class Foo { VAR $bar = null; }',
            ],
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public $bar = NULL; }',
            ],
            [
                '<?php class Foo { PUblic $bar; }',
                '<?php class Foo { PUblic $bar = nuLL; }',
            ],
            [
                '<?php trait Foo { public $bar; }',
                '<?php trait Foo { public $bar = nuLL; }',
            ],
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public $bar = \null; }',
            ],
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public $bar = \     null; }',
            ],
            [
                '<?php class Foo { public $bar/* oh hai! */; }',
                '<?php class Foo { public $bar = \/* oh hai! */null; }',
            ],
            [
                '<?php class Foo {/* */public/* A */$bar/* B *//** C */;/* D */}',
                '<?php class Foo {/* */public/* A */$bar/* B */=/** C */null;/* D */}',
            ],
            [
                '<?php class Foo { public $bar; protected $baz; }',
                '<?php class Foo { public $bar = null; protected $baz = null; }',
            ],
            [
                '<?php class Foo { public $bar = \'null\'; }',
            ],
            [
                '<?php class Foo { public function bar() { return null; } }',
            ],
            [
                '<?php class Foo { protected $bar, $baz, $qux; }',
                '<?php class Foo { protected $bar = null, $baz = null, $qux = null; }',
            ],
            [
                '<?php class Foo { protected $bar, $baz = \'baz\', $qux; }',
                '<?php class Foo { protected $bar, $baz = \'baz\', $qux = null; }',
            ],
            [
                '<?php trait Foo { public $bar; } abstract class Bar { protected $bar, $baz = \'baz\', $qux; }',
                '<?php trait Foo { public $bar = null; } abstract class Bar { protected $bar, $baz = \'baz\', $qux = null; }',
            ],
            [
                '<?php class Foo { public function foo() { return null; } public $bar; public function baz() { return null; } }',
                '<?php class Foo { public function foo() { return null; } public $bar = null; public function baz() { return null; } }',
            ],
            [
                '<?php class#1
Foo#2
{#3
protected#4
$bar#5
#6
,#7
$baz#8
#9
,#10
$qux#11
#12
;#13
}
',
                '<?php class#1
Foo#2
{#3
protected#4
$bar#5
=#6
null,#7
$baz#8
=#9
null,#10
$qux#11
=#12
null;#13
}
',
            ],
            [
                '<?php class Foo { const FOO = null; }',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.0
     * @dataProvider providePhp70Cases
     */
    public function testFixPhp70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function providePhp70Cases()
    {
        return [
            [
                '<?php new class () { public $bar; };',
                '<?php new class () { public $bar = null; };',
            ],
            [
                '<?php class Foo { public function foo() { return new class() { private $bar; }; } }',
                '<?php class Foo { public function foo() { return new class() { private $bar = null; }; } }',
            ],
            [
                '<?php class Foo { public function foo() { return new class() { private $bar; }; } } trait Baz { public $baz; }',
                '<?php class Foo { public function foo() { return new class() { private $bar = null; }; } } trait Baz { public $baz = null; }',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.1
     * @dataProvider providePhp71Cases
     */
    public function testFixPhp71($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function providePhp71Cases()
    {
        return [
            [
                '<?php class Foo { public const FOO = null; }',
            ],
        ];
    }
}
