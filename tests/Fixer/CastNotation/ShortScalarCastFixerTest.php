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
    public function testFix74(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixDeprecatedCases
     *
     * @group legacy
     */
    public function testFix74Deprecated(string $expected, ?string $input = null): void
    {
        if (\PHP_VERSION_ID >= 80000) {
            static::markTestSkipped('PHP < 8.0 is required.');
        }

        $this->expectDeprecation('%AThe (real) cast is deprecated, use (float) instead');

        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        foreach (['boolean' => 'bool', 'integer' => 'int', 'double' => 'float', 'binary' => 'string'] as $from => $to) {
            foreach ($this->createCasesFor($from, $to) as $case) {
                yield $case;
            }
        }
    }

    public function provideFixDeprecatedCases(): iterable
    {
        return $this->createCasesFor('real', 'float');
    }

    /**
     * @dataProvider provideNoFixCases
     */
    public function testNoFix(string $expected): void
    {
        $this->doTest($expected);
    }

    public function provideNoFixCases(): array
    {
        $cases = [];
        $types = ['string', 'array', 'object'];

        if (\PHP_VERSION_ID < 80000) {
            $types[] = 'unset';
        }

        foreach ($types as $cast) {
            $cases[] = [sprintf('<?php $b=(%s) $d;', $cast)];
            $cases[] = [sprintf('<?php $b=( %s ) $d;', $cast)];
            $cases[] = [sprintf('<?php $b=(%s ) $d;', ucfirst($cast))];
            $cases[] = [sprintf('<?php $b=(%s ) $d;', strtoupper($cast))];
        }

        return $cases;
    }

    /**
     * @return iterable<array{0: non-empty-string, 1?: non-empty-string}>
     */
    private function createCasesFor(string $from, string $to): iterable
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
