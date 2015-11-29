<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
class RemoveLeadingSlashUseFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                use \A\B;
                ',
            ),
            array(
                '<?php
                trait SomeTrait {
                    use \A;
                }
                ',
            ),
            array(
                '<?php
                $a = function(\B\C $a) use ($b){

                };
                ',
            ),
            array(
                '<?php
                namespace NS;
                use A\B;
                ',
                '<?php
                namespace NS;
                use \A\B;
                ',
            ),
            array(
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
            ),
            array(
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
            ),
            array(
                '<?php
                trait Foo {}
                class Bar {
                    use \Foo;
                }
                ',
            ),
            array(
                '<?php
                use \C;
                use \C\X;

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
            ),
            array(
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
            ),
        );
    }
}
