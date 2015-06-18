<?php

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
class AlignConstantEqualsFixerTest extends AbstractFixerTestBase
{
    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                const FIRST_CONSTANT          = 1;
                const SECOND_CONSTANT         = 2;
                const THIRD_AND_LAST_CONSTANT = 3;
                ',
                '<?php
                const FIRST_CONSTANT = 1;
                const SECOND_CONSTANT = 2;
                const THIRD_AND_LAST_CONSTANT = 3;
                ',
            ),
            array(
                '<?php
                const FIRST_CONSTANT          = 1;
                const SECOND_CONSTANT         = 2;
                const THIRD_AND_LAST_CONSTANT = 3;
                ',
                '<?php
                const FIRST_CONSTANT            = 1;
                const SECOND_CONSTANT           = 2;
                const THIRD_AND_LAST_CONSTANT   = 3;
                ',
            ),
            array(
                '<?php
                const FIRST_CONSTANT          = 1;
                const SECOND_CONSTANT         = 2;
                const THIRD_AND_LAST_CONSTANT = 3;
                ',
                '<?php
                const FIRST_CONSTANT          = 1;
                const SECOND_CONSTANT         = 2;
                const THIRD_AND_LAST_CONSTANT   = 3;
                ',
            ),
            array(
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT          = 1;
                    const SECOND_CONSTANT         = 2;
                    const THIRD_AND_LAST_CONSTANT = 3;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
            ),
            array(
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT          = 1;
                    const SECOND_CONSTANT         = 2;
                    const THIRD_AND_LAST_CONSTANT = 3;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT = 1;
                    const SECOND_CONSTANT = 2;
                    const THIRD_AND_LAST_CONSTANT = 3;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
            ),
            array(
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT          = 1;
                    const SECOND_CONSTANT         = 2;
                    const THIRD_AND_LAST_CONSTANT = 3;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT = 1;
                    const SECOND_CONSTANT = 2;
                    const THIRD_AND_LAST_CONSTANT   = 3;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
            ),
            array(
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT          = 1;
                    const SECOND_CONSTANT         = 2;
                    const THIRD_AND_LAST_CONSTANT = 3;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT            = 1;
                    const SECOND_CONSTANT           = 2;
                    const THIRD_AND_LAST_CONSTANT   = 3;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
            ),
            array(
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT          = 1;
                    const SECOND_CONSTANT         = 2;
                    const THIRD_AND_LAST_CONSTANT = 3;

                    const NEWLINE_BEFORE          = 42;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
                '<?php
                class Foo
                {
                    const FIRST_CONSTANT          = 1;
                    const SECOND_CONSTANT         = 2;
                    const THIRD_AND_LAST_CONSTANT = 3;

                    const NEWLINE_BEFORE = 42;

                    public function bar($thing)
                    {
                        $myThing = $thing;
                        $myOtherThing = $thing;
                    }
                }
                ',
            ),
            array(
                '<?php
                const FIRST_CONSTANT          = 1;
                const SECOND_CONSTANT         = 2;
                const THIRD_AND_LAST_CONSTANT = 3;

                class Foo
                {
                    const CLASS_FIRST_CONSTANT          = 1;
                    const CLASS_SECOND_CONSTANT         = 2;
                    const CLASS_THIRD_AND_LAST_CONSTANT = 3;
                }
                ',
                '<?php
                const FIRST_CONSTANT = 1;
                const SECOND_CONSTANT = 2;
                const THIRD_AND_LAST_CONSTANT = 3;

                class Foo
                {
                    const CLASS_FIRST_CONSTANT = 1;
                    const CLASS_SECOND_CONSTANT = 2;
                    const CLASS_THIRD_AND_LAST_CONSTANT = 3;
                }
                ',
            ),
        );
    }
}
