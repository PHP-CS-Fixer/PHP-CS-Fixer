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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer
 */
final class TernaryOperatorSpacesFixerTest extends AbstractFixerTestCase
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
        return [
            [
                '<?php $a = $a ? 1 : 0;',
                '<?php $a = $a  ? 1 : 0;',
            ],
            [
                '<?php $a = $a ?
#
: $b;',
            ],
            [
                '<?php $a = $a#
 ? '.'
#
1 : 0;',
            ],
            [
                '<?php $val = (1===1) ? true : false;',
                '<?php $val = (1===1)?true:false;',
            ],
            [
                '<?php $val = 1===1 ? true : false;',
                '<?php $val = 1===1?true:false;',
            ],
            [
                '<?php
$a = $b ? 2 : ($bc ? 2 : 3);
$a = $bc ? 2 : 3;',
                '<?php
$a = $b   ?   2  :    ($bc?2:3);
$a = $bc?2:3;',
            ],
            [
                '<?php $config = $config ?: new Config();',
                '<?php $config = $config ? : new Config();',
            ],
            [
                '<?php
$a = $b ? (
        $c + 1
    ) : (
        $d + 1
    );',
            ],
            [
                '<?php
$a = $b
    ? $c
    : $d;',
                '<?php
$a = $b
    ?$c
    :$d;',
            ],
            [
                '<?php
$a = $b  //
    ? $c  /**/
    : $d;',
                '<?php
$a = $b  //
    ?$c  /**/
    :$d;',
            ],
            [
                '<?php
$a = ($b
    ? $c
    : ($d
        ? $e
        : $f
    )
);',
            ],
            [
                '<?php
$a = ($b
    ? ($c1 ? $c2 : ($c3a ?: $c3b))
    : ($d1 ? $d2 : $d3)
);',
                '<?php
$a = ($b
    ? ($c1?$c2:($c3a? :$c3b))
    : ($d1?$d2:$d3)
);',
            ],
        ];
    }
}
