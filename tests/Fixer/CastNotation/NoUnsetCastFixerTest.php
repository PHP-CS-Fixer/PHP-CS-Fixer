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

use PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(NoUnsetCastFixer::class)]
final class NoUnsetCastFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP >= 8.0.0
     */
    #[RequiresPhp('>= 8.0.0')]
    public function testFix80(): void
    {
        // to make sure that fixer is registered and can be tested on dummy file even on PHP version mismatch

        $this->doTest('<?php echo "dummy";');
    }

    /**
     * @requires PHP < 8.0.0
     *
     * @dataProvider provideFixCases
     */
    #[DataProvider('provideFixCases')]
    #[RequiresPhp('< 8.0.0')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple form I' => [
            "<?php\n\$a = null;",
            "<?php\n\$a =(unset)\$z;",
        ];

        yield 'simple form II' => [
            "<?php\n\$b = null;",
            "<?php\n\$b = (unset)\$z;",
        ];

        yield 'simple form III' => [
            "<?php\n\$c = null?>",
            "<?php\n\$c = (unset)\$z?>",
        ];

        yield 'lot of spaces' => [
            "<?php\n\$d = \t \t \t null;",
            "<?php\n\$d = \t (unset)\$z\t \t ;",
        ];

        yield 'comments' => [
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
        ];

        yield [
            "<?php\n(unset) \$b;",
        ];

        yield [
            '<?php $r = (unset) f(1);',
        ];

        yield [
            '<?php $r = (unset) (new C())->mf(3);',
        ];

        yield [
            '<?php $r = (unset) $f(1);',
        ];

        yield [
            '<?php $r = (unset) $c::sf(2) ?>',
        ];

        yield [
            '<?php $r = (unset) $a[0];',
        ];

        yield [
            '<?php $r = (unset) $n**f($n);',
        ];
    }
}
