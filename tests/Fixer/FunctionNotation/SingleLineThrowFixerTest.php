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
 * @covers \PhpCsFixer\Fixer\FunctionNotation\SingleLineThrowFixer
 */
final class SingleLineThrowFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [<<<'EOD'
            <?php throw new Exception; foo(
                                "Foo"
                            );
            EOD];

        yield [<<<'EOD'
            <?php throw new $exceptionName; foo(
                                "Foo"
                            );
            EOD];

        yield [<<<'EOD'
            <?php throw $exception; foo(
                                "Foo"
                            );
            EOD];

        yield ['<?php throw new Exception("Foo.", 0);'];

        yield [
            '<?php throw new Exception("Foo.", 0);',
            <<<'EOD'
                <?php throw new Exception(
                                "Foo.",
                                0
                            );
                EOD,
        ];

        yield [
            '<?php throw new Exception("Foo." . "Bar");',
            <<<'EOD'
                <?php throw new Exception(
                                "Foo."
                                .
                                "Bar"
                            );
                EOD,
        ];

        yield [
            '<?php throw new Exception(new ExceptionReport("Foo"), 0);',
            <<<'EOD'
                <?php throw new Exception(
                                new
                                    ExceptionReport("Foo"),
                                0
                            );
                EOD,
        ];

        yield [
            '<?php throw new Exception(sprintf(\'Error with number "%s".\', 42));',
            <<<'EOD'
                <?php throw new Exception(sprintf(
                                'Error with number "%s".',
                                42
                            ));
                EOD,
        ];

        yield [
            '<?php throw new SomeVendor\\Exception("Foo.");',
            <<<'EOD'
                <?php throw new SomeVendor\Exception(
                                "Foo."
                            );
                EOD,
        ];

        yield [
            '<?php throw new \SomeVendor\\Exception("Foo.");',
            <<<'EOD'
                <?php throw new \SomeVendor\Exception(
                                "Foo."
                            );
                EOD,
        ];

        yield [
            '<?php throw $this->exceptionFactory->createAnException("Foo");',
            <<<'EOD'
                <?php throw $this
                                ->exceptionFactory
                                ->createAnException(
                                    "Foo"
                                );
                EOD,
        ];

        yield [
            '<?php throw $this->getExceptionFactory()->createAnException("Foo");',
            <<<'EOD'
                <?php throw $this
                                ->getExceptionFactory()
                                ->createAnException(
                                    "Foo"
                                );
                EOD,
        ];

        yield [
            '<?php throw $this->getExceptionFactory()->createAnException(function ($x, $y) { return $x === $y + 2; });',
            <<<'EOD'
                <?php throw $this
                                ->getExceptionFactory()
                                ->createAnException(
                                    function
                                    (
                                        $x,
                                        $y
                                    )
                                    {
                                        return $x === $y + 2
                                        ;
                                    }
                                );
                EOD,
        ];

        yield [
            '<?php throw ExceptionFactory::createAnException("Foo");',
            <<<'EOD'
                <?php throw ExceptionFactory
                                    ::
                                    createAnException(
                                        "Foo"
                                    );
                EOD,
        ];

        yield [
            '<?php throw new Exception("Foo.", 0);',
            <<<'EOD'
                <?php throw
                                new
                                    Exception
                                        (
                                            "Foo."
                                                ,
                                            0
                                        );
                EOD,
        ];

        yield [
            '<?php throw new $exceptionName("Foo.");',
            <<<'EOD'
                <?php throw new $exceptionName(
                                "Foo."
                            );
                EOD,
        ];

        yield [
            '<?php throw new $exceptions[4];',
            <<<'EOD'
                <?php throw new $exceptions[
                                4
                            ];
                EOD,
        ];

        yield [
            '<?php throw clone $exceptionName("Foo.");',
            <<<'EOD'
                <?php throw clone $exceptionName(
                                "Foo."
                            );
                EOD,
        ];

        yield [
            '<?php throw new WeirdException("Foo.", -20, "An elephant", 1, 2, 3, 4, 5, 6, 7, 8);',
            <<<'EOD'
                <?php throw new WeirdException("Foo.", -20, "An elephant",

                                1,
                        2,
                                                    3, 4, 5, 6, 7, 8
                            );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    throw new Exception("It is foo.", 1);
                                } else {
                                    throw new \Exception("It is not foo.", 0);
                                }
                EOD."\n            ",
            <<<'EOD'
                <?php
                                if ($foo) {
                                    throw new Exception(
                                        "It is foo.",
                                        1
                                    );
                                } else {
                                    throw new \Exception(
                                        "It is not foo.", 0
                                    );
                                }
                EOD."\n            ",
        ];

        yield [
            '<?php throw new Exception( /* A */"Foo", /* 1 */0 /* 2 */); //3',
            <<<'EOD'
                <?php throw new Exception( // A
                                "Foo", // 1
                                0 // 2
                            ); //3
                EOD,
        ];

        yield [
            '<?php throw new Exception( /* 0123 */ "Foo", /* 1 */0 /* 2 */); //3',
            <<<'EOD'
                <?php throw new Exception( /* 0123 */
                                "Foo", // 1
                                0 // 2
                            ); //3
                EOD,
        ];

        yield [
            '<?php throw new Exception( /* X  */ "Foo", /* 1 */0 /* 2 */); //3',
            <<<'EOD'
                <?php throw new Exception( /* X
                 */
                                "Foo", // 1
                                0 // 2
                            ); //3
                EOD,
        ];

        yield [
            '<?php throw new Exception(  0, /* 1 2 3 */ /*4*/              "Foo", /*5*/ /*6*/0 /*7*/);',
            <<<'EOD'
                <?php throw new Exception(  0, /*
                1
                2
                3
                */
                  /*4*/              "Foo", /*5*/
                /*6*/0 /*7*/);
                EOD,
        ];

        yield [
            '<?php throw new Exception( /* 0 */${"Foo" /* a */}, /*b */fnx/*c */(/*d */0/*e */)/*f */);',
            <<<'EOD'
                <?php throw new Exception( // 0
                    ${"Foo"
                    # a
                    }, #b
                    fnx#c
                    (#d
                    0#e
                    )#f
                );
                EOD,
        ];

        yield [
            "<?php throw new Exception('Message.'. 1);",
            <<<'EOD'
                <?php throw new Exception('Message.'.
                1
                );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php throw new class() extends Exception
                            {
                                protected $message = "Custom message";
                            }
                        ;
                EOD,
            <<<'EOD'
                <?php throw
                            new class()
                            extends Exception
                            {
                                protected $message = "Custom message";
                            }
                        ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php throw new class extends Exception
                            {
                                protected $message = "Custom message";
                            }
                        ;
                EOD,
            <<<'EOD'
                <?php throw
                            new class
                            extends Exception
                            {
                                protected $message = "Custom message";
                            }
                        ;
                EOD,
        ];

        yield [
            '<?php throw new Exception("Foo.", 0)?>',
            <<<'EOD'
                <?php throw new Exception(
                                "Foo.",
                                0
                            )?>
                EOD,
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php throw $this?->getExceptionFactory()?->createAnException("Foo");',
            <<<'EOD'
                <?php throw $this
                                    ?->getExceptionFactory()
                                    ?->createAnException(
                                    "Foo"
                                );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                match ($number) {
                                    1 => $function->one(),
                                    2 => $function->two(),
                                    default => throw new \NotOneOrTwo()
                                };
                EOD."\n            ",
        ];

        yield [
            <<<'EOD'
                <?php
                                match ($number) {
                                    1 => $function->one(),
                                    2 => throw new Exception("Number 2 is not allowed."),
                                    1 => $function->three(),
                                    default => throw new \NotOneOrTwo()
                                };
                EOD."\n            ",
        ];

        yield [
            '<?php throw new Exception(match ($a) { 1 => "a", 3 => "b" });',
            <<<'EOD'
                <?php throw new Exception(match ($a) {
                                1 => "a",
                                3 => "b"
                            });
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = [
                    $something[1] ?? throw new Exception(123)
                ];

                EOD,
            <<<'EOD'
                <?php
                $var = [
                    $something[1] ?? throw new Exception(

                    123

                    )
                ];

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $var = [
                    $something[1] ?? throw new Exception()
                ];

                EOD,
        ];
    }
}
