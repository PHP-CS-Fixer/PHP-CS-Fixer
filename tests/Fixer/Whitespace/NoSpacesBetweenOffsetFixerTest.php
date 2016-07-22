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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 *
 * @internal
 */
final class NoSpacesBetweenOffsetFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFixSpaceInsideOffset($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function testLeaveCommentsAlone()
    {
        $expected = <<<'EOF'
<?php

$withComments[0] // here is a comment
    [1] // and here is another
    [2] = 3;
EOF;
        $this->doTest($expected);
    }

    public function testLeaveComplexString()
    {
        $expected = <<<'EOF'
<?php

echo "I am printing some spaces here    {$foo->bar[1]}     {$foo->bar[1]}.";
EOF;
        $this->doTest($expected);
    }

    public function testLeaveFunctions()
    {
        $expected = <<<'EOF'
<?php

function someFunc()    {   $someVar = [];   }
EOF;
        $this->doTest($expected);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
$withComments[0] // here is a comment
    [1] // and here is another
    [2][3] = 4;',
                '<?php
$withComments [0] // here is a comment
    [1] // and here is another
    [2] [3] = 4;',
            ),
            array(
                '<?php
$c = SOME_CONST[0][1][2];',
                '<?php
$c = SOME_CONST [0] [1]   [2];',
            ),
            array(
                '<?php
$f = someFunc()[0][1][2];',
                '<?php
$f = someFunc() [0] [1]   [2];',
            ),
            array(
                '<?php
$foo[ ][0][1][2] = 3;',
                '<?php
$foo [ ] [0] [1]   [2] = 3;',
            ),
            array(
                '<?php
$foo[0][1][2] = 3;',
                '<?php
$foo [0] [1]   [2] = 3;',
            ),
            array(
                '<?php
$bar = $foo[0][1][2];',
                '<?php
$bar = $foo [0] [1]   [2];',
            ),
            array(
                '<?php
$baz[0][1][2] = 3;',
                '<?php
$baz [0]
     [1]
     [2] = 3;',
            ),
            array(
                '<?php
$foo{0}{1}{2} = 3;',
                '<?php
$foo {0} {1}   {2} = 3;',
            ),
            array(
                '<?php
$foobar = $foo{0}[1]{2};',
                '<?php
$foobar = $foo {0} [1]   {2};',
            ),
        );
    }
}
