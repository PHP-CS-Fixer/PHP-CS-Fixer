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
            <<<'EOD'
                <?php

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

                EOD,
        ];

        yield 'It does change doc blocks to multi by default' => [
            <<<'EOD'
                <?php

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

                EOD,
            <<<'EOD'
                <?php

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

                EOD,
        ];

        yield 'It does change doc blocks to single if configured to do so' => [
            <<<'EOD'
                <?php

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

                EOD,
            <<<'EOD'
                <?php

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

                EOD,
            [
                'property' => 'single',
                'const' => 'single',
                'method' => 'single',
            ],
        ];

        yield 'It does change complicated doc blocks to single if configured to do so' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var bool */
                    public $variable1 = true;

                    /** @var bool */
                    public $variable2 = true;

                    /** @Assert\File(mimeTypes={ "image/jpeg", "image/png" }) */
                    public $imageFileObject;
                }

                EOD,
            <<<'EOD'
                <?php

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

                EOD,
            [
                'property' => 'single',
            ],
        ];

        yield 'It does not changes doc blocks from single if configured to do so' => [
            <<<'EOD'
                <?php

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

                EOD,
            null,
            [
                'property' => 'single',
                'const' => 'single',
                'method' => 'single',
            ],
        ];

        yield 'It can be configured to change certain elements to single line' => [
            <<<'EOD'
                <?php

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

                EOD,
            <<<'EOD'
                <?php

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

                EOD,
            [
                'property' => 'single',
            ],
        ];

        yield 'It wont change a doc block to single line if it has multiple useful lines' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * Important
                     * Really important
                     */
                    const FOO_BAR = "foobar";
                }

                EOD,
            null,
            [
                'const' => 'single',
            ],
        ];

        yield 'It updates doc blocks correctly, even with more indentation' => [
            <<<'EOD'
                <?php

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

                EOD,
            <<<'EOD'
                <?php

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

                EOD,
            [
                'property' => 'single',
            ],
        ];

        yield 'It can convert empty doc blocks' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     *
                     */
                    const FOO = "foobar";

                    /**  */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /**  */
                    const FOO = "foobar";

                    /**
                     *
                     */
                    private $foo;
                }
                EOD,
            [
                'property' => 'single',
            ],
        ];

        yield 'It can update doc blocks of static properties' => [
            <<<'EOD'
                <?php

                class Bar
                {
                    /**
                     * Important
                     */
                    public static $variable = "acme";
                }

                EOD,
            <<<'EOD'
                <?php

                class Bar
                {
                    /** Important */
                    public static $variable = "acme";
                }

                EOD,
        ];

        yield 'It can update doc blocks of properties that use the var keyword instead of public' => [
            <<<'EOD'
                <?php

                class Bar
                {
                    /**
                     * Important
                     */
                    var $variable = "acme";
                }

                EOD,
            <<<'EOD'
                <?php

                class Bar
                {
                    /** Important */
                    var $variable = "acme";
                }

                EOD,
        ];

        yield 'It can update doc blocks of static that do not declare visibility' => [
            <<<'EOD'
                <?php

                class Bar
                {
                    /**
                     * Important
                     */
                    static $variable = "acme";
                }

                EOD,
            <<<'EOD'
                <?php

                class Bar
                {
                    /** Important */
                    static $variable = "acme";
                }

                EOD,
        ];

        yield 'It does not change method doc blocks if configured to do so' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @return mixed */
                    public function bar() {}

                    /**
                     * @return void
                     */
                    public function baz() {}
                }
                EOD,
            null,
            [
                'method' => null,
            ],
        ];

        yield 'It does not change property doc blocks if configured to do so' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * @var int
                     */
                    public $foo;

                    /** @var mixed */
                    public $bar;
                }
                EOD,
            null,
            [
                'property' => null,
            ],
        ];

        yield 'It does not change const doc blocks if configured to do so' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * @var int
                     */
                    public const FOO = 1;

                    /** @var mixed */
                    public const BAR = null;
                }
                EOD,
            null,
            [
                'const' => null,
            ],
        ];

        yield 'It can handle constants with visibility, does not crash on trait imports' => [
            <<<'EOD'
                <?php
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
                }
                EOD,
            <<<'EOD'
                <?php
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
                }
                EOD,
            [
                'property' => 'single',
            ],
        ];

        yield 'It can handle properties with type declaration' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /**  */
                    private ?string $foo;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     *
                     */
                    private ?string $foo;
                }
                EOD,
            [
                'property' => 'single',
            ],
        ];

        yield 'It can handle properties with array type declaration' => [
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var string[] */
                    private array $foo;
                }
                EOD,
            <<<'EOD'
                <?php

                class Foo
                {
                    /**
                     * @var string[]
                     */
                    private array $foo;
                }
                EOD,
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
            <<<'EOD'
                <?php

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
                }
                EOD,
            <<<'EOD'
                <?php

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
                }
                EOD,
            [
                'property' => 'single',
            ],
        ];

        yield 'It handles class constants correctly' => [
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
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

                EOD,
        ];

        yield 'It handles class functions correctly' => [
            <<<'EOD'
                <?php
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
                EOD."\n            ",
            <<<'EOD'
                <?php
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
                EOD."\n            ",
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
            <<<'EOD'
                <?php

                class Foo
                {
                    /** @var string[] */
                    private readonly array $foo1;

                    /** @var string[] */
                    readonly private array $foo2;

                    /** @var string[] */
                    readonly array $foo3;
                }
                EOD,
            <<<'EOD'
                <?php

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
                }
                EOD,
            [
                'property' => 'single',
            ],
        ];

        yield 'It handles class constant correctly' => [
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
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

                EOD,
        ];

        yield 'It handles enum functions correctly' => [
            <<<'EOD'
                <?php
                                enum Foo
                                {
                                    /**
                                     * @return void
                                     */
                                    public function hello() {}
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                enum Foo
                                {
                                    /** @return void */
                                    public function hello() {}
                                }
                EOD."\n            ",
        ];

        yield 'It handles enum function with attributes correctly' => [
            <<<'EOD'
                <?php
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
                EOD."\n            ",
            <<<'EOD'
                <?php
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
                EOD."\n            ",
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
