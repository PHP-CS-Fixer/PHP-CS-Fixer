<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\CastNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer
 *
 * @requires PHP <8.0
 */
final class NoUnsetCastFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            'simple form I' => [
                "<?php\n\$a = null;",
                "<?php\n\$a =(unset)\$z;",
            ],
            'simple form II' => [
                "<?php\n\$b = null;",
                "<?php\n\$b = (unset)\$z;",
            ],
            'simple form III' => [
                "<?php\n\$c = null?>",
                "<?php\n\$c = (unset)\$z?>",
            ],
            'lot of spaces' => [
                "<?php\n\$d = \t \t \t null;",
                "<?php\n\$d = \t (unset)\$z\t \t ;",
            ],
            'comments' => [
                '<?php
#0
$a#1
#2
= null#3
#4
#5
#6
#7
#8
;
',
                '<?php
#0
$a#1
#2
=#3
#4
(unset)#5
#6
$b#7
#8
;
',
            ],
            [
                "<?php\n(unset) \$b;",
            ],
            [
                '<?php $r = (unset) f(1);',
            ],
            [
                '<?php $r = (unset) (new C())->mf(3);',
            ],
            [
                '<?php $r = (unset) $f(1);',
            ],
            [
                '<?php $r = (unset) $c::sf(2) ?>',
            ],
            [
                '<?php $r = (unset) $a[0];',
            ],
            [
                '<?php $r = (unset) $n**f($n);',
            ],
        ];
    }
}
