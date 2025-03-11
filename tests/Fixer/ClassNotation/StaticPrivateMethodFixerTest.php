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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\StaticPrivateMethodFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\StaticPrivateMethodFixer>
 */
final class StaticPrivateMethodFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'main-use-case' => [
            '<?php
class Foo
{
    public $baz;

    public function bar()
    {
        $var = $this->baz;
        $var = self::baz();
        if (true) {
            $var = self::baz();
        }
    }

    private static function baz()
    {
        if (true) {
            return 1;
        }
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
        if (true) {
            $var = $this->baz();
        }
    }

    private function baz()
    {
        if (true) {
            return 1;
        }
    }
}
',
        ];

        yield 'handle-multiple-classes' => [
            '<?php
abstract class Foo
{
    private static function baz() { return 1; }

    public function xyz()
    {
        return 1;
    }
}
abstract class Bar
{
    public function baz() { return 1; }

    abstract protected function xyz1();
    protected abstract function xyz2();
    abstract function xyz3();
}
',
            '<?php
abstract class Foo
{
    private function baz() { return 1; }

    public function xyz()
    {
        return 1;
    }
}
abstract class Bar
{
    public function baz() { return 1; }

    abstract protected function xyz1();
    protected abstract function xyz2();
    abstract function xyz3();
}
',
        ];

        yield 'inverse-order-keywords-already-ok' => [
            '<?php
class Foo
{
    static private function inverseOrder() { return 1; }
}
',
        ];

        yield 'skip-methods-containing-closures' => [
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
        ];

        yield 'skip-instance-references' => [
            '<?php
class Foo
{
    private function bar()
    {
        if (true) {
            return $this;
        }
    }
}
',
        ];

        yield 'skip-debug_backtrace' => [
            '<?php
class Foo
{
    private function bar()
    {
        return debug_backtrace()[1][\'object\'];
    }
}
',
        ];

        yield 'fix-references-inside-non-static-closures' => [
            '<?php
class Foo
{
    public $baz;

    public function bar()
    {
        $var = function() {
            $var = $this->baz;
            $var = self::baz();
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
        ];

        yield 'skip-magic-methods' => [
            '<?php
class Foo
{
    private function __clone() {}
    private function __construct() {}
    private function __destruct() {}
//    private function __serialize() {}
//    private function __set_state() {}
//    private function __sleep() {}
//    private function __unserialize() {}
    private function __wakeup() {}
}
',
        ];

        yield 'bug-multiple-methods' => [
            self::generate50Samples(true),
            self::generate50Samples(false),
        ];

        yield 'fix-self' => [
            '<?php
class Foo
{
    private static function baz()
    {
        return self::baz();
    }
}
',
            '<?php
class Foo
{
    private function baz()
    {
        return $this->baz();
    }
}
',
        ];

        yield 'bug-trait' => [
            '<?php

class Foo
{
    use A, B, C {
        asd as lol;
    }

    private static function bar() {}
}
',
            '<?php

class Foo
{
    use A, B, C {
        asd as lol;
    }

    private function bar() {}
}
',
        ];

        yield 'fix-final-as-well' => [
            '<?php
class Foo
{
    final private static function baz1() { return 1; }
    private final static function baz2() { return 1; }
}
',
            '<?php
class Foo
{
    final private function baz1() { return 1; }
    private final function baz2() { return 1; }
}
',
        ];

        yield 'main-use-case-with-anonymous-class' => [
            '<?php
class Foo
{
    public $baz;

    public function bar()
    {
        $var = $this->baz;
        $var = self::baz();
        if (true) {
            $var = self::baz();
        }
    }

    private static function baz()
    {
        if (true) {
            return new class() {
                public function baz()
                {
                    return $this;
                }
            };
        }
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
        if (true) {
            $var = $this->baz();
        }
    }

    private function baz()
    {
        if (true) {
            return new class() {
                public function baz()
                {
                    return $this;
                }
            };
        }
    }
}
',
        ];

        yield 'handle-multiple-classes-with-anonymous-class' => [
            '<?php
abstract class Foo
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
abstract class Bar
{
    public function baz() { return 1; }

    abstract protected function xyz1();
    protected abstract function xyz2();
    abstract function xyz3();
}
',
            '<?php
abstract class Foo
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
abstract class Bar
{
    public function baz() { return 1; }

    abstract protected function xyz1();
    protected abstract function xyz2();
    abstract function xyz3();
}
',
        ];

        yield 'fix-references-inside-non-static-closures-with-anonymous-class' => [
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
        ];
    }

    private static function generate50Samples(bool $fixed): string
    {
        $template = '<?php
class Foo
{
    public function userMethodStart()
    {
%s
    }
%s
}
';
        $usage = '';
        $signature = '';
        for ($inc = 0; $inc < 50; ++$inc) {
            $usage .= \sprintf('$var = %sbar%02s();%s', $fixed ? 'self::' : '$this->', $inc, PHP_EOL);
            $signature .= \sprintf('private %sfunction bar%02s() {}%s', $fixed ? 'static ' : '', $inc, PHP_EOL);
        }

        return \sprintf($template, $usage, $signature);
    }
}
