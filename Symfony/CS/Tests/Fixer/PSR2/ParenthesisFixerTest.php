<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Marc Aubé
 */
final class ParenthesisFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFixSpaceInsideParenthesis($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function testLeaveNewLinesAlone()
    {
        $expected = <<<EOF
<?php

class Foo
{
    private function bar()
    {
        if (foo(
            'foo' ,
            'bar'    ,
            [1, 2, 3],
            'baz' // a comment just to mix things up
        )) {
            return 1;
        };
    }
}
EOF;
        $this->makeTest($expected);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php foo();',
                '<?php foo( );',
            ),
            array(
                '<?php
if (true) {
    // if body
}',
                '<?php
if ( true ) {
    // if body
}',
            ),
            array(
                '<?php
if (true) {
    // if body
}',
                '<?php
if (     true   ) {
    // if body
}',
            ),
            array(
                '<?php
function foo(\$bar, \$baz) {
{
    // function body
}',
                '<?php
function foo( \$bar, \$baz ) {
{
    // function body
}',
            ),
            array(
                '<?php
$foo->bar($arg1, $arg2);',
                '<?php
$foo->bar(  $arg1, $arg2   );',
            ),
            array(
                '<?php
$var = array( 1, 2, 3 );
',
            ),
            array(
                '<?php
$var = [ 1, 2, 3 ];
',
            ),
            // list call with trailing comma - need to leave alone
            array(
                '<?php list($path, $mode, ) = foo();',
            ),
            array(
                '<?php list($path, $mode,) = foo();',
            ),
        );
    }
}
