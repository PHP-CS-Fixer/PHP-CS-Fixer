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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Tareq Hasan <tareq@wedevs.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer
 */
final class SpaceInsideParenthesisFixerTest extends AbstractFixerTestCase
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

    public function testLeaveNewLinesAlone()
    {
        $expected = <<<'EOF'
<?php

class Foo
{
    private function bar()
    {
        if ( true ) {
            // do something
        }
    }
}
EOF;
        $this->doTest($expected);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
function foo( $bar, $baz )
{
    // function body
}',
                '<?php
function foo($bar, $baz)
{
    // function body
}',
            ],
            [
                '<?php
function hello( $value ) {
    // code...
}',
                '<?php
function hello($value) {
    // code...
}',
            ],
            [
                '<?php
$code = function ( $hello, $there ) use ( $ami, $tumi ) {
    // code
};
',
                '<?php
$code = function ($hello, $there) use ($ami, $tumi) {
    // code
};
',
            ],
            [
                '<?php
for ( $i = 0; $i < 42; $i++ ) {
    // code...
}
',
                '<?php
for ($i = 0; $i < 42; $i++) {
    // code...
}
',
            ],
            [
                '<?php
explode( $a, $b );
',
                '<?php
explode($a, $b);
',
            ],
            [
                '<?php
if ( $something ) {
    // code
}
',
                '<?php
if ($something) {
    // code
}
',
            ],
            [
                '<?php
multiply( ( 2 + 3 ) * 4 );
',
                '<?php
multiply((2 + 3 ) * 4);
',
            ]
        ];
    }
}
