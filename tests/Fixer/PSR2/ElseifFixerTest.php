<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\PSR2;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 *
 * @internal
 */
final class ElseifFixerTest extends AbstractFixerTestCase
{
    /**
     * @covers PhpCsFixer\Fixer\PSR2\ElseifFixer::fix
     */
    public function testThatInvalidElseIfIsFixed()
    {
        $this->doTest(
            '<?php if ($some) { $test = true; } else { $test = false; }'
        );

        $this->doTest(
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            '<?php if ($some) { $test = true; } else if ($some !== "test") { $test = false; }'
        );

        $this->doTest(
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            '<?php if ($some) { $test = true; } else  if ($some !== "test") { $test = false; }'
        );

        $this->doTest(
            '<?php $js = \'if (foo.a) { foo.a = "OK"; } else if (foo.b) { foo.b = "OK"; }\';'
        );

        $this->doTest(
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
