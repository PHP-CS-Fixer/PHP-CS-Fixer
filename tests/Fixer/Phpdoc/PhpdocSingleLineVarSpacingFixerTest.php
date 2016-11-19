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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class PhpdocSingleLineVarSpacingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
                    class A {
                        /** @var MyCass6 $a */
                        public $test6 = 6;

                        /** @var MyCass6 */
                        public $testB = 7;
                    }
                ',
                '<?php
                    class A {
                        /**@var MyCass6 $a */
                        public $test6 = 6;

                        /**@var MyCass6*/
                        public $testB = 7;
                    }
                ',
            ),
            array(
                '<?php
                    /** @var MyCass1 $test1 description   and more. */
                    $test0 = 1;

                    /** @var MyCass2 description and    such. */
                    $test1 = 2;

                    /** @var MyCass3 description. */
                    $test2 = 3;

                    class A {
                        /** @var MyCass4 aa */
                        public $test4 = 4;

                        /** @var MyCass5 */
                        public $test5 = 5;

                        /** @var MyCass6 */
                        public $test6 = 6;
                    }
                ',
                '<?php
                    /**    @var   MyCass1 $test1      description   and more.*/
                    $test0 = 1;

                    /**    @var   MyCass2    description and    such. */
                    $test1 = 2;

                    /** @var	MyCass3    description.    */
                    $test2 = 3;

                    class A {
                        /**  @var   MyCass4   aa       */
                        public $test4 = 4;

                        /**     @var		MyCass5       */
                        public $test5 = 5;

                        /**     @var		MyCass6*/
                        public $test6 = 6;
                    }
                ',
            ),
            array(
                '<?php
class A
{
    /**
     * @param array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */
    public function __construct(array $options = array())
    {

    }

    /**
     * @var bool   $required Whether this element is required
     * @var string $label    The display name for this element
     */
    public function test($required, $label)
    {

    }

    /** @var   MyCass3
    */
    private $test0 = 0;
}',
            ),
        );
    }
}
