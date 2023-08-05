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

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer
 */
final class NoLeadingImportSlashFixerTest extends AbstractFixerTestCase
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
            '<?php
                use A\B;
                ',
            '<?php
                use \A\B;
                ',
        ];

        yield [
            '<?php
                use/*1*/A\C;
                ',
            '<?php
                use/*1*/\A\C;
                ',
        ];

        yield [
            '<?php
                $a = function(\B\C $a) use ($b){

                };
                ',
        ];

        yield [
            '<?php
                namespace NS;
                use A\B;
                ',
            '<?php
                namespace NS;
                use \A\B;
                ',
        ];

        yield [
            '<?php
                namespace NS{
                    use A\B;
                }
                namespace NS2{
                    use C\D;
                }
                ',
            '<?php
                namespace NS{
                    use \A\B;
                }
                namespace NS2{
                    use \C\D;
                }
                ',
        ];

        yield [
            '<?php
                namespace Foo {
                    use A;
                    use A\X;

                    new X();
                }

                namespace Bar {
                    use B;
                    use B\X;

                    new X();
                }
                ',
            '<?php
                namespace Foo {
                    use \A;
                    use \A\X;

                    new X();
                }

                namespace Bar {
                    use \B;
                    use \B\X;

                    new X();
                }
                ',
        ];

        yield [
            '<?php
                namespace Foo\Bar;
                use Baz;
                class Foo implements Baz {}
                ',
            '<?php
                namespace Foo\Bar;
                use \Baz;
                class Foo implements Baz {}
                ',
        ];

        yield [
            '<?php
                trait SomeTrait {
                    use \A;
                }
                ',
        ];

        yield [
            '<?php
                namespace NS{
                    use A\B;
                    trait Tr8A{
                        use \B, \C;
                    }
                }
                namespace NS2{
                    use C\D;
                }
                ',
            '<?php
                namespace NS{
                    use \A\B;
                    trait Tr8A{
                        use \B, \C;
                    }
                }
                namespace NS2{
                    use \C\D;
                }
                ',
        ];

        yield [
            '<?php
                trait Foo {}
                class Bar {
                    use \Foo;
                }
                ',
        ];

        yield [
            '<?php
                    use function a\b;
                    use const d\e;
                ',
            '<?php
                    use function \a\b;
                    use const \d\e;
                ',
        ];

        yield [
            '<?php
namespace AAA;
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA,ConstB,ConstC
,
};
use const some\Z\{ConstX,ConstY,ConstZ,};
',
            '<?php
namespace AAA;
use \some\a\{ClassA, ClassB, ClassC as C,};
use function \some\a\{fn_a, fn_b, fn_c,};
use const \some\a\{ConstA,ConstB,ConstC
,
};
use const \some\Z\{ConstX,ConstY,ConstZ,};
',
        ];
    }

    /**
     * @dataProvider provideFixPrePHP80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPrePHP80Cases(): iterable
    {
        yield [
            '<?php use /*1*/A\D;',
            '<?php use\/*1*/A\D;',
        ];

        yield 'no space case' => [
            '<?php
                use Events\Payment\Base as PaymentEvent;
                use const d\e;
            ',
            '<?php
                use\Events\Payment\Base as PaymentEvent;
                use const\d\e;
            ',
        ];

        yield [
            '<?php
            use C;
            use C\X;

            namespace Foo {
                use A;
                use A\X;

                new X();
            }

            namespace Bar {
                use B;
                use B\X;

                new X();
            }
            ',
            '<?php
            use \C;
            use \C\X;

            namespace Foo {
                use \A;
                use \A\X;

                new X();
            }

            namespace Bar {
                use \B;
                use \B\X;

                new X();
            }
            ',
        ];
    }
}
