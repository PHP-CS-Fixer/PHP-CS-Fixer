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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoTrailingCommaInListCallFixer
 */
final class NoTrailingCommaInListCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
    list($a, $b) = foo();
    list($a, , $c, $d) = foo();
    list($a, , $c) = foo();
    list($a) = foo();
    list($a , $b) = foo();
    list($a, /* $b */, $c) = foo();
',
            '<?php
    list($a, $b) = foo();
    list($a, , $c, $d, ) = foo();
    list($a, , $c, , ) = foo();
    list($a, , , , , ) = foo();
    list($a , $b , ) = foo();
    list($a, /* $b */, $c, ) = foo();
',
        ];

        yield [
            '<?php
list(
$a#
,#
#
) = $a;',
        ];
    }
}
