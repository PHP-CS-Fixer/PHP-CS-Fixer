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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\PhpdocToPropertyTypeFixer
 */
final class PhpdocToPropertyTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            'no phpdoc return' => [
                '<?php class Foo { private $foo; }',
            ],
            'invalid return' => [
                '<?php class Foo { /** @var */ private $foo; }',
            ],
            'invalid class 1' => [
                '<?php class Foo { /** @var \9 */ private $foo; }',
            ],
            'invalid class 2' => [
                '<?php class Foo { /** @var \\Foo\\\\Bar */ private $foo; }',
            ],
            'multiple returns' => [
                '<?php
                    class Foo {
                        /**
                         * @var Bar
                         * @var Baz
                         */
                        private $foo;
                    }
                ',
            ],
            'non-root class' => [
                '<?php class Foo { /** @var Bar */ private Bar $foo; }',
                '<?php class Foo { /** @var Bar */ private $foo; }',
            ],
            'non-root namespaced class' => [
                '<?php class Foo { /** @var My\Bar */ private My\Bar $foo; }',
                '<?php class Foo { /** @var My\Bar */ private $foo; }',
            ],
            'root class' => [
                '<?php class Foo { /** @var \My\Bar */ private \My\Bar $foo; }',
                '<?php class Foo { /** @var \My\Bar */ private $foo; }',
            ],
            'void' => [
                '<?php class Foo { /** @var void */ private $foo; }',
            ],
            'never' => [
                '<?php class Foo { /** @var never */ private $foo; }',
            ],
            'iterable' => [
                '<?php class Foo { /** @var iterable */ private iterable $foo; }',
                '<?php class Foo { /** @var iterable */ private $foo; }',
            ],
            'object' => [
                '<?php class Foo { /** @var object */ private object $foo; }',
                '<?php class Foo { /** @var object */ private $foo; }',
            ],
            'fix scalar types by default, int' => [
                '<?php class Foo { /** @var int */ private int $foo; }',
                '<?php class Foo { /** @var int */ private $foo; }',
            ],
            'fix scalar types by default, float' => [
                '<?php class Foo { /** @var float */ private float $foo; }',
                '<?php class Foo { /** @var float */ private $foo; }',
            ],
            'fix scalar types by default, string' => [
                '<?php class Foo { /** @var string */ private string $foo; }',
                '<?php class Foo { /** @var string */ private $foo; }',
            ],
            'fix scalar types by default, bool' => [
                '<?php class Foo { /** @var bool */ private bool $foo; }',
                '<?php class Foo { /** @var bool */ private $foo; }',
            ],
            'fix scalar types by default, false' => [
                '<?php class Foo { /** @var false */ private bool $foo; }',
                '<?php class Foo { /** @var false */ private $foo; }',
            ],
            'fix scalar types by default, true' => [
                '<?php class Foo { /** @var true */ private bool $foo; }',
                '<?php class Foo { /** @var true */ private $foo; }',
            ],
            'do not fix scalar types when configured as such' => [
                '<?php class Foo { /** @var int */ private $foo; }',
                null,
                ['scalar_types' => false],
            ],
            'array native type' => [
                '<?php class Foo { /** @var array */ private array $foo; }',
                '<?php class Foo { /** @var array */ private $foo; }',
            ],
            'callable type' => [
                '<?php class Foo { /** @var callable */ private $foo; }',
            ],
            'self accessor' => [
                '<?php class Foo { /** @var self */ private self $foo; }',
                '<?php class Foo { /** @var self */ private $foo; }',
            ],
            'report static as self' => [
                '<?php class Foo { /** @var static */ private self $foo; }',
                '<?php class Foo { /** @var static */ private $foo; }',
            ],
            'skip resource special type' => [
                '<?php class Foo { /** @var resource */ private $foo; }',
            ],
            'skip mixed special type' => [
                '<?php class Foo { /** @var mixed */ private $foo; }',
            ],
            'null alone cannot be a property type' => [
                '<?php class Foo { /** @var null */ private $foo; }',
            ],
            'skip mixed types' => [
                '<?php class Foo { /** @var Foo|Bar */ private $foo; }',
            ],
            'nullable type' => [
                '<?php class Foo { /** @var null|Bar */ private ?Bar $foo; }',
                '<?php class Foo { /** @var null|Bar */ private $foo; }',
            ],
            'nullable type reverse order' => [
                '<?php class Foo { /** @var Bar|null */ private ?Bar $foo; }',
                '<?php class Foo { /** @var Bar|null */ private $foo; }',
            ],
            'nullable native type' => [
                '<?php class Foo { /** @var null|array */ private ?array $foo; }',
                '<?php class Foo { /** @var null|array */ private $foo; }',
            ],
            'skip mixed nullable types' => [
                '<?php class Foo { /** @var null|Foo|Bar */ private $foo; }',
            ],
            'generics' => [
                '<?php class Foo { /** @var array<int, bool> */ private array $foo; }',
                '<?php class Foo { /** @var array<int, bool> */ private $foo; }',
            ],
            'array of types' => [
                '<?php class Foo { /** @var Foo[] */ private array $foo; }',
                '<?php class Foo { /** @var Foo[] */ private $foo; }',
            ],
            'array of array of types' => [
                '<?php class Foo { /** @var Foo[][] */ private array $foo; }',
                '<?php class Foo { /** @var Foo[][] */ private $foo; }',
            ],
            'nullable array of types' => [
                '<?php class Foo { /** @var null|Foo[] */ private ?array $foo; }',
                '<?php class Foo { /** @var null|Foo[] */ private $foo; }',
            ],
            'comments' => [
                '<?php
                    class Foo
                    {
                        // comment 0
                        /** @var Foo */ # comment 1
                        public/**/Foo $foo/**/;# comment 2
                    }
                ',
                '<?php
                    class Foo
                    {
                        // comment 0
                        /** @var Foo */ # comment 1
                        public/**/$foo/**/;# comment 2
                    }
                ',
            ],
            'array and traversable' => [
                '<?php class Foo { /** @var array|Traversable */ private iterable $foo; }',
                '<?php class Foo { /** @var array|Traversable */ private $foo; }',
            ],
            'array and traversable with leading slash' => [
                '<?php class Foo { /** @var array|\Traversable */ private iterable $foo; }',
                '<?php class Foo { /** @var array|\Traversable */ private $foo; }',
            ],
            'array and traversable in a namespace' => [
                '<?php
                     namespace App;
                     class Foo {
                         /** @var array|Traversable */
                         private $foo;
                     }
                ',
            ],
            'array and traversable with leading slash in a namespace' => [
                '<?php
                     namespace App;
                     class Foo {
                         /** @var array|\Traversable */
                         private iterable $foo;
                     }
                ',
                '<?php
                     namespace App;
                     class Foo {
                         /** @var array|\Traversable */
                         private $foo;
                     }
                ',
            ],
            'array and imported traversable in a namespace' => [
                '<?php
                     namespace App;
                     use Traversable;
                     class Foo {
                         /** @var array|Traversable */
                         private iterable $foo;
                     }
                ',
                '<?php
                     namespace App;
                     use Traversable;
                     class Foo {
                         /** @var array|Traversable */
                         private $foo;
                     }
                ',
            ],
            'array and object aliased as traversable in a namespace' => [
                '<?php
                     namespace App;
                     use Bar as Traversable;
                     class Foo {
                         /** @var array|Traversable */
                         private $foo;
                     }
                ',
                null,
            ],
            'array of object and traversable' => [
                '<?php class Foo { /** @var Foo[]|Traversable */ private iterable $foo; }',
                '<?php class Foo { /** @var Foo[]|Traversable */ private $foo; }',
            ],
            'array of object and iterable' => [
                '<?php class Foo { /** @var Foo[]|iterable */ private iterable $foo; }',
                '<?php class Foo { /** @var Foo[]|iterable */ private $foo; }',
            ],
            'array of string and array of int' => [
                '<?php class Foo { /** @var string[]|int[] */ private array $foo; }',
                '<?php class Foo { /** @var string[]|int[] */ private $foo; }',
            ],
            'trait' => [
                '<?php trait Foo { /** @var int */ private int $foo; }',
                '<?php trait Foo { /** @var int */ private $foo; }',
            ],
            'static property' => [
                '<?php class Foo { /** @var int */ private static int $foo; }',
                '<?php class Foo { /** @var int */ private static $foo; }',
            ],
            'static property reverse order' => [
                '<?php class Foo { /** @var int */ static private int $foo; }',
                '<?php class Foo { /** @var int */ static private $foo; }',
            ],
            'var' => [
                '<?php class Foo { /** @var int */ var int $foo; }',
                '<?php class Foo { /** @var int */ var $foo; }',
            ],
            'with default value' => [
                '<?php class Foo { /** @var int */ public int $foo = 1; }',
                '<?php class Foo { /** @var int */ public $foo = 1; }',
            ],
            'multiple properties of the same type' => [
                '<?php class Foo {
                    /**
                     * @var int $foo
                     * @var int $bar
                     */
                    public int $foo, $bar;
                }',
                '<?php class Foo {
                    /**
                     * @var int $foo
                     * @var int $bar
                     */
                    public $foo, $bar;
                }',
            ],
            'multiple properties of different types' => [
                '<?php class Foo {
                    /**
                     * @var int    $foo
                     * @var string $bar
                     */
                    public $foo, $bar;
                }',
            ],
            'single property with different annotations' => [
                '<?php class Foo {
                    /**
                     * @var int    $foo
                     * @var string $foo
                     */
                    public $foo;
                }',
            ],
            'multiple properties with missing annotation' => [
                '<?php class Foo {
                    /**
                     * @var int $foo
                     */
                    public $foo, $bar;
                }',
            ],
            'multiple properties with annotation without name' => [
                '<?php class Foo {
                    /**
                     * @var int
                     * @var int $bar
                     */
                    public $foo, $bar;
                }',
            ],
            'multiple properties with annotation without name reverse order' => [
                '<?php class Foo {
                    /**
                     * @var int $foo
                     * @var int
                     */
                    public $foo, $bar;
                }',
            ],
            'multiple properties with extra annotations' => [
                '<?php class Foo {
                    /**
                     * @var string
                     * @var int $foo
                     * @var int $bar
                     * @var int
                     */
                    public int $foo, $bar;
                }',
                '<?php class Foo {
                    /**
                     * @var string
                     * @var int $foo
                     * @var int $bar
                     * @var int
                     */
                    public $foo, $bar;
                }',
            ],
            'abstract method' => [
                '<?php abstract class Foo {
                    /** @var Bar */ private Bar $foo;

                    public abstract function getFoo();
                }',
                '<?php abstract class Foo {
                    /** @var Bar */ private $foo;

                    public abstract function getFoo();
                }',
            ],
            'great number of properties' => [
                '<?php class Foo {
                    /** @var string */
                    private string $foo1;

                    /** @var string */
                    private string $foo2;

                    /** @var int */
                    private int $foo3;

                    /** @var string */
                    private string $foo4;

                    /** @var string */
                    private string $foo5;

                    /** @var string */
                    private string $foo6;

                    /** @var string */
                    private string $foo7;

                    /** @var int */
                    private int $foo8;

                    /** @var string */
                    private string $foo9;

                    /** @var int|null */
                    private ?int $foo10;
                }',
                '<?php class Foo {
                    /** @var string */
                    private $foo1;

                    /** @var string */
                    private $foo2;

                    /** @var int */
                    private $foo3;

                    /** @var string */
                    private $foo4;

                    /** @var string */
                    private $foo5;

                    /** @var string */
                    private $foo6;

                    /** @var string */
                    private $foo7;

                    /** @var int */
                    private $foo8;

                    /** @var string */
                    private $foo9;

                    /** @var int|null */
                    private $foo10;
                }',
            ],
            'anonymous class' => [
                '<?php new class { /** @var int */ private int $foo; };',
                '<?php new class { /** @var int */ private $foo; };',
            ],
            'intersection types' => [
                '<?php class Foo { /** @var Bar&Baz */ private $x; }',
            ],
            'very long class name before ampersand' => [
                '<?php class Foo { /** @var Baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaar&Baz */ private $x; }',
            ],
            'very long class name after ampersand' => [
                '<?php class Foo { /** @var Bar&Baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaz */ private $x; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'readonly properties are always typed, make sure the fixer does not crash' => [
            '<?php class Foo { /** @var int */ private readonly string $foo; }',
        ];
    }
}
