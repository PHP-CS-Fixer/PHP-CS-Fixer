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

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ElseifFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return array(
            array('<?php if ($some) { $test = true; } else { $test = false; }'),
            array(
                '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
                '<?php if ($some) { $test = true; } else if ($some !== "test") { $test = false; }',
            ),
            array(
                '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
                '<?php if ($some) { $test = true; } else  if ($some !== "test") { $test = false; }',
            ),
            array(
                '<?php $js = \'if (foo.a) { foo.a = "OK"; } else if (foo.b) { foo.b = "OK"; }\';',
            ),
            array(
                '<?php
                    if ($a) {
                        $x = 1;
                    } elseif ($b) {
                        $x = 2;
                    }',
                '<?php
                    if ($a) {
                        $x = 1;
                    } else
                    if ($b) {
                        $x = 2;
                    }',
            ),
            array(
                '<?php
                    if ($a) {
                    } elseif/**/ ($b) {
                    }
                ',
                '<?php
                    if ($a) {
                    } else /**/ if ($b) {
                    }
                ',
            ),
            array(
                '<?php
                    if ($a) {
                    } elseif//
 ($b) {
                    }
                ',
                '<?php
                    if ($a) {
                    } else //
                        if ($b) {
                    }
                ',
            ),
        );
    }
}
