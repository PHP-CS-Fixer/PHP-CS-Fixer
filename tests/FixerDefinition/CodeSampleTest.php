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

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\FixerDefinition\CodeSample
 *
 * @author Andreas Möller <am@localheinz.com>
 */
final class CodeSampleTest extends TestCase
{
    public function testConstructorSetsValues(): void
    {
        $code = '<php echo $foo;';
        $configuration = [
            'foo' => 'bar',
        ];

        $codeSample = new CodeSample(
            $code,
            $configuration
        );

        self::assertSame($code, $codeSample->getCode());
        self::assertSame($configuration, $codeSample->getConfiguration());
    }

    public function testConfigurationDefaultsToNull(): void
    {
        $codeSample = new CodeSample('<php echo $foo;');

        self::assertNull($codeSample->getConfiguration());
    }
}
