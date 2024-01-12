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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer
 */
final class NativeFunctionCasingFixerTest extends AbstractFixerTestCase
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
                                namespace Bar {
                                    function STRLEN($str) {
                                        return "overridden" . \strlen($str);
                                    }
                                }

                                namespace {
                                    echo \Bar\STRLEN("xxx");
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    echo strtolower("hello 1");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo STRTOLOWER("hello 1");
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    echo strtolower //a
                                        ("hello 2");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo STRTOLOWER //a
                                        ("hello 2");
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    echo strtolower /**/   ("hello 3");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo STRTOLOWER /**/   ("hello 3");
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    echo \sqrt(4);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo \sQrT(4);
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    echo "1".\sqrt("hello 5");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo "1".\SQRT("hello 5");
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    class Test{
                                        public function gettypE()
                                        {
                                            return 1;
                                        }

                                        function sqrT($a)
                                        {
                                        }

                                        function &END($a)
                                        {
                                        }
                                    }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    new STRTOLOWER();
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    new \STRTOLOWER();
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    new \A\B\STRTOLOWER();
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    a::STRTOLOWER();
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    $a->STRTOLOWER();
                EOD."\n                ",
        ];

        yield [
            '<?php fOoO();',
        ];

        yield [
            <<<'EOD'
                <?php
                                    namespace Foo {
                                        function &Next() {
                                            return prev(-1);
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $next1 = & next($array1);
                                    $next2 = & \next($array2);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $next1 = & Next($array1);
                                    $next2 = & \Next($array2);
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    namespace Foo;
                                    use function MyStuff\StrToLower;
                                    class Bar {
                                        public function getName() {
                                            return StrToLower($this->name);
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    echo \sqrt(4 , );
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    echo \sQrT(4 , );
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    $a->STRTOLOWER(1,);
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideFix80Cases(): iterable
    {
        yield ['<?php $a?->STRTOLOWER(1,);'];

        yield [
            <<<'EOD'
                <?php
                                    final class SomeClass
                            {
                                #[File(mimeTypes: ["application/pdf", "image/*"])]
                                public FileBlob $attachment;
                            }
                EOD."\n            ",
        ];
    }
}
