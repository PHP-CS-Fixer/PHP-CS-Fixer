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
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\FunctionNotation\SingleLineThrowFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php throw new Exception; foo(
                    "Foo"
                );'];

        yield ['<?php throw new $exceptionName; foo(
                    "Foo"
                );'];

        yield ['<?php throw $exception; foo(
                    "Foo"
                );'];

        yield ['<?php throw new Exception("Foo.", 0);'];

        yield [
            '<?php throw new Exception("Foo.", 0);',
            '<?php throw new Exception(
                "Foo.",
                0
            );',
        ];

        yield [
            '<?php throw new Exception("Foo." . "Bar");',
            '<?php throw new Exception(
                "Foo."
                .
                "Bar"
            );',
        ];

        yield [
            '<?php throw new Exception(new ExceptionReport("Foo"), 0);',
            '<?php throw new Exception(
                new
                    ExceptionReport("Foo"),
                0
            );',
        ];

        yield [
            '<?php throw new Exception(sprintf(\'Error with number "%s".\', 42));',
            '<?php throw new Exception(sprintf(
                \'Error with number "%s".\',
                42
            ));',
        ];

        yield [
            '<?php throw new SomeVendor\Exception("Foo.");',
            '<?php throw new SomeVendor\Exception(
                "Foo."
            );',
        ];

        yield [
            '<?php throw new \SomeVendor\Exception("Foo.");',
            '<?php throw new \SomeVendor\Exception(
                "Foo."
            );',
        ];

        yield [
            '<?php throw $this->exceptionFactory->createAnException("Foo");',
            '<?php throw $this
                ->exceptionFactory
                ->createAnException(
                    "Foo"
                );',
        ];

        yield [
            '<?php throw $this->getExceptionFactory()->createAnException("Foo");',
            '<?php throw $this
                ->getExceptionFactory()
                ->createAnException(
                    "Foo"
                );',
        ];

        yield [
            '<?php throw $this->getExceptionFactory()->createAnException(function ($x, $y) { return $x === $y + 2; });',
            '<?php throw $this
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
                );',
        ];

        yield [
            '<?php throw ExceptionFactory::createAnException("Foo");',
            '<?php throw ExceptionFactory
                    ::
                    createAnException(
                        "Foo"
                    );',
        ];

        yield [
            '<?php throw new Exception("Foo.", 0);',
            '<?php throw
                new
                    Exception
                        (
                            "Foo."
                                ,
                            0
                        );',
        ];

        yield [
            '<?php throw new $exceptionName("Foo.");',
            '<?php throw new $exceptionName(
                "Foo."
            );',
        ];

        yield [
            '<?php throw new $exceptions[4];',
            '<?php throw new $exceptions[
                4
            ];',
        ];

        yield [
            '<?php throw clone $exceptionName("Foo.");',
            '<?php throw clone $exceptionName(
                "Foo."
            );',
        ];

        yield [
            '<?php throw new WeirdException("Foo.", -20, "An elephant", 1, 2, 3, 4, 5, 6, 7, 8);',
            '<?php throw new WeirdException("Foo.", -20, "An elephant",

                1,
        2,
                                    3, 4, 5, 6, 7, 8
            );',
        ];

        yield [
            '<?php
                if ($foo) {
                    throw new Exception("It is foo.", 1);
                } else {
                    throw new \Exception("It is not foo.", 0);
                }
            ',
            '<?php
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
            ',
        ];

        yield [
            '<?php throw new Exception( /* A */"Foo", /* 1 */0 /* 2 */); //3',
            '<?php throw new Exception( // A
                "Foo", // 1
                0 // 2
            ); //3',
        ];

        yield [
            '<?php throw new Exception( /* 0123 */ "Foo", /* 1 */0 /* 2 */); //3',
            '<?php throw new Exception( /* 0123 */
                "Foo", // 1
                0 // 2
            ); //3',
        ];

        yield [
            '<?php throw new Exception( /* X  */ "Foo", /* 1 */0 /* 2 */); //3',
            '<?php throw new Exception( /* X
 */
                "Foo", // 1
                0 // 2
            ); //3',
        ];

        yield [
            '<?php throw new Exception(  0, /* 1 2 3 */ /*4*/              "Foo", /*5*/ /*6*/0 /*7*/);',
            '<?php throw new Exception(  0, /*
1
2
3
*/
  /*4*/              "Foo", /*5*/
/*6*/0 /*7*/);',
        ];

        yield [
            '<?php throw new Exception( /* 0 */${"Foo" /* a */}, /*b */fnx/*c */(/*d */0/*e */)/*f */);',
            '<?php throw new Exception( // 0
    ${"Foo"
    # a
    }, #b
    fnx#c
    (#d
    0#e
    )#f
);',
        ];

        yield [
            "<?php throw new Exception('Message.'. 1);",
            "<?php throw new Exception('Message.'.
1
);",
        ];

        yield [
            '<?php throw new class() extends Exception
            {
                protected $message = "Custom message";
            }
        ;',
            '<?php throw
            new class()
            extends Exception
            {
                protected $message = "Custom message";
            }
        ;',
        ];

        yield [
            '<?php throw new class extends Exception
            {
                protected $message = "Custom message";
            }
        ;',
            '<?php throw
            new class
            extends Exception
            {
                protected $message = "Custom message";
            }
        ;',
        ];

        yield [
            '<?php throw new Exception("Foo.", 0)?>',
            '<?php throw new Exception(
                "Foo.",
                0
            )?>',
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

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php throw $this?->getExceptionFactory()?->createAnException("Foo");',
            '<?php throw $this
                    ?->getExceptionFactory()
                    ?->createAnException(
                    "Foo"
                );',
        ];

        yield [
            '<?php
                match ($number) {
                    1 => $function->one(),
                    2 => $function->two(),
                    default => throw new \NotOneOrTwo()
                };
            ',
        ];

        yield [
            '<?php
                match ($number) {
                    1 => $function->one(),
                    2 => throw new Exception("Number 2 is not allowed."),
                    1 => $function->three(),
                    default => throw new \NotOneOrTwo()
                };
            ',
        ];

        yield [
            '<?php throw new Exception(match ($a) { 1 => "a", 3 => "b" });',
            '<?php throw new Exception(match ($a) {
                1 => "a",
                3 => "b"
            });',
        ];

        yield [
            '<?php
$var = [
    $something[1] ?? throw new Exception(123)
];
',
            '<?php
$var = [
    $something[1] ?? throw new Exception(

    123

    )
];
',
        ];

        yield [
            '<?php
$var = [
    $something[1] ?? throw new Exception()
];
',
        ];
    }
}
