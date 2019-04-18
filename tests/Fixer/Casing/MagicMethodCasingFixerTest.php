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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Fixer\Casing\MagicMethodCasingFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\MagicMethodCasingFixer
 */
final class MagicMethodCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $fixerReflection = new \ReflectionClass(MagicMethodCasingFixer::class);
        $property = $fixerReflection->getProperty('magicNames');
        $property->setAccessible(true);
        $allMethodNames = $property->getValue();

        // '__callStatic'
        yield 'method declaration for "__callstatic".' => [
            '<?php class Foo {public static function __callStatic($a, $b){}}',
            '<?php class Foo {public static function __CALLStatic($a, $b){}}',
        ];

        yield 'static call to "__callstatic".' => [
            '<?php Foo::__callStatic() ?>',
            '<?php Foo::__callstatic() ?>',
        ];

        unset($allMethodNames['__callstatic']);

        // static version of '__set_state'
        yield 'method declaration for "__set_state".' => [
            '<?php class Foo {public static function __set_state($a, $b){}}',
            '<?php class Foo {public static function __set_STATE($a, $b){}}',
        ];

        yield 'static call to "__set_state".' => [
            '<?php Foo::__set_state() ?>',
            '<?php Foo::__set_STATE() ?>',
        ];

        // '__clone'
        yield 'method declaration for "__clone".' => [
            '<?php class Foo {public function __clone(){}}',
            '<?php class Foo {public function __CLONE(){}}',
        ];

        unset($allMethodNames['__clone']);

        // two arguments
        $methodNames = ['__call', '__set'];

        foreach ($methodNames as $i => $name) {
            unset($allMethodNames[$name]);

            yield sprintf('method declaration for "%s".', $name) => [
                sprintf('<?php class Foo {public function %s($a, $b){}}', $name),
                sprintf('<?php class Foo {public function %s($a, $b){}}', strtoupper($name)),
            ];
        }

        foreach ($methodNames as $i => $name) {
            yield sprintf('method call "%s".', $name) => [
                sprintf('<?php $a->%s($a, $b);', $name),
                sprintf('<?php $a->%s($a, $b);', strtoupper($name)),
            ];
        }

        // single argument
        $methodNames = ['__get', '__isset', '__unset'];

        foreach ($methodNames as $i => $name) {
            unset($allMethodNames[$name]);

            yield sprintf('method declaration for "%s".', $name) => [
                sprintf('<?php class Foo {public function %s($a){}}', $name),
                sprintf('<?php class Foo {public function %s($a){}}', strtoupper($name)),
            ];
        }

        foreach ($methodNames as $i => $name) {
            yield sprintf('method call "%s".', $name) => [
                sprintf('<?php $a->%s($a);', $name),
                sprintf('<?php $a->%s($a);', strtoupper($name)),
            ];
        }

        // no argument

        foreach ($allMethodNames as $i => $name) {
            yield sprintf('method declaration for "%s".', $name) => [
                sprintf('<?php class Foo {public function %s(){}}', $name),
                sprintf('<?php class Foo {public function %s(){}}', strtoupper($name)),
            ];
        }

        foreach ($allMethodNames as $i => $name) {
            yield sprintf('method call "%s".', $name) => [
                sprintf('<?php $a->%s();', $name),
                sprintf('<?php $a->%s();', strtoupper($name)),
            ];
        }

        yield 'method declaration in interface' => [
            '<?php interface Foo {public function __toString();}',
            '<?php interface Foo {public function __tostring();}',
        ];

        yield 'method declaration in interface' => [
            '<?php trait Foo {public function __toString(){}}',
            '<?php trait Foo {public function __tostring(){}}',
        ];

        yield '(un)serialize' => [
            '<?php

class Foo extends Bar
{
    public function __serialize() {
        $this->__serialize();
    }

    public function __unserialize($payload) {
        $this->__unserialize($this_>$a);
    }
}
',
            '<?php

class Foo extends Bar
{
    public function __SERIALIZE() {
        $this->__SERIALIZE();
    }

    public function __unSERIALIZE($payload) {
        $this->__unSERIALIZE($this_>$a);
    }
}
',
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideDoNotFixCases()
    {
        return [
            [
                '<?php
__Tostring();',
            ],
            [
                '<?php
function __Tostring() {}',
            ],
            [
                '<?php
                    #->__sleep()
                    /** ->__sleep() */
                    echo $a->__sleep;
                ',
            ],
            [
                '<?php
                    class B
                    {
                        public function _not_magic()
                        {
                        }
                    }
                ',
            ],
            [
                '<?php
                    function __alsoNotMagic()
                    {
                    }
                ',
            ],
            [
                '<?php
                    function __()
                    {
                    }
                ',
            ],
            [
                '<?php
                    function a()
                    {
                    }
                ',
            ],
            [
                '<?php
                    $a->__not_magic();
                ',
            ],
            [
                '<?php
                    $a->a();
                ',
            ],
        ];
    }

    /**
     * @requires PHP 7.0
     */
    public function testFixPHP7()
    {
        $this->doTest(
            '<?php
            function __TOSTRING(){} // do not fix

            trait FooTrait
            {
                public function __invoke($a){} // fix
            }

            function __GET($a){} // do not fix

            interface Foo
            {
                public function __sleep(); // fix
            }

            final class Foo
            {
                private function __construct($a, $b, $c, $d = null, $e = 1) // fix
                {
                }

                public function __isset($a) // fix
                {
                    return $b->__isset($b); // fix
                }

                private function bar()
                {
                    new class {
                        public function __unset($a) // fix
                        {
                            $b = null === $a
                                ? $b->__unset($a) // fix
                                : $a->__unset($a) // fix
                            ;

                            return $b;
                        }
                    };
                }
            }

            function __ISSET($bar){} // do not fix

            $a->__unset($foo); // fix
            ',
            '<?php
            function __TOSTRING(){} // do not fix

            trait FooTrait
            {
                public function __INVOKE($a){} // fix
            }

            function __GET($a){} // do not fix

            interface Foo
            {
                public function __SlEeP(); // fix
            }

            final class Foo
            {
                private function __consTRUCT($a, $b, $c, $d = null, $e = 1) // fix
                {
                }

                public function __ISSET($a) // fix
                {
                    return $b->__IsseT($b); // fix
                }

                private function bar()
                {
                    new class {
                        public function __UnSet($a) // fix
                        {
                            $b = null === $a
                                ? $b->__UnSet($a) // fix
                                : $a->__UnSet($a) // fix
                            ;

                            return $b;
                        }
                    };
                }
            }

            function __ISSET($bar){} // do not fix

            $a->__UnSet($foo); // fix
            '
        );
    }

    /**
     * @requires PHP 7.3
     */
    public function testFix73()
    {
        $this->doTest(
            '<?php $foo->__invoke(1, );',
            '<?php $foo->__INVOKE(1, );'
        );
    }
}
