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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author John Kelly <wablam@gmail.com>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Egidijus Girčys <e.gircys@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer
 */
final class MultilineWhitespaceBeforeSemicolonsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideMultiLineWhitespaceFixCases
     */
    public function testFixMultiLineWhitespace(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NO_MULTI_LINE]);
        $this->doTest($expected, $input);
    }

    public static function provideMultiLineWhitespaceFixCases(): array
    {
        return [
            [
                '<?php
                    $foo->bar(); // test',
                '<?php
                    $foo->bar() // test
                    ;',
            ],
            [
                '<?php echo(1); // test',
                "<?php echo(1) // test\n;",
            ],
            [
                "<?php echo(1); // test\n",
            ],
            [
                '<?php
                    $foo->bar(); # test',
                '<?php
                    $foo->bar() # test


                ;',
            ],
            [
                '<?php
                    $foo->bar();// test',
                '<?php
                    $foo->bar()// test


                ;',
            ],
            [
                "<?php\n;",
            ],
            [
                '<?php
$this
    ->setName(\'readme1\')
    ->setDescription(\'Generates the README\');
',
                '<?php
$this
    ->setName(\'readme1\')
    ->setDescription(\'Generates the README\')
;
',
            ],
            [
                '<?php
$this
    ->setName(\'readme2\')
    ->setDescription(\'Generates the README\');
',
                '<?php
$this
    ->setName(\'readme2\')
    ->setDescription(\'Generates the README\')
    ;
',
            ],
            [
                '<?php echo "$this->foo(\'with param containing ;\') ;" ;',
            ],
            [
                '<?php $this->foo();',
            ],
            [
                '<?php $this->foo() ;',
            ],
            [
                '<?php $this->foo(\'with param containing ;\') ;',
            ],
            [
                '<?php $this->foo(\'with param containing ) ; \') ;',
            ],
            [
                '<?php $this->foo("with param containing ) ; ")  ; ?>',
            ],
            [
                '<?php $this->foo("with semicolon in string) ; "); ?>',
            ],
            [
                '<?php
$this
    ->example();',
                '<?php
$this
    ->example()

    ;',
            ],
            [
                '<?php
                    Foo::bar(); // test',
                '<?php
                    Foo::bar() // test
                    ;',
            ],
            [
                '<?php
                    Foo::bar(); # test',
                '<?php
                    Foo::bar() # test


                ;',
            ],
            [
                '<?php
self
    ::setName(\'readme1\')
    ->setDescription(\'Generates the README\');
',
                '<?php
self
    ::setName(\'readme1\')
    ->setDescription(\'Generates the README\')
;
',
            ],
            [
                '<?php
self
    ::setName(\'readme2\')
    ->setDescription(\'Generates the README\');
',
                '<?php
self
    ::setName(\'readme2\')
    ->setDescription(\'Generates the README\')
    ;
',
            ],
            [
                '<?php echo "self::foo(\'with param containing ;\') ;" ;',
            ],
            [
                '<?php self::foo();',
            ],
            [
                '<?php self::foo() ;',
            ],
            [
                '<?php self::foo(\'with param containing ;\') ;',
            ],
            [
                '<?php self::foo(\'with param containing ) ; \') ;',
            ],
            [
                '<?php self::foo("with param containing ) ; ")  ; ?>',
            ],
            [
                '<?php self::foo("with semicolon in string) ; "); ?>',
            ],
            [
                '<?php
self
    ::example();',
                '<?php
self
    ::example()

    ;',
            ],
            [
                '<?php
$seconds = $minutes
    * 60; // seconds in a minute',
                '<?php
$seconds = $minutes
    * 60 // seconds in a minute
;',
            ],
            [
                '<?php
$seconds = $minutes
    * (int) \'60\'; // seconds in a minute',
                '<?php
$seconds = $minutes
    * (int) \'60\' // seconds in a minute
;',
            ],
            [
                '<?php
$secondsPerMinute = 60;
$seconds = $minutes
    * $secondsPerMinute; // seconds in a minute',
                '<?php
$secondsPerMinute = 60;
$seconds = $minutes
    * $secondsPerMinute // seconds in a minute
;',
            ],
            [
                '<?php
$secondsPerMinute = 60;
$seconds = $minutes
    * 60 * (int) true; // seconds in a minute',
                '<?php
$secondsPerMinute = 60;
$seconds = $minutes
    * 60 * (int) true // seconds in a minute
;',
            ],
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesMultiLineWhitespaceFixCases
     */
    public function testMessyWhitespacesMultiLineWhitespace(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NO_MULTI_LINE]);
        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesMultiLineWhitespaceFixCases(): array
    {
        return [
            [
                '<?php echo(1); // test',
                "<?php echo(1) // test\r\n;",
            ],
        ];
    }

    /**
     * @dataProvider provideSemicolonForChainedCallsFixCases
     */
    public function testSemicolonForChainedCallsFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]);
        $this->doTest($expected, $input);
    }

    public static function provideSemicolonForChainedCallsFixCases(): array
    {
        return [
            [
                '<?php

                    $this
                        ->method1()
                        ->method2()
                    ;
                ?>',
                '<?php

                    $this
                        ->method1()
                        ->method2();
                ?>',
            ],
            [
                '<?php

                    $this
                        ->method1()
                        ->method2() // comment
                    ;


',
                '<?php

                    $this
                        ->method1()
                        ->method2(); // comment


',
            ],
            [
                '<?php

                    $service->method1()
                        ->method2()
                    ;

                    $service->method3();
                    $this
                        ->method1()
                        ->method2()
                    ;',
                '<?php

                    $service->method1()
                        ->method2()
                    ;

                    $service->method3();
                    $this
                        ->method1()
                        ->method2();',
            ],
            [
                '<?php

                    $service
                        ->method2()
                    ;
                ?>',
                '<?php

                    $service
                        ->method2();
                ?>',
            ],
            [
                '<?php

                    $service->method1()
                        ->method2()
                        ->method3()
                        ->method4()
                    ;
                ?>',
                '<?php

                    $service->method1()
                        ->method2()
                        ->method3()
                        ->method4();
                ?>',
            ],
            [
                '<?php

                    $this->service->method1()
                        ->method2([1, 2])
                        ->method3(
                            "2",
                            2,
                            [1, 2]
                        )
                        ->method4()
                    ;
                ?>',
                '<?php

                    $this->service->method1()
                        ->method2([1, 2])
                        ->method3(
                            "2",
                            2,
                            [1, 2]
                        )
                        ->method4();
                ?>',
            ],
            [
                '<?php

                    $service
                        ->method1()
                            ->method2()
                        ->method3()
                            ->method4()
                    ;
                ?>',
                '<?php

                    $service
                        ->method1()
                            ->method2()
                        ->method3()
                            ->method4();
                ?>',
            ],
            [
                '<?php
                    $f = "g";

                    $service
                        ->method1("a", true)
                        ->method2(true, false)
                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                        ->method4(1, "a", $f)
                    ;
                ?>',
                '<?php
                    $f = "g";

                    $service
                        ->method1("a", true)
                        ->method2(true, false)
                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                        ->method4(1, "a", $f);
                ?>',
            ],
            [
                '<?php
                    $f = "g";

                    $service
                        ->method1("a", true) // this is a comment
                        /* ->method2(true, false) */
                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                        ->method4(1, "a", $f) /* this is a comment */
                    ;
                ?>',
                '<?php
                    $f = "g";

                    $service
                        ->method1("a", true) // this is a comment
                        /* ->method2(true, false) */
                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                        ->method4(1, "a", $f); /* this is a comment */
                ?>',
            ],
            [
                '<?php
                    $service->method1();
                    $service->method2()->method3();
                ?>',
            ],
            [
                '<?php
                    $service->method1() ;
                    $service->method2()->method3() ;
                ?>',
            ],
            [
                '<?php

                    $service
                        ->method2(function ($a) {
                            $a->otherCall()
                                ->a()
                                ->b()
                            ;
                        })
                    ;
                ?>',
                '<?php

                    $service
                        ->method2(function ($a) {
                            $a->otherCall()
                                ->a()
                                ->b()
                            ;
                        });
                ?>',
            ],
            [
                '<?php

                    $data = $service
                        ->method2(function ($a) {
                            $a->otherCall()
                                ->a()
                                ->b(array_merge([
                                        1 => 1,
                                        2 => 2,
                                    ], $this->getOtherArray()
                                ))
                            ;
                        })
                    ;
                ?>',
                '<?php

                    $data = $service
                        ->method2(function ($a) {
                            $a->otherCall()
                                ->a()
                                ->b(array_merge([
                                        1 => 1,
                                        2 => 2,
                                    ], $this->getOtherArray()
                                ));
                        });
                ?>',
            ],
            [
                '<?php

                    $service
                        ->method1(null, null, [
                            null => null,
                            1 => $data->getId() > 0,
                        ])
                        ->method2(4, Type::class)
                    ;
',
                '<?php

                    $service
                        ->method1(null, null, [
                            null => null,
                            1 => $data->getId() > 0,
                        ])
                        ->method2(4, Type::class);
',
            ],
            [
                '<?php
$this
                        ->method1()
                        ->method2()
;
                ?>',
                '<?php
$this
                        ->method1()
                        ->method2();
                ?>',
            ],
            [
                '<?php

                    self
                        ::method1()
                        ->method2()
                    ;
                ?>',
                '<?php

                    self
                        ::method1()
                        ->method2();
                ?>',
            ],
            [
                '<?php

                    self
                        ::method1()
                        ->method2() // comment
                    ;


',
                '<?php

                    self
                        ::method1()
                        ->method2(); // comment


',
            ],
            [
                '<?php

                    Service::method1()
                        ->method2()
                    ;

                    Service::method3();
                    $this
                        ->method1()
                        ->method2()
                    ;',
                '<?php

                    Service::method1()
                        ->method2()
                    ;

                    Service::method3();
                    $this
                        ->method1()
                        ->method2();',
            ],
            [
                '<?php

                    Service
                        ::method2()
                    ;
                ?>',
                '<?php

                    Service
                        ::method2();
                ?>',
            ],
            [
                '<?php

                    Service::method1()
                        ->method2()
                        ->method3()
                        ->method4()
                    ;
                ?>',
                '<?php

                    Service::method1()
                        ->method2()
                        ->method3()
                        ->method4();
                ?>',
            ],
            [
                '<?php

                    self::method1()
                        ->method2([1, 2])
                        ->method3(
                            "2",
                            2,
                            [1, 2]
                        )
                        ->method4()
                    ;
                ?>',
                '<?php

                    self::method1()
                        ->method2([1, 2])
                        ->method3(
                            "2",
                            2,
                            [1, 2]
                        )
                        ->method4();
                ?>',
            ],
            [
                '<?php

                    Service
                        ::method1()
                            ->method2()
                        ->method3()
                            ->method4()
                    ;
                ?>',
                '<?php

                    Service
                        ::method1()
                            ->method2()
                        ->method3()
                            ->method4();
                ?>',
            ],
            [
                '<?php
                    $f = "g";

                    Service
                        ::method1("a", true)
                        ->method2(true, false)
                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                        ->method4(1, "a", $f)
                    ;
                ?>',
                '<?php
                    $f = "g";

                    Service
                        ::method1("a", true)
                        ->method2(true, false)
                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                        ->method4(1, "a", $f);
                ?>',
            ],
            [
                '<?php
                    $f = "g";

                    Service
                        ::method1("a", true) // this is a comment
                        /* ->method2(true, false) */
                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                        ->method4(1, "a", $f) /* this is a comment */
                    ;
                ?>',
                '<?php
                    $f = "g";

                    Service
                        ::method1("a", true) // this is a comment
                        /* ->method2(true, false) */
                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                        ->method4(1, "a", $f); /* this is a comment */
                ?>',
            ],
            [
                '<?php
                    Service::method1();
                    Service::method2()->method3();
                ?>',
            ],
            [
                '<?php
                    Service::method1() ;
                    Service::method2()->method3() ;
                ?>',
            ],
            [
                '<?php

                    Service
                        ::method2(function ($a) {
                            $a->otherCall()
                                ->a()
                                ->b()
                            ;
                        })
                    ;
                ?>',
                '<?php

                    Service
                        ::method2(function ($a) {
                            $a->otherCall()
                                ->a()
                                ->b()
                            ;
                        });
                ?>',
            ],
            [
                '<?php

                    $data = Service
                        ::method2(function () {
                            Foo::otherCall()
                                ->a()
                                ->b(array_merge([
                                        1 => 1,
                                        2 => 2,
                                    ], $this->getOtherArray()
                                ))
                            ;
                        })
                    ;
                ?>',
                '<?php

                    $data = Service
                        ::method2(function () {
                            Foo::otherCall()
                                ->a()
                                ->b(array_merge([
                                        1 => 1,
                                        2 => 2,
                                    ], $this->getOtherArray()
                                ));
                        });
                ?>',
            ],
            [
                '<?php

                    Service
                        ::method1(null, null, [
                            null => null,
                            1 => $data->getId() > 0,
                        ])
                        ->method2(4, Type::class)
                    ;
',
                '<?php

                    Service
                        ::method1(null, null, [
                            null => null,
                            1 => $data->getId() > 0,
                        ])
                        ->method2(4, Type::class);
',
            ],
            [
                '<?php
Service
                        ::method1()
                        ->method2()
;
                ?>',
                '<?php
Service
                        ::method1()
                        ->method2();
                ?>',
            ],
            [
                '<?php

                    function foo($bar)
                    {
                        if ($bar === 1) {
                            $baz
                                ->bar()
                            ;
                        }

                        return (new Foo($bar))
                            ->baz()
                        ;
                    }
                ?>',
                '<?php

                    function foo($bar)
                    {
                        if ($bar === 1) {
                            $baz
                                ->bar();
                        }

                        return (new Foo($bar))
                            ->baz();
                    }
                ?>',
            ],
            [
                '<?php

                    $foo = (new Foo($bar))
                        ->baz()
                    ;

                    function foo($bar)
                    {
                        $foo = (new Foo($bar))
                            ->baz()
                        ;
                    }
                ?>',
                '<?php

                    $foo = (new Foo($bar))
                        ->baz();

                    function foo($bar)
                    {
                        $foo = (new Foo($bar))
                            ->baz();
                    }
                ?>',
            ],
            [
                '<?php $this
        ->methodA()
        ->methodB();',
            ],
            [
                "<?php\n\$this\n    ->one()\n    ->two(2, )\n;",
                "<?php\n\$this\n    ->one()\n    ->two(2, );",
            ],
            [
                "<?php\n\$this\n    ->one(1, )\n    ->two()\n;",
                "<?php\n\$this\n    ->one(1, )\n    ->two();",
            ],
            [
                '<?php

                    $foo->bar();

                    Service::method1()
                        ->method2()
                        ->method3()->method4()
                    ;
                ?>',
                '<?php

                    $foo->bar()
                    ;

                    Service::method1()
                        ->method2()
                        ->method3()->method4();
                ?>',
            ],
            [
                '<?php

                    $foo->bar();

                    \Service::method1()
                        ->method2()
                        ->method3()->method4()
                    ;
                ?>',
                '<?php

                    $foo->bar()
                    ;

                    \Service::method1()
                        ->method2()
                        ->method3()->method4();
                ?>',
            ],
            [
                '<?php

                    $foo->bar();

                    Ns\Service::method1()
                        ->method2()
                        ->method3()->method4()
                    ;
                ?>',
                '<?php

                    $foo->bar()
                    ;

                    Ns\Service::method1()
                        ->method2()
                        ->method3()->method4();
                ?>',
            ],
            [
                '<?php

                    $foo->bar();

                    \Ns\Service::method1()
                        ->method2()
                        ->method3()->method4()
                    ;
                ?>',
                '<?php

                    $foo->bar()
                    ;

                    \Ns\Service::method1()
                        ->method2()
                        ->method3()->method4();
                ?>',
            ],
            [
                '<?php
$this
    ->setName(\'readme2\')
    ->setDescription(\'Generates the README\')
;
',
                '<?php
$this
    ->setName(\'readme2\')
    ->setDescription(\'Generates the README\')
    ;
',
            ],
            [
                '<?php
$this
    ->foo()
    ->{$bar ? \'bar\' : \'baz\'}()
;
',
            ],
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesSemicolonForChainedCallsFixCases
     */
    public function testMessyWhitespacesSemicolonForChainedCalls(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]);
        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesSemicolonForChainedCallsFixCases(): array
    {
        return [
            [
                "<?php\r\n\r\n   \$this\r\n\t->method1()\r\n\t\t->method2()\r\n   ;",
                "<?php\r\n\r\n   \$this\r\n\t->method1()\r\n\t\t->method2();",
            ],
            [
                "<?php\r\n\r\n\t\$this->method1()\r\n\t\t->method2()\r\n\t\t->method(3)\r\n\t;",
                "<?php\r\n\r\n\t\$this->method1()\r\n\t\t->method2()\r\n\t\t->method(3);",
            ],
            [
                "<?php\r\n\r\n\t\$data   =  \$service\r\n\t ->method2(function (\$a) {\r\n\t\t\t\$a->otherCall()\r\n\t\t\t\t->a()\r\n\t\t\t\t->b(array_merge([\r\n\t\t\t\t\t\t1 => 1,\r\n\t\t\t\t\t\t2 => 2,\r\n\t\t\t\t\t], \$this->getOtherArray()\r\n\t\t\t\t))\r\n\t\t\t;\r\n\t\t})\r\n\t;\r\n?>",
                "<?php\r\n\r\n\t\$data   =  \$service\r\n\t ->method2(function (\$a) {\r\n\t\t\t\$a->otherCall()\r\n\t\t\t\t->a()\r\n\t\t\t\t->b(array_merge([\r\n\t\t\t\t\t\t1 => 1,\r\n\t\t\t\t\t\t2 => 2,\r\n\t\t\t\t\t], \$this->getOtherArray()\r\n\t\t\t\t));\r\n\t\t});\r\n?>",
            ],
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testFix80(): void
    {
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]);
        $this->doTest(
            '<?php

                $foo?->method1()
                    ?->method2()
                    ?->method3()
                ;
                ',
            '<?php

                $foo?->method1()
                    ?->method2()
                    ?->method3();
                '
        );
    }
}
