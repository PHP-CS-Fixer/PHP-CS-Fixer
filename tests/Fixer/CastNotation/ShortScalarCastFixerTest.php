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
 * @covers \PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer>
 */
final class ShortScalarCastFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        foreach (['boolean' => 'bool', 'integer' => 'int', 'double' => 'float', 'binary' => 'string'] as $from => $to) {
            yield from self::createCasesFor($from, $to);
        }

        $types = ['string', 'array', 'object'];

        foreach ($types as $cast) {
            yield [\sprintf('<?php $b=(%s) $d;', $cast)];

            yield [\sprintf('<?php $b=( %s ) $d;', $cast)];

            yield [\sprintf('<?php $b=(%s ) $d;', ucfirst($cast))];

            yield [\sprintf('<?php $b=(%s ) $d;', strtoupper($cast))];
        }
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield ['<?php $b=(unset) $d;'];

        yield ['<?php $b=( unset ) $d;'];

        yield ['<?php $b=(Unset ) $d;'];

        yield ['<?php $b=(UNSET ) $d;'];

        yield from self::createCasesFor('real', 'float');
    }

    /**
     * @return iterable<array{0: non-empty-string, 1?: non-empty-string}>
     */
    private static function createCasesFor(string $from, string $to): iterable
    {
        yield [
            \sprintf('<?php echo ( %s  )$a;', $to),
            \sprintf('<?php echo ( %s  )$a;', $from),
        ];

        yield [
            \sprintf('<?php $b=(%s) $d;', $to),
            \sprintf('<?php $b=(%s) $d;', $from),
        ];

        yield [
            \sprintf('<?php $b= (%s)$d;', $to),
            \sprintf('<?php $b= (%s)$d;', strtoupper($from)),
        ];

        yield [
            \sprintf('<?php $b=( %s) $d;', $to),
            \sprintf('<?php $b=( %s) $d;', ucfirst($from)),
        ];

        yield [
            \sprintf('<?php $b=(%s ) $d;', $to),
            \sprintf('<?php $b=(%s ) $d;', ucfirst($from)),
        ];
    }
}
