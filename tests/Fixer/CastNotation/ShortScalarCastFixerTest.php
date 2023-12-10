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
     * @dataProvider provideFix74DeprecatedCases
     *
     * @group legacy
     *
     * @requires PHP <8.0
     */
    public function testFix74Deprecated(string $expected, ?string $input = null): void
    {
        $this->expectDeprecation('%AThe (real) cast is deprecated, use (float) instead');

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        foreach (['boolean' => 'bool', 'integer' => 'int', 'double' => 'float', 'binary' => 'string'] as $from => $to) {
            foreach (self::createCasesFor($from, $to) as $case) {
                yield $case;
            }
        }
    }

    public static function provideFix74DeprecatedCases(): iterable
    {
        return self::createCasesFor('real', 'float');
    }

    /**
     * @dataProvider provideNoFixCases
     */
    public function testNoFix(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideNoFixCases(): iterable
    {
        $types = ['string', 'array', 'object'];

        if (\PHP_VERSION_ID < 8_00_00) {
            $types[] = 'unset';
        }

        foreach ($types as $cast) {
            yield [sprintf('<?php $b=(%s) $d;', $cast)];

            yield [sprintf('<?php $b=( %s ) $d;', $cast)];

            yield [sprintf('<?php $b=(%s ) $d;', ucfirst($cast))];

            yield [sprintf('<?php $b=(%s ) $d;', strtoupper($cast))];
        }
    }

    /**
     * @return iterable<array{0: non-empty-string, 1?: non-empty-string}>
     */
    private static function createCasesFor(string $from, string $to): iterable
    {
        yield [
            sprintf('<?php echo ( %s  )$a;', $to),
            sprintf('<?php echo ( %s  )$a;', $from),
        ];

        yield [
            sprintf('<?php $b=(%s) $d;', $to),
            sprintf('<?php $b=(%s) $d;', $from),
        ];

        yield [
            sprintf('<?php $b= (%s)$d;', $to),
            sprintf('<?php $b= (%s)$d;', strtoupper($from)),
        ];

        yield [
            sprintf('<?php $b=( %s) $d;', $to),
            sprintf('<?php $b=( %s) $d;', ucfirst($from)),
        ];

        yield [
            sprintf('<?php $b=(%s ) $d;', $to),
            sprintf('<?php $b=(%s ) $d;', ucfirst($from)),
        ];
    }
}
