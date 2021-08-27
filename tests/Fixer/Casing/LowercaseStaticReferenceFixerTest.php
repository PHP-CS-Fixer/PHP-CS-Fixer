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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @covers \PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer
 *
 * @internal
 */
final class LowercaseStaticReferenceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php class Foo extends Bar { public function baz() { self::qux(); } }',
                '<?php class Foo extends Bar { public function baz() { SELF::qux(); } }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() { static::qux(); } }',
                '<?php class Foo extends Bar { public function baz() { STATIC::qux(); } }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() { parent::baz(); } }',
                '<?php class Foo extends Bar { public function baz() { PARENT::baz(); } }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() { parent::baz(); } }',
                '<?php class Foo extends Bar { public function baz() { Parent::baz(); } }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() { return new self(); } }',
                '<?php class Foo extends Bar { public function baz() { return new Self(); } }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() { return SelfFoo::FOO; } }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() { return FooSelf::FOO; } }',
            ],
            [
                '<?php class Foo extends Bar { private STATIC $baz; }',
            ],
            [
                '<?php class Foo extends Bar { STATIC private $baz; }',
            ],
            [
                '<?php class Foo extends Bar { public function paRent() {} }',
            ],
            [
                '<?php $foo->Self();',
            ],
            [
                '<?php Foo::Self();',
            ],
            [
                '<?php if ($foo instanceof self) { return true; }',
                '<?php if ($foo instanceof Self) { return true; }',
            ],
            [
                '<?php if ($foo instanceof static) { return true; }',
                '<?php if ($foo instanceof Static) { return true; }',
            ],
            [
                '<?php if ($foo instanceof parent) { return true; }',
                '<?php if ($foo instanceof Parent) { return true; }',
            ],
            [
                '<?php if ($foo instanceof Self\Bar) { return true; }',
            ],
            [
                '<?php if ($foo instanceof MySelf) { return true; }',
            ],
            [
                '<?php class Foo extends Bar { public function baz(self $x) {} }',
                '<?php class Foo extends Bar { public function baz(Self $x) {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz(parent $x) {} }',
                '<?php class Foo extends Bar { public function baz(Parent $x) {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz(MySelf $x) {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz(Self\Qux $x) {} }',
            ],
            [
                '<?php $a = STATIC function() {};',
            ],
            [
                '<?php class A { public function B() { STATIC $a; echo $a; }}',
            ],
            [
                '<?php class A { public function B() { $collection = $static ? new static($b) : new self(); } }',
                '<?php class A { public function B() { $collection = $static ? new STATIC($b) : new self(); } }',
            ],
            [
                '<?php class A { STATIC public function B() {} }',
            ],
            [
                '<?php
                    $a = function () {
                        STATIC $B = false;
                        if ($B) {
                            echo 1;
                        }
                        $B = true;
                    };
                ',
            ],
            [
                '<?php class A { const PARENT = 42; }',
            ],
            [
                '<?php namespace Foo\Parent;',
            ],
            [
                '<?php namespace Parent\Foo;',
            ],
            [
                '<?php class Foo extends Bar { public function baz() : self {} }',
                '<?php class Foo extends Bar { public function baz() : Self {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() : parent {} }',
                '<?php class Foo extends Bar { public function baz() : Parent {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() : MySelf {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() : Self\Qux {} }',
            ],
        ];
    }

    /**
     * @requires PHP 7.1
     * @dataProvider provideFix71Cases
     */
    public function testFix71(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix71Cases()
    {
        return [
            [
                '<?php class Foo extends Bar { public function baz(?self $x) {} }',
                '<?php class Foo extends Bar { public function baz(?Self $x) {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz(?parent $x) {} }',
                '<?php class Foo extends Bar { public function baz(?Parent $x) {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() : ?self {} }',
                '<?php class Foo extends Bar { public function baz() : ?Self {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() : ?parent {} }',
                '<?php class Foo extends Bar { public function baz() : ?Parent {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() : ?MySelf {} }',
            ],
            [
                '<?php class Foo extends Bar { public function baz() : ?Self\Qux {} }',
            ],
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testFix80(): void
    {
        $this->doTest('<?php $foo?->Self();');
    }
}
