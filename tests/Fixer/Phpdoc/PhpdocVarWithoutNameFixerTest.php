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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer>
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class PhpdocVarWithoutNameFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);

        $expected = str_replace('@var', '@type', $expected);
        if (null !== $input) {
            $input = str_replace('@var', '@type', $input);
        }
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'testFixVar' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var string Hello!
                     */
                    public $foo;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var string $foo Hello!
                     */
                    public $foo;
                }
                EOF,
        ];

        yield 'testFixType' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var int|null
                     */
                    public $bar;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var int|null $bar
                     */
                    public $bar;
                }
                EOF,
        ];

        yield 'testDoNothing' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var Foo\Bar This is a variable.
                     */
                    public $bar;
                }
                EOF,
        ];

        yield 'testFixVarWithNestedKeys' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var array {
                     *     @var bool   $required Whether this element is required
                     *     @var string $label    The display name for this element
                     * }
                     */
                     public $options;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var array $options {
                     *     @var bool   $required Whether this element is required
                     *     @var string $label    The display name for this element
                     * }
                     */
                     public $options;
                }
                EOF,
        ];

        yield 'testSingleLine' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    public $bar;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    public $bar;
                }
                EOF,
        ];

        yield 'testSingleLineProtected' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    protected $bar;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    protected $bar;
                }
                EOF,
        ];

        yield 'testSingleLinePrivate' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    private $bar;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    private $bar;
                }
                EOF,
        ];

        yield 'testSingleLineVar' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    var $bar;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    var $bar;
                }
                EOF,
        ];

        yield 'testSingleLineStatic' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    static public $bar;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    static public $bar;
                }
                EOF,
        ];

        yield 'testSingleLineNoSpace' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar*/
                    public $bar;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar*/
                    public $bar;
                }
                EOF,
        ];

        yield 'testInlineDoc' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * Initializes this class with the given options.
                     *
                     * @param array $options {
                     *     @var bool   $required Whether this element is required
                     *     @var string $label    The display name for this element
                     * }
                     */
                    public function init($options)
                    {
                        // Do something
                    }
                }
                EOF,
        ];

        yield 'testSingleLineNoProperty' => [
            <<<'EOF'
                <?php

                /** @var Foo\Bar $bar */
                $bar;
                EOF,
        ];

        yield 'testMultiLineNoProperty' => [
            <<<'EOF'
                <?php

                /**
                 * @var Foo\Bar $bar
                 */
                $bar;
                EOF,
        ];

        yield 'testVeryNestedInlineDoc' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var array {
                     *     @var array $secondLevelOne   {
                     *         {@internal This should not break}
                     *         @var int $thirdLevel
                     *     }
                     *     @var array $secondLevelTwo   {
                     *         @var array $thirdLevel     {
                     *             @var string $fourthLevel
                     *         }
                     *         @var int   $moreThirdLevel
                     *     }
                     *     @var int   $secondLevelThree
                     * }
                     */
                    public $nestedFoo;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var array $nestedFoo {
                     *     @var array $secondLevelOne   {
                     *         {@internal This should not break}
                     *         @var int $thirdLevel
                     *     }
                     *     @var array $secondLevelTwo   {
                     *         @var array $thirdLevel     {
                     *             @var string $fourthLevel
                     *         }
                     *         @var int   $moreThirdLevel
                     *     }
                     *     @var int   $secondLevelThree
                     * }
                     */
                    public $nestedFoo;
                }
                EOF,
        ];

        yield [
            '<?php
class Foo
{
    /**
     * @no_candidate string Hello!
     */
    public $foo;
}
',
        ];

        yield [
            '<?php
class Foo{}
/**  */',
        ];

        yield 'anonymousClass' => [
            <<<'EOF'
                <?php

                class Anon
                {
                    public function getNewAnon()
                    {
                        return new class()
                        {
                            /**
                             * @var string
                             */
                            public $stringVar;

                            public function getNewAnon()
                            {
                                return new class()
                                {
                                    /**
                                     * @var string
                                     */
                                    public $stringVar;
                                };
                            }
                        };
                    }
                }
                EOF,
            <<<'EOF'
                <?php

                class Anon
                {
                    public function getNewAnon()
                    {
                        return new class()
                        {
                            /**
                             * @var $stringVar string
                             */
                            public $stringVar;

                            public function getNewAnon()
                            {
                                return new class()
                                {
                                    /**
                                     * @var $stringVar string
                                     */
                                    public $stringVar;
                                };
                            }
                        };
                    }
                }
                EOF,
        ];

        yield [
            '<?php
/**
 * Header
 */

class A {} // for the candidate check

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.\'/../vendor/autoload.php\';

/**
 * @var \Foo\Bar $bar
 */
$bar->doSomething(1);

/**
 * @var $bar \Foo\Bar
 */
$bar->doSomething(2);

/**
 * @var User $bar
 */
($bar = tmp())->doSomething(3);

/**
 * @var User $bar
 */
list($bar) = a();
                ',
        ];

        yield 'const are not handled by this fixer' => [
            '<?php
class A
{
    /**
     * @var array<string, true> SKIPPED_TYPES
     */
    private const SKIPPED_TYPES = ["a" => true];
}
',
        ];

        yield 'trait' => [
            '<?php
 trait StaticExample {
    /**
     * @var string Hello!
     */
    public static $static = "foo";
}',
            '<?php
 trait StaticExample {
    /**
     * @var string $static Hello!
     */
    public static $static = "foo";
}',
        ];

        yield 'complex type with union containing callable that has `$this` in signature' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var array<string, string|array{ string|\Closure(mixed, string, $this): int|float }>|false Hello!
                     */
                    public $foo;

                    /** @var int Hello! */
                    public $foo2;

                    /** @var int Hello! */
                    public $foo3;

                    /** @var int Hello! */
                    public $foo4;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /**
                     * @var array<string, string|array{ string|\Closure(mixed, string, $this): int|float }>|false $foo Hello!
                     */
                    public $foo;

                    /** @var int $thi Hello! */
                    public $foo2;

                    /** @var int $thiss Hello! */
                    public $foo3;

                    /** @var int $this2 Hello! */
                    public $foo4;
                }
                EOF,
        ];

        yield 'testFixMultibyteVariableName' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var int Hello! */
                    public $foo;

                    /** @var 🚀 🚀 */
                    public $foo2;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var int $my🚀 Hello! */
                    public $foo;

                    /** @var 🚀 $my 🚀 */
                    public $foo2;
                }
                EOF,
        ];

        yield '@var with callable syntax' => [
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var array<callable(string, Buzz): void> */
                    protected $bar;
                }
                EOF,
            <<<'EOF'
                <?php

                class Foo
                {
                    /** @var array<callable(string $baz, Buzz $buzz): void> */
                    protected $bar;
                }
                EOF,
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

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'readonly' => [
            '<?php

class Foo
{
    /** @var Foo */
    public $bar1;

    /** @var Foo */
    public readonly int $bar2;

    /** @var Foo */
    readonly public int $bar3;

    /** @var Foo */
    readonly int $bar4;
}',
            '<?php

class Foo
{
    /** @var Foo $bar1 */
    public $bar1;

    /** @var Foo $bar2 */
    public readonly int $bar2;

    /** @var Foo $bar3 */
    readonly public int $bar3;

    /** @var Foo $bar4 */
    readonly int $bar4;
}',
        ];

        yield 'final public const are not handled by this fixer' => [
            '<?php
class A
{
    /**
     * @var array<string, true> SKIPPED_TYPES
     */
    final public const SKIPPED_TYPES = ["a" => true];
}
',
        ];
    }

    /**
     * @dataProvider provideFix84Cases
     *
     * @requires PHP 8.4
     */
    public function testFix84(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield 'asymmetric visibility' => [
            <<<'PHP'
                <?php class Foo
                {
                    /** @var bool */
                    public(set) bool $a;

                    /** @var bool */
                    protected(set) bool $b;

                    /** @var bool */
                    private(set) bool $c;
                }
                PHP,
            <<<'PHP'
                <?php class Foo
                {
                    /** @var bool $a */
                    public(set) bool $a;

                    /** @var bool $b */
                    protected(set) bool $b;

                    /** @var bool $c */
                    private(set) bool $c;
                }
                PHP,
        ];
    }
}
