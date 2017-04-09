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

use PhpCsFixer\FixerDefinition\CodeSample;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerDefinition\CodeSample
 */
final class CodeSampleTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsValues()
    {
        $code = '<php echo $foo;';
        $configuration = array(
            'foo' => 'bar',
        );

        $codeSample = new CodeSample(
            $code,
            $configuration
        );

        $this->assertSame($code, $codeSample->getCode());
        $this->assertSame($configuration, $codeSample->getConfiguration());
    }

    public function testConfigurationDefaultsToNull()
    {
        $codeSample = new CodeSample('<php echo $foo;');

        $this->assertNull($codeSample->getConfiguration());
    }
}
