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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author John Kelly <wablam@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMultiLineWhitespaceFixCases
     */
    public function testFixMultiLineWhitespace($expected, $input = null)
    {
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NO_MULTI_LINE]);
        $this->doTest($expected, $input);
    }

    public function provideMultiLineWhitespaceFixCases()
    {
        return [
            [
                '<?php
                    $foo->bar() // test
;',
                '<?php
                    $foo->bar() // test
                    ;',
            ],
            [
                "<?php echo(1) // test\n;",
            ],
            [
                '<?php
                    $foo->bar() # test
;',
                '<?php
                    $foo->bar() # test


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
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesMultiLineWhitespaceFixCases
     */
    public function testMessyWhitespacesMultiLineWhitespace($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NO_MULTI_LINE]);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesMultiLineWhitespaceFixCases()
    {
        return [
            [
                "<?php echo(1) // test\r\n;",
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideSemicolonForChainedCallsFixCases
     */
    public function testSemicolonForChainedCallsFix($expected, $input = null)
    {
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]);
        $this->doTest($expected, $input);
    }

    public function provideSemicolonForChainedCallsFixCases()
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
            ], [
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
            ], [
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
            ], [
                '<?php

                    $service
                        ->method2()
                    ;
                ?>',
                '<?php

                    $service
                        ->method2();
                ?>',
            ], [
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
            ], [
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
            ], [
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
            ], [
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
            ], [
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
            ], [
                '<?php
                    $service->method1();
                    $service->method2()->method3();
                ?>',
            ], [
                '<?php
                    $service->method1() ;
                    $service->method2()->method3() ;
                ?>',
            ], [
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
            ], [
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
            ], [
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
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesSemicolonForChainedCallsFixCases
     */
    public function testMessyWhitespacesSemicolonForChainedCalls($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure(['strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesSemicolonForChainedCallsFixCases()
    {
        return [
            [
                "<?php\r\n\r\n   \$this\r\n\t->method1()\r\n\t\t->method2()\r\n   ;",
                "<?php\r\n\r\n   \$this\r\n\t->method1()\r\n\t\t->method2();",
            ],
            [
                "<?php\r\n\r\n\t\$this->method1()\r\n\t\t->method2()\r\n\t\t->method(3)\r\n\t;",
                "<?php\r\n\r\n\t\$this->method1()\r\n\t\t->method2()\r\n\t\t->method(3);",
            ], [
                "<?php\r\n\r\n\t\$data   =  \$service\r\n\t ->method2(function (\$a) {\r\n\t\t\t\$a->otherCall()\r\n\t\t\t\t->a()\r\n\t\t\t\t->b(array_merge([\r\n\t\t\t\t\t\t1 => 1,\r\n\t\t\t\t\t\t2 => 2,\r\n\t\t\t\t\t], \$this->getOtherArray()\r\n\t\t\t\t))\r\n\t\t\t;\r\n\t\t})\r\n\t;\r\n?>",
                "<?php\r\n\r\n\t\$data   =  \$service\r\n\t ->method2(function (\$a) {\r\n\t\t\t\$a->otherCall()\r\n\t\t\t\t->a()\r\n\t\t\t\t->b(array_merge([\r\n\t\t\t\t\t\t1 => 1,\r\n\t\t\t\t\t\t2 => 2,\r\n\t\t\t\t\t], \$this->getOtherArray()\r\n\t\t\t\t));\r\n\t\t});\r\n?>",
            ],
        ];
    }
}
