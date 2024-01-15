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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Ceeram <ceeram@cakephp.org>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer
 */
final class SingleLineAfterImportsFixerTest extends AbstractFixerTestCase
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
        yield [
            <<<'EOD'
                <?php
                use D;
                use E;
                use DP;   /**/
                use EZ; //
                use DAZ;
                use EGGGG; /**/
                use A\B;

                use C\DE;


                use E\F;



                use G\H;


                EOD,
            <<<'EOD'
                <?php
                use D;         use E;
                use DP;   /**/      use EZ; //
                use DAZ;         use EGGGG; /**/
                use A\B;

                use C\DE;


                use E\F;



                use G\H;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php use \Exception;

                ?>
                <?php
                $a = new Exception();

                EOD,
            <<<'EOD'
                <?php use \Exception?>
                <?php
                $a = new Exception();

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php use \stdClass;
                use \DateTime;

                ?>
                <?php
                $a = new DateTime();

                EOD,
            <<<'EOD'
                <?php use \stdClass; use \DateTime?>
                <?php
                $a = new DateTime();

                EOD,
        ];

        yield [
            '<?php namespace Foo;'."\n              ".<<<'EOD'

                use Bar\Baz;

                /**
                 * Foo.
                 */
                EOD,
            '<?php namespace Foo;'."\n              ".<<<'EOD'

                use Bar\Baz;
                /**
                 * Foo.
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;

                use D;

                class C {}

                EOD,
            <<<'EOD'
                <?php
                namespace A\B;

                use D;
                class C {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    namespace A\B;

                    use D;

                    class C {}

                EOD,
            <<<'EOD'
                <?php
                    namespace A\B;

                    use D;
                    class C {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;

                use D;
                use E;

                class C {}

                EOD,
            <<<'EOD'
                <?php
                namespace A\B;

                use D;
                use E;
                class C {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;

                use D;

                class C {}

                EOD,
            <<<'EOD'
                <?php
                namespace A\B;

                use D; class C {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;
                use D;
                use E;

                {
                    class C {}
                }
                EOD,
            <<<'EOD'
                <?php
                namespace A\B;
                use D; use E; {
                    class C {}
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;
                use D;
                use E;

                {
                    class C {}
                }
                EOD,
            <<<'EOD'
                <?php
                namespace A\B;
                use D;
                use E; {
                    class C {}
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B {
                    use D;
                    use E;

                    class C {}
                }
                EOD,
            <<<'EOD'
                <?php
                namespace A\B {
                    use D; use E; class C {}
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;
                class C {
                    use SomeTrait;
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $lambda = function () use (
                    $arg
                ){
                    return true;
                };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A\B;
                use D, E;

                class C {

                }
                EOD,
            <<<'EOD'
                <?php
                namespace A\B;
                use D, E;
                class C {

                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    namespace A1;
                    use B1; // need to import this !
                    use B2;

                    class C1 {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    namespace A2;
                    use B2;// need to import this !
                    use B3;

                    class C4 {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A1;
                use B1; // need to import this !
                use B2;

                class C1 {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A1;
                use B1;// need to import this !
                use B2;

                class C1 {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A1;
                use B1; /** need to import this !*/
                use B2;

                class C1 {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace A1;
                use B1;# need to import this !
                use B2;

                class C1 {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Foo;

                use Bar;
                use Baz;

                class Hello {}

                EOD,
            <<<'EOD'
                <?php
                namespace Foo;

                use Bar;
                use Baz;


                class Hello {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class HelloTrait {
                    use SomeTrait;

                    use Another;// ensure use statements for traits are not touched
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Foo {}
                namespace Bar {
                    class Baz
                    {
                        use Aaa;
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php use A\B;

                ?>
                EOD,
            '<?php use A\B?>',
        ];

        yield [
            <<<'EOD'
                <?php use A\B;


                EOD,
            '<?php use A\B;',
        ];

        yield [
            str_replace("\n", "\r\n", <<<'EOD'
                <?php
                use Foo;
                use Bar;

                class Baz {}

                EOD),
        ];

        yield [
            <<<'EOD'
                <?php
                use some\test\{ClassA, ClassB, ClassC as C};

                ?>
                test 123

                EOD,
            <<<'EOD'
                <?php
                use some\test\{ClassA, ClassB, ClassC as C}         ?>
                test 123

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                use some\test\{CA, Cl, ClassC as C};

                class Test {}

                EOD,
            <<<'EOD'
                <?php
                use some\test\{CA, Cl, ClassC as C};
                class Test {}

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                use function some\test\{fn_g, fn_f, fn_e};

                fn_a();
                EOD,
            <<<'EOD'
                <?php
                use function some\test\{fn_g, fn_f, fn_e};
                fn_a();
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                use const some\test\{ConstA, ConstB, ConstD};


                EOD,
            <<<'EOD'
                <?php
                use const some\test\{ConstA, ConstB, ConstD};

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Z\B;
                use const some\test\{ConstA, ConstB, ConstC};
                use A\B\C;


                EOD,
            <<<'EOD'
                <?php
                namespace Z\B;
                use const some\test\{ConstA, ConstB, ConstC};
                use A\B\C;

                EOD,
        ];

        yield [
            <<<'EOD'
                 <?php
                use some\a\ClassA;
                use function some\a\fn_a;
                use const some\c;


                EOD,
            <<<'EOD'
                 <?php
                use some\a\ClassA; use function some\a\fn_a; use const some\c;

                EOD,
        ];

        yield [
            "<?php use some\\a\\{ClassA,};\n\n",
            '<?php use some\a\{ClassA,};',
        ];

        yield [
            "<?php use some\\a\\{ClassA};\nuse some\\b\\{ClassB};\n\n",
            '<?php use some\a\{ClassA};use some\b\{ClassB};',
        ];

        yield [
            "<?php use some\\a\\{ClassA};\nuse const some\\b\\{ClassB};\n\n",
            '<?php use some\a\{ClassA};use const some\b\{ClassB};',
        ];

        yield [
            "<?php use some\\a\\{ClassA, ClassZ};\nuse const some\\b\\{ClassB, ClassX};\nuse function some\\d;\n\n",
            '<?php use some\a\{ClassA, ClassZ};use const some\b\{ClassB, ClassX};use function some\\d;',
        ];

        $imports = [
            'some\a\{ClassA, ClassB, ClassC as C,};',
            'function some\a\{fn_a, fn_b, fn_c,};',
            'const some\a\{ConstA,ConstB,ConstC,};',
            'const some\Z\{ConstX,ConstY,ConstZ,};',
        ];

        yield 'group types with trailing comma' => [
            "<?php\nuse ".implode("\nuse ", $imports)."\n\necho 1;",
            "<?php\nuse ".implode('use ', $imports).' echo 1;',
        ];

        foreach ($imports as $import) {
            $case = [
                "<?php\nuse ".$import."\n\necho 1;",
                "<?php\nuse ".$import.' echo 1;',
            ];

            yield [
                str_replace('some', '\\some', $case[0]),
                str_replace('some', '\\some', $case[1]),
            ];

            yield $case;
        }
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php namespace A\\B;\r\n    use D;\r\n\r\n    class C {}",
            "<?php namespace A\\B;\r\n    use D;\r\n\r\n\r\n    class C {}",
        ];

        yield [
            "<?php namespace A\\B;\r\n    use D;\r\n\r\n    class C {}",
            "<?php namespace A\\B;\r\n    use D;\r\n    class C {}",
        ];
    }
}
