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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @author SpacePossum
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoUselessInheritdocFixer
 */
final class PhpdocNoUselessInheritdocFixerTest extends AbstractFixerTestCase
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
                "<?php\n/** */class min1{}",
                "<?php\n/** @inheritdoc */class min1{}",
            ],
            [
                "<?php\nclass min2{/** */}",
                "<?php\nclass min2{/** @inheritdoc */}",
            ],
            [
                '<?php
                class A
                {
                    /** */
                    public function A(){}

                    /**
                     * '.'
                     */
                    public function B(){}

                    /**
                     * Descr.
                     *
                     * @param int $c
                     * '.'
                     */
                    public function C($c){}
                }
                ',
                '<?php
                class A
                {
                    /** @inheritdoc */
                    public function A(){}

                    /**
                     * @inheritdoc
                     */
                    public function B(){}

                    /**
                     * Descr.
                     *
                     * @param int $c
                     * @inheritdoc
                     */
                    public function C($c){}
                }
                ',
            ],
            [
                '<?php
                class B
                {
                    /** */
                    public function B(){}
                }
                ',
                '<?php
                class B
                {
                    /** {@INHERITDOC} */
                    public function B(){}
                }
                ',
            ],
            [
                '<?php
                /** D C */
                class C
                {
                }
                ',
                '<?php
                /** D {    @INHERITDOC   } C */
                class C
                {
                }
                ',
            ],
            [
                '<?php
                /** E */
                class E
                {
                }
                ',
                '<?php
                /**     {{@Inheritdoc}}   E */
                class E
                {
                }
                ',
            ],
            [
                '<?php
                /** F */
                class F
                {
                }
                ',
                '<?php
                /** F    @inheritdoc      */
                class F
                {
                }
                ',
            ],
            [
                '<?php
                    /** */
                    class G1{}
                    /** */
                    class G2{}
                ',
                '<?php
                    /** @inheritdoc */
                    class G1{}
                    /** @inheritdoc */
                    class G2{}
                ',
            ],
            [
                '<?php
                class H
                {
                    /* @inheritdoc comment, not PHPDoc */
                    public function H(){}
                }
                ',
            ],
            [
                '<?php
                class J extends Z
                {
                    /** @inheritdoc */
                    public function H(){}
                }
                ',
            ],
            [
                '<?php
                interface K extends Z
                {
                    /** @inheritdoc */
                    public function H();
                }
                ',
            ],
            [
                '<?php
                /** */
                interface K
                {
                    /** */
                    public function H();
                }
                ',
                '<?php
                /** @{inheritdoc} */
                interface K
                {
                    /** {@Inheritdoc} */
                    public function H();
                }
                ',
            ],
            [
                '<?php
                trait T
                {
                    /** @inheritdoc */
                    public function T()
                    {
                    }
                }',
            ],
            [
                '<?php
                class B
                {
                    /** */
                    public function falseImportFromTrait()
                    {
                    }
                }

                /** */
                class A
                {
                    use T;

                    /** @inheritdoc */
                    public function importFromTrait()
                    {
                    }
                }
                ',
                '<?php
                class B
                {
                    /** @inheritdoc */
                    public function falseImportFromTrait()
                    {
                    }
                }

                /** @inheritdoc */
                class A
                {
                    use T;

                    /** @inheritdoc */
                    public function importFromTrait()
                    {
                    }
                }
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return [
            [
'<?php

/** delete 1 */
class A
{
    /** delete 2 */
    public function B()
    {
        $a = new class implements I {

            /** @inheritdoc keep */
            public function A()
            {
                $b = new class extends D {

                    /** @inheritdoc keep */
                    public function C()
                    {
                        $d = new class() {

                            /** delete 3 */
                            public function D()
                            {
                            }
                        };
                    }
                };
            }
        };
    }

    /** delete 4 */
    public function B1()
    {
        $a1 = new class(){ };
    }

    /** delete 5 */
    public function B2()
    {
        //$a1 = new class(){ use D; };
    }
}
',
'<?php

/** @inheritdoc delete 1 */
class A
{
    /** @inheritdoc delete 2 */
    public function B()
    {
        $a = new class implements I {

            /** @inheritdoc keep */
            public function A()
            {
                $b = new class extends D {

                    /** @inheritdoc keep */
                    public function C()
                    {
                        $d = new class() {

                            /** @inheritdoc delete 3 */
                            public function D()
                            {
                            }
                        };
                    }
                };
            }
        };
    }

    /** @inheritdoc delete 4 */
    public function B1()
    {
        $a1 = new class(){ };
    }

    /** @inheritdoc delete 5 */
    public function B2()
    {
        //$a1 = new class(){ use D; };
    }
}
',
            ],
        ];
    }
}
