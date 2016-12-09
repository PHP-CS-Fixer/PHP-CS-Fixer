<?php

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
use Prophecy\Prophecy;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class VersionSpecificCodeSampleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsValues()
    {
        $code = '<php echo $foo;';
        $configuration = array(
            'foo' => 'bar',
        );

        $codeSample = new VersionSpecificCodeSample(
            $code,
            $this->createVersionSpecificationMock()->reveal(),
            $configuration
        );

        $this->assertSame($code, $codeSample->getCode());
        $this->assertSame($configuration, $codeSample->getConfiguration());
    }

    public function testConfigurationDefaultsToNull()
    {
        $codeSample = new VersionSpecificCodeSample(
            '<php echo $foo;',
            $this->createVersionSpecificationMock()->reveal()
        );

        $this->assertNull($codeSample->getConfiguration());
    }

    /**
     * @dataProvider providerIsSuitableForVersionUsesVersionSpecification
     *
     * @param int  $version
     * @param bool $isSatisfied
     */
    public function testIsSuitableForUsesVersionSpecification($version, $isSatisfied)
    {
        $versionSpecification = $this->createVersionSpecificationMock();

        $versionSpecification
            ->isSatisfiedBy($version)
            ->willReturn($isSatisfied);

        $codeSample = new VersionSpecificCodeSample(
            '<php echo $foo;',
            $versionSpecification->reveal()
        );

        $this->assertSame($isSatisfied, $codeSample->isSuitableFor($version));
    }

    /**
     * @return array
     */
    public function providerIsSuitableForVersionUsesVersionSpecification()
    {
        return array(
            'is-satisfied' => array(PHP_VERSION_ID, true),
            'is-not-satisfied' => array(PHP_VERSION_ID, false),
        );
    }

    /**
     * @return Prophecy\ObjectProphecy|VersionSpecificationInterface
     */
    private function createVersionSpecificationMock()
    {
        return $this->prophesize('PhpCsFixer\FixerDefinition\VersionSpecificationInterface');
    }
}
