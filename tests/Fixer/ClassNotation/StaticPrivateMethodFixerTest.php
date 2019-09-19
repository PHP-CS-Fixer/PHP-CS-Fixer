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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\StaticPrivateMethodFixer
 */
final class StaticPrivateMethodFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'main-use-case' => [
                '<?php
class Foo
{
    public $baz;

    public function bar()
    {
        $var = $this->baz;
        $var = self::baz();
    }

    private static function baz()
    {
        return 1;
    }
}
',
                '<?php
class Foo
{
    public $baz;

    public function bar()
    {
        $var = $this->baz;
        $var = $this->baz();
    }

    private function baz()
    {
        return 1;
    }
}
',
            ],
            'handle-multiple-classes' => [
                '<?php
class Foo
{
    private static function baz() { return 1; }

    public function xyz()
    {
        return new class() extends Wut {
            public function anonym_xyz()
            {
                return $this->baz();
            }
        };
    }
}
class Bar
{
    public function baz() { return 1; }
}
',
                '<?php
class Foo
{
    private function baz() { return 1; }

    public function xyz()
    {
        return new class() extends Wut {
            public function anonym_xyz()
            {
                return $this->baz();
            }
        };
    }
}
class Bar
{
    public function baz() { return 1; }
}
',
            ],
            'inverse-order-keywords-already-ok' => [
                '<?php
class Foo
{
    static private function inverseOrder() { return 1; }
}
',
            ],
            'skip-methods-containing-closures' => [
                '<?php
class Foo
{
    private function bar()
    {
        return function() {};
    }

    private function baz()
    {
        return static function() {};
    }
}
',
            ],
            'skip-instance-references' => [
                '<?php
class Foo
{
    private function bar()
    {
        return $this;
    }
}
',
            ],
            'skip-debug_backtrace' => [
                '<?php
class Foo
{
    private function bar()
    {
        return debug_backtrace()[1][\'object\'];
    }
}
',
            ],
            'fix-references-inside-non-static-closures' => [
                '<?php
class Foo
{
    public $baz;

    public function bar()
    {
        $var = function() {
            $var = $this->baz;
            $var = self::baz();
            $var = new class() {
                public function foo()
                {
                    return $this->baz();
                }
            };
        };
        // Non valid in runtime, but valid syntax
        $var = static function() {
            $var = $this->baz();
        };
    }

    private static function baz()
    {
        return 1;
    }
}
',
                '<?php
class Foo
{
    public $baz;

    public function bar()
    {
        $var = function() {
            $var = $this->baz;
            $var = $this->baz();
            $var = new class() {
                public function foo()
                {
                    return $this->baz();
                }
            };
        };
        // Non valid in runtime, but valid syntax
        $var = static function() {
            $var = $this->baz();
        };
    }

    private function baz()
    {
        return 1;
    }
}
',
            ],
        ];
    }
}
