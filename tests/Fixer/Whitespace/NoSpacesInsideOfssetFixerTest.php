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
final class NoSpacesInsideOfssetFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFixSpaceInsideOffset($expected, $input = null)
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
        if ([1, 2, 3] && [
            'foo',
            'bar' ,
            'baz'// a comment just to mix things up
        ]) {
            return 1;
        };
    }
}
EOF;
        $this->doTest($expected);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
$foo = array(1, 2, 3);
$var = $foo[1];',
                '<?php
$foo = array(1, 2, 3);
$var = $foo[ 1 ];',
            ),
            array(
                '<?php
$arr = [2,   2 , ];
$var = $arr[0];',
                '<?php
$arr = [2,   2 , ];
$var = $arr[ 0 ];',
            ),
            array(
                '<?php
$arr[2] = 3;',
                '<?php
$arr[ 2    ] = 3;',
            ),
            array(
                '<?php
$arr[] = 3;',
                '<?php
$arr[  ] = 3;',
            ),
            array(
                '<?php
$arr[]["some_offset"][] = 3;',
                '<?php
$arr[  ][ "some_offset"   ][     ] = 3;',
            ),
            array(
                '<?php
$arr[]["some  offset with  spaces"][] = 3;',
                '<?php
$arr[  ][ "some  offset with  spaces"   ][     ] = 3;',
            ),
            array(
                '<?php
$var = $arr[0];',
                '<?php
$var = $arr[     0   ];',
            ),
            array(
                '<?php
$var = $arr[0][0];',
                '<?php
$var = $arr[    0        ][ 0  ];',
            ),
            array(
                '<?php
$var = $arr[$a    [$b]];',
                '<?php
$var = $arr[    $a    [ $b    ]  ];',
            ),
            array(
                '<?php
$var = $arr[$a	[$b]];',
                '<?php
$var = $arr[	$a	[	$b	]	];',
            ),
            array(
                '<?php
$var = $arr[0][
     0];',
                 '<?php
$var = $arr[0][
     0 ];',
            ),
            array(
                '<?php
$var = $arr[0][0
         ];',
                 '<?php
$var = $arr[0][     0
         ];',
            ),
        );
    }
}
