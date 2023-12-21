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

namespace PhpCsFixer\Tests\FixerDefinition;

use PhpCsFixer\FixerDefinition\VersionSpecificationInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample
 */
final class VersionSpecificCodeSampleTest extends TestCase
{
    public function testConstructorSetsValues(): void
    {
        $code = '<php echo $foo;';
        $configuration = [
            'foo' => 'bar',
        ];

        $codeSample = new VersionSpecificCodeSample(
            $code,
            $this->createVersionSpecificationDouble(),
            $configuration
        );

        self::assertSame($code, $codeSample->getCode());
        self::assertSame($configuration, $codeSample->getConfiguration());
    }

    public function testConfigurationDefaultsToNull(): void
    {
        $codeSample = new VersionSpecificCodeSample(
            '<php echo $foo;',
            $this->createVersionSpecificationDouble()
        );

        self::assertNull($codeSample->getConfiguration());
    }

    /**
     * @dataProvider provideIsSuitableForUsesVersionSpecificationCases
     */
    public function testIsSuitableForUsesVersionSpecification(int $version, bool $isSatisfied): void
    {
        $codeSample = new VersionSpecificCodeSample(
            '<php echo $foo;',
            $this->createVersionSpecificationDouble($isSatisfied)
        );

        self::assertSame($isSatisfied, $codeSample->isSuitableFor($version));
    }

    public static function provideIsSuitableForUsesVersionSpecificationCases(): iterable
    {
        yield 'is-satisfied' => [100, true];

        yield 'is-not-satisfied' => [100, false];
    }

    private function createVersionSpecificationDouble(bool $isSatisfied = true): VersionSpecificationInterface
    {
        return new class($isSatisfied) implements VersionSpecificationInterface {
            private bool $isSatisfied;

            public function __construct(bool $isSatisfied)
            {
                $this->isSatisfied = $isSatisfied;
            }

            public function isSatisfiedBy(int $version): bool
            {
                return $this->isSatisfied;
            }
        };
    }
}
