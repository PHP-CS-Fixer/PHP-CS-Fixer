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

    public function provideFixVarCases(): array
    {
        return [
            'testFixVar' => [
                <<<'EOF'
<?php

class Foo
{
    /**
     * @var string Hello!
     */
    public $foo;
}
EOF
                ,
                <<<'EOF'
<?php

class Foo
{
    /**
     * @var string $foo Hello!
     */
    public $foo;
}
EOF
                ,
            ],
            'testFixType' => [
                <<<'EOF'
<?php

class Foo
{
    /**
     * @var int|null
     */
    public $bar;
}
EOF
                ,
                <<<'EOF'
<?php

class Foo
{
    /**
     * @var int|null $bar
     */
    public $bar;
}
EOF
                ,
            ],
            'testDoNothing' => [
                <<<'EOF'
<?php

class Foo
{
    /**
     * @var Foo\Bar This is a variable.
     */
    public $bar;
}
EOF
            ],
            'testFixVarWithNestedKeys' => [
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
EOF
                ,
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
EOF
            ],
            'testSingleLine' => [
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar */
    public $bar;
}
EOF
                ,
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar $bar */
    public $bar;
}
EOF
                ,
            ],
            'testSingleLineProtected' => [
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar */
    protected $bar;
}
EOF
                ,
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar $bar */
    protected $bar;
}
EOF
                ,
            ],
            'testSingleLinePrivate' => [
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar */
    private $bar;
}
EOF
                ,
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar $bar */
    private $bar;
}
EOF
                ,
            ],
            'testSingleLineVar' => [
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar */
    var $bar;
}
EOF
                ,
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar $bar */
    var $bar;
}
EOF
                ,
            ],
            'testSingleLineStatic' => [
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar */
    static public $bar;
}
EOF
                ,
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar $bar */
    static public $bar;
}
EOF
                ,
            ],
            'testSingleLineNoSpace' => [
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar*/
    public $bar;
}
EOF
                ,
                <<<'EOF'
<?php

class Foo
{
    /** @var Foo\Bar $bar*/
    public $bar;
}
EOF
                ,
            ],
            'testInlineDoc' => [
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
EOF
                ,
            ],
            'testSingleLineNoProperty' => [
                <<<'EOF'
<?php

/** @var Foo\Bar $bar */
$bar;
EOF
            ],
            'testMultiLineNoProperty' => [
                <<<'EOF'
<?php

/**
 * @var Foo\Bar $bar
 */
$bar;
EOF
            ],
            'testVeryNestedInlineDoc' => [
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
EOF
                ,
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
EOF
            ],
            [
                '<?php
class Foo
{
    /**
     * @no_candidate string Hello!
     */
    public $foo;
}
',
            ],
            [
                '<?php
class Foo{}
/**  */',
            ],
            'anonymousClass' => [
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
EOF
                ,
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
EOF
                ,
            ],
            [
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
            ],
            'const are not handled by this fixer' => [
                '<?php
class A
{
    /**
     * @var array<string, true> SKIPPED_TYPES
     */
    private const SKIPPED_TYPES = ["a" => true];
}
',
            ],
            'trait' => [
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
            ],
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

    public function provideFix81Cases(): iterable
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
