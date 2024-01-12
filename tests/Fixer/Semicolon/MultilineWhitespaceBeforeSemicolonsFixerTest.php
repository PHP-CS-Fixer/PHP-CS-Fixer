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
     * @dataProvider provideFixMultiLineWhitespaceCases
     */
    public function testFixMultiLineWhitespace(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NO_MULTI_LINE]);
        $this->doTest($expected, $input);
    }

    public static function provideFixMultiLineWhitespaceCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                    $foo->bar(); // test
                EOD,
            <<<'EOD'
                <?php
                                    $foo->bar() // test
                                    ;
                EOD,
        ];

        yield [
            '<?php echo(1); // test',
            "<?php echo(1) // test\n;",
        ];

        yield [
            "<?php echo(1); // test\n",
        ];

        yield [
            <<<'EOD'
                <?php
                                    $foo->bar(); # test
                EOD,
            <<<'EOD'
                <?php
                                    $foo->bar() # test


                                ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $foo->bar();// test
                EOD,
            <<<'EOD'
                <?php
                                    $foo->bar()// test


                                ;
                EOD,
        ];

        yield [
            "<?php\n;",
        ];

        yield [
            '<?= $a; ?>',
        ];

        yield [
            <<<'EOD'
                <?php
                $this
                    ->setName('readme1')
                    ->setDescription('Generates the README');

                EOD,
            <<<'EOD'
                <?php
                $this
                    ->setName('readme1')
                    ->setDescription('Generates the README')
                ;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $this
                    ->setName('readme2')
                    ->setDescription('Generates the README');

                EOD,
            <<<'EOD'
                <?php
                $this
                    ->setName('readme2')
                    ->setDescription('Generates the README')
                    ;

                EOD,
        ];

        yield [
            '<?php echo "$this->foo(\'with param containing ;\') ;" ;',
        ];

        yield [
            '<?php $this->foo();',
        ];

        yield [
            '<?php $this->foo() ;',
        ];

        yield [
            '<?php $this->foo(\'with param containing ;\') ;',
        ];

        yield [
            '<?php $this->foo(\'with param containing ) ; \') ;',
        ];

        yield [
            '<?php $this->foo("with param containing ) ; ")  ; ?>',
        ];

        yield [
            '<?php $this->foo("with semicolon in string) ; "); ?>',
        ];

        yield [
            <<<'EOD'
                <?php
                $this
                    ->example();
                EOD,
            <<<'EOD'
                <?php
                $this
                    ->example()

                    ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    Foo::bar(); // test
                EOD,
            <<<'EOD'
                <?php
                                    Foo::bar() // test
                                    ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    Foo::bar(); # test
                EOD,
            <<<'EOD'
                <?php
                                    Foo::bar() # test


                                ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                self
                    ::setName('readme1')
                    ->setDescription('Generates the README');

                EOD,
            <<<'EOD'
                <?php
                self
                    ::setName('readme1')
                    ->setDescription('Generates the README')
                ;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                self
                    ::setName('readme2')
                    ->setDescription('Generates the README');

                EOD,
            <<<'EOD'
                <?php
                self
                    ::setName('readme2')
                    ->setDescription('Generates the README')
                    ;

                EOD,
        ];

        yield [
            '<?php echo "self::foo(\'with param containing ;\') ;" ;',
        ];

        yield [
            '<?php self::foo();',
        ];

        yield [
            '<?php self::foo() ;',
        ];

        yield [
            '<?php self::foo(\'with param containing ;\') ;',
        ];

        yield [
            '<?php self::foo(\'with param containing ) ; \') ;',
        ];

        yield [
            '<?php self::foo("with param containing ) ; ")  ; ?>',
        ];

        yield [
            '<?php self::foo("with semicolon in string) ; "); ?>',
        ];

        yield [
            <<<'EOD'
                <?php
                self
                    ::example();
                EOD,
            <<<'EOD'
                <?php
                self
                    ::example()

                    ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $seconds = $minutes
                    * 60; // seconds in a minute
                EOD,
            <<<'EOD'
                <?php
                $seconds = $minutes
                    * 60 // seconds in a minute
                ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $seconds = $minutes
                    * (int) '60'; // seconds in a minute
                EOD,
            <<<'EOD'
                <?php
                $seconds = $minutes
                    * (int) '60' // seconds in a minute
                ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $secondsPerMinute = 60;
                $seconds = $minutes
                    * $secondsPerMinute; // seconds in a minute
                EOD,
            <<<'EOD'
                <?php
                $secondsPerMinute = 60;
                $seconds = $minutes
                    * $secondsPerMinute // seconds in a minute
                ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $secondsPerMinute = 60;
                $seconds = $minutes
                    * 60 * (int) true; // seconds in a minute
                EOD,
            <<<'EOD'
                <?php
                $secondsPerMinute = 60;
                $seconds = $minutes
                    * 60 * (int) true // seconds in a minute
                ;
                EOD,
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesMultiLineWhitespaceCases
     */
    public function testMessyWhitespacesMultiLineWhitespace(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NO_MULTI_LINE]);
        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesMultiLineWhitespaceCases(): iterable
    {
        yield [
            '<?php echo(1); // test',
            "<?php echo(1) // test\r\n;",
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

    public static function provideSemicolonForChainedCallsFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php

                                    $this
                                        ->method1()
                                        ->method2()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $this
                                        ->method1()
                                        ->method2();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $this
                                        ->method1()
                                        ->method2() // comment
                                    ;



                EOD,
            <<<'EOD'
                <?php

                                    $this
                                        ->method1()
                                        ->method2(); // comment



                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $service->method1()
                                        ->method2()
                                    ;

                                    $service->method3();
                                    $this
                                        ->method1()
                                        ->method2()
                                    ;
                EOD,
            <<<'EOD'
                <?php

                                    $service->method1()
                                        ->method2()
                                    ;

                                    $service->method3();
                                    $this
                                        ->method1()
                                        ->method2();
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $service
                                        ->method2()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $service
                                        ->method2();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $service->method1()
                                        ->method2()
                                        ->method3()
                                        ->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $service->method1()
                                        ->method2()
                                        ->method3()
                                        ->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $this->service->method1()
                                        ->method2([1, 2])
                                        ->method3(
                                            "2",
                                            2,
                                            [1, 2]
                                        )
                                        ->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $this->service->method1()
                                        ->method2([1, 2])
                                        ->method3(
                                            "2",
                                            2,
                                            [1, 2]
                                        )
                                        ->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $service
                                        ->method1()
                                            ->method2()
                                        ->method3()
                                            ->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $service
                                        ->method1()
                                            ->method2()
                                        ->method3()
                                            ->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $f = "g";

                                    $service
                                        ->method1("a", true)
                                        ->method2(true, false)
                                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                                        ->method4(1, "a", $f)
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php
                                    $f = "g";

                                    $service
                                        ->method1("a", true)
                                        ->method2(true, false)
                                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                                        ->method4(1, "a", $f);
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $f = "g";

                                    $service
                                        ->method1("a", true) // this is a comment
                                        /* ->method2(true, false) */
                                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                                        ->method4(1, "a", $f) /* this is a comment */
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php
                                    $f = "g";

                                    $service
                                        ->method1("a", true) // this is a comment
                                        /* ->method2(true, false) */
                                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                                        ->method4(1, "a", $f); /* this is a comment */
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $service->method1();
                                    $service->method2()->method3();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $service->method1() ;
                                    $service->method2()->method3() ;
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $service
                                        ->method2(function ($a) {
                                            $a->otherCall()
                                                ->a()
                                                ->b()
                                            ;
                                        })
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $service
                                        ->method2(function ($a) {
                                            $a->otherCall()
                                                ->a()
                                                ->b()
                                            ;
                                        });
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

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
                                ?>
                EOD,
            <<<'EOD'
                <?php

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
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $service
                                        ->method1(null, null, [
                                            null => null,
                                            1 => $data->getId() > 0,
                                        ])
                                        ->method2(4, Type::class)
                                    ;

                EOD,
            <<<'EOD'
                <?php

                                    $service
                                        ->method1(null, null, [
                                            null => null,
                                            1 => $data->getId() > 0,
                                        ])
                                        ->method2(4, Type::class);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $this
                                        ->method1()
                                        ->method2()
                ;
                                ?>
                EOD,
            <<<'EOD'
                <?php
                $this
                                        ->method1()
                                        ->method2();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    self
                                        ::method1()
                                        ->method2()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    self
                                        ::method1()
                                        ->method2();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    self
                                        ::method1()
                                        ->method2() // comment
                                    ;



                EOD,
            <<<'EOD'
                <?php

                                    self
                                        ::method1()
                                        ->method2(); // comment



                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    Service::method1()
                                        ->method2()
                                    ;

                                    Service::method3();
                                    $this
                                        ->method1()
                                        ->method2()
                                    ;
                EOD,
            <<<'EOD'
                <?php

                                    Service::method1()
                                        ->method2()
                                    ;

                                    Service::method3();
                                    $this
                                        ->method1()
                                        ->method2();
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    Service
                                        ::method2()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    Service
                                        ::method2();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    Service::method1()
                                        ->method2()
                                        ->method3()
                                        ->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    Service::method1()
                                        ->method2()
                                        ->method3()
                                        ->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    self::method1()
                                        ->method2([1, 2])
                                        ->method3(
                                            "2",
                                            2,
                                            [1, 2]
                                        )
                                        ->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    self::method1()
                                        ->method2([1, 2])
                                        ->method3(
                                            "2",
                                            2,
                                            [1, 2]
                                        )
                                        ->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    Service
                                        ::method1()
                                            ->method2()
                                        ->method3()
                                            ->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    Service
                                        ::method1()
                                            ->method2()
                                        ->method3()
                                            ->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $f = "g";

                                    Service
                                        ::method1("a", true)
                                        ->method2(true, false)
                                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                                        ->method4(1, "a", $f)
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php
                                    $f = "g";

                                    Service
                                        ::method1("a", true)
                                        ->method2(true, false)
                                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                                        ->method4(1, "a", $f);
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $f = "g";

                                    Service
                                        ::method1("a", true) // this is a comment
                                        /* ->method2(true, false) */
                                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                                        ->method4(1, "a", $f) /* this is a comment */
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php
                                    $f = "g";

                                    Service
                                        ::method1("a", true) // this is a comment
                                        /* ->method2(true, false) */
                                        ->method3([1, 2, 3], ["a" => "b", "c" => 1, "d" => true])
                                        ->method4(1, "a", $f); /* this is a comment */
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    Service::method1();
                                    Service::method2()->method3();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    Service::method1() ;
                                    Service::method2()->method3() ;
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    Service
                                        ::method2(function ($a) {
                                            $a->otherCall()
                                                ->a()
                                                ->b()
                                            ;
                                        })
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    Service
                                        ::method2(function ($a) {
                                            $a->otherCall()
                                                ->a()
                                                ->b()
                                            ;
                                        });
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

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
                                ?>
                EOD,
            <<<'EOD'
                <?php

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
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    Service
                                        ::method1(null, null, [
                                            null => null,
                                            1 => $data->getId() > 0,
                                        ])
                                        ->method2(4, Type::class)
                                    ;

                EOD,
            <<<'EOD'
                <?php

                                    Service
                                        ::method1(null, null, [
                                            null => null,
                                            1 => $data->getId() > 0,
                                        ])
                                        ->method2(4, Type::class);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                Service
                                        ::method1()
                                        ->method2()
                ;
                                ?>
                EOD,
            <<<'EOD'
                <?php
                Service
                                        ::method1()
                                        ->method2();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

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
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    function foo($bar)
                                    {
                                        if ($bar === 1) {
                                            $baz
                                                ->bar();
                                        }

                                        return (new Foo($bar))
                                            ->baz();
                                    }
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $foo = (new Foo($bar))
                                        ->baz()
                                    ;

                                    function foo($bar)
                                    {
                                        $foo = (new Foo($bar))
                                            ->baz()
                                        ;
                                    }
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $foo = (new Foo($bar))
                                        ->baz();

                                    function foo($bar)
                                    {
                                        $foo = (new Foo($bar))
                                            ->baz();
                                    }
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $object
                    ->methodA()
                    ->methodB()
                ;

                EOD,
            <<<'EOD'
                <?php
                $object
                    ->methodA()
                    ->methodB();

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php $object
                    ->methodA()
                    ->methodB()
                ;

                EOD,
            <<<'EOD'
                <?php $object
                    ->methodA()
                    ->methodB();

                EOD,
        ];

        yield [
            "<?php\n\$this\n    ->one()\n    ->two(2, )\n;",
            "<?php\n\$this\n    ->one()\n    ->two(2, );",
        ];

        yield [
            "<?php\n\$this\n    ->one(1, )\n    ->two()\n;",
            "<?php\n\$this\n    ->one(1, )\n    ->two();",
        ];

        yield [
            <<<'EOD'
                <?php

                                    $foo->bar();

                                    Service::method1()
                                        ->method2()
                                        ->method3()->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $foo->bar()
                                    ;

                                    Service::method1()
                                        ->method2()
                                        ->method3()->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $foo->bar();

                                    \Service::method1()
                                        ->method2()
                                        ->method3()->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $foo->bar()
                                    ;

                                    \Service::method1()
                                        ->method2()
                                        ->method3()->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $foo->bar();

                                    Ns\Service::method1()
                                        ->method2()
                                        ->method3()->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $foo->bar()
                                    ;

                                    Ns\Service::method1()
                                        ->method2()
                                        ->method3()->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                    $foo->bar();

                                    \Ns\Service::method1()
                                        ->method2()
                                        ->method3()->method4()
                                    ;
                                ?>
                EOD,
            <<<'EOD'
                <?php

                                    $foo->bar()
                                    ;

                                    \Ns\Service::method1()
                                        ->method2()
                                        ->method3()->method4();
                                ?>
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $this
                    ->setName('readme2')
                    ->setDescription('Generates the README')
                ;

                EOD,
            <<<'EOD'
                <?php
                $this
                    ->setName('readme2')
                    ->setDescription('Generates the README')
                    ;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $this
                    ->foo()
                    ->{$bar ? 'bar' : 'baz'}()
                ;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    foo("bar")
                                        ->method1()
                                        ->method2()
                                    ;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    foo("bar")
                                        ->method1()
                                        ->method2();
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    $result = $arrayOfAwesomeObjects["most awesome object"]
                                        ->method1()
                                        ->method2()
                                    ;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $result = $arrayOfAwesomeObjects["most awesome object"]
                                        ->method1()
                                        ->method2();
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    $foo;
                                    $bar = [
                                        1 => 2,
                                        3 => $baz->method(),
                                    ];
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 1:
                        $bar
                            ->baz()
                        ;
                }

                EOD,
            <<<'EOD'
                <?php
                switch ($foo) {
                    case 1:
                        $bar
                            ->baz()
                              ;
                }

                EOD,
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesSemicolonForChainedCallsCases
     */
    public function testMessyWhitespacesSemicolonForChainedCalls(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]);
        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesSemicolonForChainedCallsCases(): iterable
    {
        yield [
            "<?php\r\n\r\n   \$this\r\n\t->method1()\r\n\t\t->method2()\r\n   ;",
            "<?php\r\n\r\n   \$this\r\n\t->method1()\r\n\t\t->method2();",
        ];

        yield [
            "<?php\r\n\r\n\t\$this->method1()\r\n\t\t->method2()\r\n\t\t->method(3)\r\n\t;",
            "<?php\r\n\r\n\t\$this->method1()\r\n\t\t->method2()\r\n\t\t->method(3);",
        ];

        yield [
            "<?php\r\n\r\n\t\$data   =  \$service\r\n\t ->method2(function (\$a) {\r\n\t\t\t\$a->otherCall()\r\n\t\t\t\t->a()\r\n\t\t\t\t->b(array_merge([\r\n\t\t\t\t\t\t1 => 1,\r\n\t\t\t\t\t\t2 => 2,\r\n\t\t\t\t\t], \$this->getOtherArray()\r\n\t\t\t\t))\r\n\t\t\t;\r\n\t\t})\r\n\t;\r\n?>",
            "<?php\r\n\r\n\t\$data   =  \$service\r\n\t ->method2(function (\$a) {\r\n\t\t\t\$a->otherCall()\r\n\t\t\t\t->a()\r\n\t\t\t\t->b(array_merge([\r\n\t\t\t\t\t\t1 => 1,\r\n\t\t\t\t\t\t2 => 2,\r\n\t\t\t\t\t], \$this->getOtherArray()\r\n\t\t\t\t));\r\n\t\t});\r\n?>",
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testFix80(): void
    {
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]);
        $this->doTest(
            <<<'EOD'
                <?php

                                $foo?->method1()
                                    ?->method2()
                                    ?->method3()
                                ;
                EOD."\n                ",
            <<<'EOD'
                <?php

                                $foo?->method1()
                                    ?->method2()
                                    ?->method3();
                EOD."\n                "
        );
    }
}
