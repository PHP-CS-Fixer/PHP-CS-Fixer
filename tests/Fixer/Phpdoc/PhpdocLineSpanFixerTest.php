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
 * @author Gert de Pagter <BackEndTea@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer
 */
final class PhpdocLineSpanFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param array<string, mixed> $config
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'It does not change doc blocks if not needed' => [
            '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /**
     * @var bool
     */
    public $variable = true;

    /**
     * @var bool
     */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
        ];

        yield 'It does change doc blocks to multi by default' => [
            '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /**
     * @var bool
     */
    public $variable = true;

    /**
     * @var bool
     */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
            '<?php

class Foo
{
    /** Important */
    const FOO_BAR = "foobar";

    /** @var bool */
    public $variable = true;

    /** @var bool */
    private $var = false;


    /** @return void */
    public function hello() {}
}
',
        ];

        yield 'It does change doc blocks to single if configured to do so' => [
            '<?php

class Foo
{
    /** Important */
    const FOO_BAR = "foobar";

    /** @var bool */
    public $variable = true;

    /** @var bool */
    private $var = false;


    /** @return void */
    public function hello() {}
}
',
            '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /**
     * @var bool
     */
    public $variable = true;

    /**
     * @var bool
     */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
            [
                'property' => 'single',
                'const' => 'single',
                'method' => 'single',
            ],
        ];

        yield 'It does change complicated doc blocks to single if configured to do so' => [
            '<?php

class Foo
{
    /** @var bool */
    public $variable1 = true;

    /** @var bool */
    public $variable2 = true;

    /** @Assert\File(mimeTypes={ "image/jpeg", "image/png" }) */
    public $imageFileObject;
}
',
            '<?php

class Foo
{
    /**
     * @var bool */
    public $variable1 = true;

    /** @var bool
     */
    public $variable2 = true;

    /**
     * @Assert\File(mimeTypes={ "image/jpeg", "image/png" })
     */
    public $imageFileObject;
}
',
            [
                'property' => 'single',
            ],
        ];

        yield 'It does not changes doc blocks from single if configured to do so' => [
            '<?php

class Foo
{
    /** Important */
    const FOO_BAR = "foobar";

    /** @var bool */
    public $variable = true;

    /** @var bool */
    private $var = false;


    /** @return void */
    public function hello() {}
}
',
            null,
            [
                'property' => 'single',
                'const' => 'single',
                'method' => 'single',
            ],
        ];

        yield 'It can be configured to change certain elements to single line' => [
            '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /** @var bool */
    public $variable = true;

    /** @var bool */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
            '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /**
     * @var bool
     */
    public $variable = true;

    /**
     * @var bool
     */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
            [
                'property' => 'single',
            ],
        ];

        yield 'It wont change a doc block to single line if it has multiple useful lines' => [
            '<?php

class Foo
{
    /**
     * Important
     * Really important
     */
    const FOO_BAR = "foobar";
}
',
            null,
            [
                'const' => 'single',
            ],
        ];

        yield 'It updates doc blocks correctly, even with more indentation' => [
            '<?php

if (false) {
    class Foo
    {
        /** @var bool */
        public $var = true;

        /**
         * @return void
         */
        public function hello () {}
    }
}
',
            '<?php

if (false) {
    class Foo
    {
        /**
         * @var bool
         */
        public $var = true;

        /** @return void */
        public function hello () {}
    }
}
',
            [
                'property' => 'single',
            ],
        ];

        yield 'It can convert empty doc blocks' => [
            '<?php

class Foo
{
    /**
     *
     */
    const FOO = "foobar";

    /**  */
    private $foo;
}',
            '<?php

class Foo
{
    /**  */
    const FOO = "foobar";

    /**
     *
     */
    private $foo;
}',
            [
                'property' => 'single',
            ],
        ];

        yield 'It can update doc blocks of static properties' => [
            '<?php

class Bar
{
    /**
     * Important
     */
    public static $variable = "acme";
}
',
            '<?php

class Bar
{
    /** Important */
    public static $variable = "acme";
}
',
        ];

        yield 'It can update doc blocks of properties that use the var keyword instead of public' => [
            '<?php

class Bar
{
    /**
     * Important
     */
    var $variable = "acme";
}
',
            '<?php

class Bar
{
    /** Important */
    var $variable = "acme";
}
',
        ];

        yield 'It can update doc blocks of static that do not declare visibility' => [
            '<?php

class Bar
{
    /**
     * Important
     */
    static $variable = "acme";
}
',
            '<?php

class Bar
{
    /** Important */
    static $variable = "acme";
}
',
        ];

        yield 'It does not change method doc blocks if configured to do so' => [
            '<?php

class Foo
{
    /** @return mixed */
    public function bar() {}

    /**
     * @return void
     */
    public function baz() {}
}',
            null,
            [
                'method' => null,
            ],
        ];

        yield 'It does not change property doc blocks if configured to do so' => [
            '<?php

class Foo
{
    /**
     * @var int
     */
    public $foo;

    /** @var mixed */
    public $bar;
}',
            null,
            [
                'property' => null,
            ],
        ];

        yield 'It does not change const doc blocks if configured to do so' => [
            '<?php

class Foo
{
    /**
     * @var int
     */
    public const FOO = 1;

    /** @var mixed */
    public const BAR = null;
}',
            null,
            [
                'const' => null,
            ],
        ];

        yield 'It can handle constants with visibility, does not crash on trait imports' => [
            '<?php
trait Bar
{}

class Foo
{
    /** whatever */
    use Bar;

    /**
     *
     */
    public const FOO = "foobar";

    /**  */
    private $foo;
}',
            '<?php
trait Bar
{}

class Foo
{
    /** whatever */
    use Bar;

    /**  */
    public const FOO = "foobar";

    /**
     *
     */
    private $foo;
}',
            [
                'property' => 'single',
            ],
        ];

        yield 'It can handle properties with type declaration' => [
            '<?php

class Foo
{
    /**  */
    private ?string $foo;
}',
            '<?php

class Foo
{
    /**
     *
     */
    private ?string $foo;
}',
            [
                'property' => 'single',
            ],
        ];

        yield 'It can handle properties with array type declaration' => [
            '<?php

class Foo
{
    /** @var string[] */
    private array $foo;
}',
            '<?php

class Foo
{
    /**
     * @var string[]
     */
    private array $foo;
}',
            [
                'property' => 'single',
            ],
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     *
     * @param array<string, mixed> $config
     */
    public function testFix80(string $expected, string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'It detects attributes between docblock and token' => [
            '<?php

class Foo
{
    /** @var string[] */
    #[Attribute1]
    private array $foo1;

    /** @var string[] */
    #[Attribute1]
    #[Attribute2]
    private array $foo2;

    /** @var string[] */
    #[Attribute1, Attribute2]
    public array $foo3;
}',
            '<?php

class Foo
{
    /**
     * @var string[]
     */
    #[Attribute1]
    private array $foo1;

    /**
     * @var string[]
     */
    #[Attribute1]
    #[Attribute2]
    private array $foo2;

    /**
     * @var string[]
     */
    #[Attribute1, Attribute2]
    public array $foo3;
}',
            [
                'property' => 'single',
            ],
        ];

        yield 'It handles class constants correctly' => [
            '<?php
class Foo
{
    /**
     * 0
     */
    #[Attribute1]
    const B0 = "0";

    /**
     * 1
     */
    #[Attribute1]
    #[Attribute2]
    public const B1 = "1";

    /**
     * 2
     */
    #[Attribute1, Attribute2]
    public const B2 = "2";
}
',
            '<?php
class Foo
{
    /** 0 */
    #[Attribute1]
    const B0 = "0";

    /** 1 */
    #[Attribute1]
    #[Attribute2]
    public const B1 = "1";

    /** 2 */
    #[Attribute1, Attribute2]
    public const B2 = "2";
}
',
        ];

        yield 'It handles class functions correctly' => [
            '<?php
                class Foo
                {
                    /**
                     * @return void
                     */
                    #[Attribute1]
                    public function hello1() {}

                    /**
                     * @return void
                     */
                    #[Attribute1]
                    #[Attribute2]
                    public function hello2() {}

                    /**
                     * @return void
                     */
                    #[Attribute1, Attribute2]
                    public function hello3() {}
                }
            ',
            '<?php
                class Foo
                {
                    /** @return void */
                    #[Attribute1]
                    public function hello1() {}

                    /** @return void */
                    #[Attribute1]
                    #[Attribute2]
                    public function hello2() {}

                    /** @return void */
                    #[Attribute1, Attribute2]
                    public function hello3() {}
                }
            ',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     *
     * @param array<string, mixed> $config
     */
    public function testFix81(string $expected, string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'It handles readonly properties correctly' => [
            '<?php

class Foo
{
    /** @var string[] */
    private readonly array $foo1;

    /** @var string[] */
    readonly private array $foo2;

    /** @var string[] */
    readonly array $foo3;
}',
            '<?php

class Foo
{
    /**
     * @var string[]
     */
    private readonly array $foo1;

    /**
     * @var string[]
     */
    readonly private array $foo2;

    /**
     * @var string[]
     */
    readonly array $foo3;
}',
            [
                'property' => 'single',
            ],
        ];

        yield 'It handles class constant correctly' => [
            '<?php
class Foo
{
    /**
     * 0
     */
    const B0 = "0";

    /**
     * 1
     */
    final public const B1 = "1";

    /**
     * 2
     */
    public final const B2 = "2";

    /**
     * 3
     */
    final const B3 = "3";
}
',
            '<?php
class Foo
{
    /** 0 */
    const B0 = "0";

    /** 1 */
    final public const B1 = "1";

    /** 2 */
    public final const B2 = "2";

    /** 3 */
    final const B3 = "3";
}
',
        ];

        yield 'It handles enum functions correctly' => [
            '<?php
                enum Foo
                {
                    /**
                     * @return void
                     */
                    public function hello() {}
                }
            ',
            '<?php
                enum Foo
                {
                    /** @return void */
                    public function hello() {}
                }
            ',
        ];

        yield 'It handles enum function with attributes correctly' => [
            '<?php
                enum Foo
                {
                    /**
                     * @return void
                     */
                    #[Attribute1]
                    public function hello1() {}

                    /**
                     * @return void
                     */
                    #[Attribute1]
                    #[Attribute2]
                    public function hello2() {}

                    /**
                     * @return void
                     */
                    #[Attribute1, Attribute2]
                    public function hello3() {}
                }
            ',
            '<?php
                enum Foo
                {
                    /** @return void */
                    #[Attribute1]
                    public function hello1() {}

                    /** @return void */
                    #[Attribute1]
                    #[Attribute2]
                    public function hello2() {}

                    /** @return void */
                    #[Attribute1, Attribute2]
                    public function hello3() {}
                }
            ',
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield 'constant in trait' => [
            <<<'PHP'
                <?php
                trait Foo {
                    /**
                     * @var string
                     */
                    const Foo = 'foo';
                }
                PHP,
            <<<'PHP'
                <?php
                trait Foo {
                    /** @var string */
                    const Foo = 'foo';
                }
                PHP,
        ];
    }
}
