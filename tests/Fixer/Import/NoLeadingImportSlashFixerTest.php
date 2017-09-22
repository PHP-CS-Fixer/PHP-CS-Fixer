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
                use A\B;
                ',
                '<?php
                use \A\B;
                ',
            ],
            [
                '<?php
                use/*1*/A\B;
                ',
                '<?php
                use/*1*/\A\B;
                ',
            ],
            [
                '<?php
                $a = function(\B\C $a) use ($b){

                };
                ',
            ],
            [
                '<?php
                namespace NS;
                use A\B;
                ',
                '<?php
                namespace NS;
                use \A\B;
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                trait SomeTrait {
                    use \A;
                }
                ',
            ],
            [
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
            ],
            [
                '<?php
                trait Foo {}
                class Bar {
                    use \Foo;
                }
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix56Cases
     * @requires PHP 5.6
     */
    public function testFix56($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix56Cases()
    {
        return [
            [
                '<?php
                    use function a\b;
                    use const d\e;
                ',
                '<?php
                    use function \a\b;
                    use const \d\e;
                ',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFix72Cases
     * @requires PHP 7.2
     */
    public function testFix72($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix72Cases()
    {
        return [
            [
                '<?php
namespace AAA;
use some\a\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\a\{ConstA,ConstB,ConstC
,
};
use const some\Z\{ConstA,ConstB,ConstC,};
',
                '<?php
namespace AAA;
use \some\a\{ClassA, ClassB, ClassC as C,};
use function \some\a\{fn_a, fn_b, fn_c,};
use const \some\a\{ConstA,ConstB,ConstC
,
};
use const \some\Z\{ConstA,ConstB,ConstC,};
',
            ],
        ];
    }
}
