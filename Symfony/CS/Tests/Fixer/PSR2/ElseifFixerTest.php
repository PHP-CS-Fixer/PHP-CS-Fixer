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

use Symfony\CS\Fixer;
use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ElseifFixerTest extends AbstractFixerTestBase
{
    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::fix
     */
    public function testThatInvalidElseIfIsFixed()
    {
        $this->makeTest(
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            '<?php if ($some) { $test = true; } else if ($some !== "test") { $test = false; }'
        );

        $this->makeTest(
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            '<?php if ($some) { $test = true; } else  if ($some !== "test") { $test = false; }'
        );

        $this->makeTest(
            '<?php $js = \'if (foo.a) { foo.a = "OK"; } else if (foo.b) { foo.b = "OK"; }\';'
        );

        $this->makeTest(
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
}'
        );
    }
}
