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

    public function provideFixCases(): array
    {
        return [
            [
                '<?php
                namespace Bar {
                    function STRLEN($str) {
                        return "overriden" . \strlen($str);
                    }
                }

                namespace {
                    echo \Bar\STRLEN("xxx");
                }',
            ],
            [
                '<?php
                    echo strtolower("hello 1");
                ',
                '<?php
                    echo STRTOLOWER("hello 1");
                ',
            ],
            [
                '<?php
                    echo strtolower //a
                        ("hello 2");
                ',
                '<?php
                    echo STRTOLOWER //a
                        ("hello 2");
                ',
            ],
            [
                '<?php
                    echo strtolower /**/   ("hello 3");
                ',
                '<?php
                    echo STRTOLOWER /**/   ("hello 3");
                ',
            ],
            [
                '<?php
                    echo \sqrt(4);
                ',
                '<?php
                    echo \sQrT(4);
                ',
            ],
            [
                '<?php
                    echo "1".\sqrt("hello 5");
                ',
                '<?php
                    echo "1".\SQRT("hello 5");
                ',
            ],
            [
                '<?php
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
                ',
            ],
            [
                '<?php
                    new STRTOLOWER();
                ',
            ],
            [
                '<?php
                    new \STRTOLOWER();
                ',
            ],
            [
                '<?php
                    new \A\B\STRTOLOWER();
                ',
            ],
            [
                '<?php
                    a::STRTOLOWER();
                ',
            ],
            [
                '<?php
                    $a->STRTOLOWER();
                ',
            ],
            [
                '<?php fOoO();',
            ],
            [
                '<?php
                    namespace Foo {
                        function &Next() {
                            return prev(-1);
                        }
                    }',
            ],
            [
                '<?php
                    $next1 = & next($array1);
                    $next2 = & \next($array2);
                ',
                '<?php
                    $next1 = & Next($array1);
                    $next2 = & \Next($array2);
                ',
            ],
            [
                '<?php
                    namespace Foo;
                    use function MyStuff\StrToLower;
                    class Bar {
                        public function getName() {
                            return StrToLower($this->name);
                        }
                    }',
            ],
            [
                '<?php
                    echo \sqrt(4 , );
                ',
                '<?php
                    echo \sQrT(4 , );
                ',
            ],
            [
                '<?php
                    $a->STRTOLOWER(1,);
                ',
            ],
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

    public function provideFix80Cases(): iterable
    {
        yield ['<?php $a?->STRTOLOWER(1,);'];

        yield [
            '<?php
                    final class SomeClass
            {
                #[File(mimeTypes: ["application/pdf", "image/*"])]
                public FileBlob $attachment;
            }
            ',
        ];
    }
}
