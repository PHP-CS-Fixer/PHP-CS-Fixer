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
use Prophecy\Prophecy;

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
            $this->createVersionSpecificationMock()->reveal(),
            $configuration
        );

        static::assertSame($code, $codeSample->getCode());
        static::assertSame($configuration, $codeSample->getConfiguration());
    }

    public function testConfigurationDefaultsToNull(): void
    {
        $codeSample = new VersionSpecificCodeSample(
            '<php echo $foo;',
            $this->createVersionSpecificationMock()->reveal()
        );

        static::assertNull($codeSample->getConfiguration());
    }

    /**
     * @dataProvider provideIsSuitableForVersionUsesVersionSpecificationCases
     */
    public function testIsSuitableForUsesVersionSpecification(int $version, bool $isSatisfied): void
    {
        $versionSpecification = $this->createVersionSpecificationMock();

        $versionSpecification
            ->isSatisfiedBy($version)
            ->willReturn($isSatisfied)
        ;

        $codeSample = new VersionSpecificCodeSample(
            '<php echo $foo;',
            $versionSpecification->reveal()
        );

        static::assertSame($isSatisfied, $codeSample->isSuitableFor($version));
    }

    public static function provideIsSuitableForVersionUsesVersionSpecificationCases(): array
    {
        return [
            'is-satisfied' => [\PHP_VERSION_ID, true],
            'is-not-satisfied' => [\PHP_VERSION_ID, false],
        ];
    }

    /**
     * @return Prophecy\ObjectProphecy|VersionSpecificationInterface
     */
    private function createVersionSpecificationMock()
    {
        return $this->prophesize(\PhpCsFixer\FixerDefinition\VersionSpecificationInterface::class);
    }
}
