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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Egidijus Girčys <e.gircys@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\SemicolonOnNewLineForChainedCallFixer
 */
final class SemicolonOnNewLineForChainedCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
        ];
    }
}
