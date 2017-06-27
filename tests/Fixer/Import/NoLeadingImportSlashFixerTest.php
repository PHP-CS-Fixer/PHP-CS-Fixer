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

use PhpCsFixer\Test\AbstractFixerTestCase;

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
}
