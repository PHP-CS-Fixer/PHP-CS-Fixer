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

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class ClassKeywordRemoveFixerTest extends AbstractFixerTestBase
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
                "<?php
                use Foo\Bar\Thing;

                echo 'Foo\Bar\Thing';
                ",
                "<?php
                use Foo\Bar\Thing;

                echo Thing::class;
                ",
            ),
            array(
                "<?php
                use Foo\Bar;
            
                echo 'Foo\Bar\Thing';
                ",
                "<?php
                use Foo\Bar;
            
                echo Bar\Thing::class;
                ",
            ),
            array(
                "<?php
                use Foo\Bar\Thing as Alias;

                echo 'Foo\Bar\Thing';
                ",
                "<?php
                use Foo\Bar\Thing as Alias;

                echo Alias::class;
                ",
            ),
            array(
                "<?php
                use Foo\Bar\Dummy;
                use Foo\Bar\Thing as Alias;

                echo 'Foo\Bar\Dummy';
                echo 'Foo\Bar\Thing';
                ",
                "<?php
                use Foo\Bar\Dummy;
                use Foo\Bar\Thing as Alias;

                echo Dummy::class;
                echo Alias::class;
                ",
            ),
            array(
                "<?php
                echo '\DateTime';
                ",
                "<?php
                echo \DateTime::class;
                ",
            ),
            array(
                "<?php
                echo 'Thing';
                ",
                '<?php
                echo Thing::class;
                ',
            ),
            array(
                "<?php
                class Foo {
                    public function amazingFunction() {
                        echo 'Thing';
                    }
                }
                ",
                '<?php
                class Foo {
                    public function amazingFunction() {
                        echo Thing::class;
                    }
                }
                ',
            ),
            array(
                "<?php
                namespace A\B;

                use Foo\Bar;

                echo 'Foo\Bar';
                ",
                '<?php
                namespace A\B;

                use Foo\Bar;

                echo Bar::class;
                ',
            ),
            array(
                "<?php

                namespace A\B {

                    class D {

                    }
                }

                namespace B\B {
                    class D {

                    }
                }

                namespace C {
                    use A\B\D;
                    var_dump('A\B\D');
                }

                namespace C1 {
                    use B\B\D;
                    var_dump('B\B\D');
                }
                ",
                '<?php

                namespace A\B {

                    class D {

                    }
                }

                namespace B\B {
                    class D {

                    }
                }

                namespace C {
                    use A\B\D;
                    var_dump(D::class);
                }

                namespace C1 {
                    use B\B\D;
                    var_dump(D::class);
                }
                ',
            ),
            array(
                "<?php
                use Foo\\Bar\{ClassA, ClassB, ClassC as C};
                use function Foo\\Bar\{fn_a, fn_b, fn_c};
                use const Foo\\Bar\{ConstA, ConstB, ConstC};

                echo 'Foo\\Bar\ClassB';
                echo 'Foo\\Bar\ClassC';
                ",
                '<?php
                use Foo\Bar\{ClassA, ClassB, ClassC as C};
                use function Foo\Bar\{fn_a, fn_b, fn_c};
                use const Foo\Bar\{ConstA, ConstB, ConstC};

                echo ClassB::class;
                echo C::class;
                ',
            ),
        );
    }
}
