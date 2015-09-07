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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class TernarySpacesFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php $val = (1===1) ? true : false;',
                '<?php $val = (1===1)?true:false;',
            ),
            array(
                '<?php $val = 1===1 ? true : false;',
                '<?php $val = 1===1?true:false;',
            ),
            array(
                '<?php
$a = $b  ? 2 : 3;
$a = $bc ? 2 : 3;',
            ),
            array(
                '<?php $config = $config ?: new Config();',
                '<?php $config = $config ? : new Config();',
            ),
            array(
                '<?php
$a = $b ? (
        $c + 1
    ) : (
        $d + 1
    );',
            ),
            array(
                '<?php
$a = $b
    ? $c
    : $d;',
                '<?php
$a = $b
    ?$c
    :$d;',
            ),
            array(
                '<?php
$a = ($b
    ? $c
    : ($d
        ? $e
        : $f
    )
);',
            ),
            array(
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
            ),
        );
    }
}
