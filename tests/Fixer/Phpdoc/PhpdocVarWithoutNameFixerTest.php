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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer
 */
final class PhpdocVarWithoutNameFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixVarCases
     */
    public function testFixVar(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixVarCases
     */
    public function testFixType(string $expected, ?string $input = null): void
    {
        $expected = str_replace('@var', '@type', $expected);
        if (null !== $input) {
            $input = str_replace('@var', '@type', $input);
        }

        $this->doTest($expected, $input);
    }

    public static function provideFixVarCases(): iterable
    {
        yield 'testFixVar' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * @var string Hello!
                     */
                    public $foo;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * @var string $foo Hello!
                     */
                    public $foo;
                }
                EOD,
        ];

        yield 'testFixType' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * @var int|null
                     */
                    public $bar;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * @var int|null $bar
                     */
                    public $bar;
                }
                EOD,
        ];

        yield 'testDoNothing' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * @var Foo\Bar This is a variable.
                     */
                    public $bar;
                }
                EOD
        ];

        yield 'testFixVarWithNestedKeys' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
        ];

        yield 'testSingleLine' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    public $bar;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    public $bar;
                }
                EOD,
        ];

        yield 'testSingleLineProtected' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    protected $bar;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    protected $bar;
                }
                EOD,
        ];

        yield 'testSingleLinePrivate' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    private $bar;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    private $bar;
                }
                EOD,
        ];

        yield 'testSingleLineVar' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    var $bar;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    var $bar;
                }
                EOD,
        ];

        yield 'testSingleLineStatic' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar */
                    static public $bar;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar */
                    static public $bar;
                }
                EOD,
        ];

        yield 'testSingleLineNoSpace' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar*/
                    public $bar;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var Foo\Bar $bar*/
                    public $bar;
                }
                EOD,
        ];

        yield 'testInlineDoc' => [
            <<<'EOD'
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
                EOD,
        ];

        yield 'testSingleLineNoProperty' => [
            <<<'EOD'
                <?php

                /** @var Foo\Bar $bar */
                $bar;
                EOD
        ];

        yield 'testMultiLineNoProperty' => [
            <<<'EOD'
                <?php

                /**
                 * @var Foo\Bar $bar
                 */
                $bar;
                EOD
        ];

        yield 'testVeryNestedInlineDoc' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD
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
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD,
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
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'testFixMultibyteVariableName' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var int Hello! */
                    public $foo;

                    /** @var 🚀 🚀 */
                    public $foo2;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var int $my🚀 Hello! */
                    public $foo;

                    /** @var 🚀 $my 🚀 */
                    public $foo2;
                }
                EOD,
        ];

        yield '@var with callable syntax' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var array<callable(string, Buzz): void> */
                    protected $bar;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var array<callable(string $baz, Buzz $buzz): void> */
                    protected $bar;
                }
                EOD,
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
}
