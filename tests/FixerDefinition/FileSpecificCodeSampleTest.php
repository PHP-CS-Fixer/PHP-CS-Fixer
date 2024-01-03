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

use PhpCsFixer\FixerDefinition\FileSpecificCodeSample;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerDefinition\FileSpecificCodeSample
 */
final class FileSpecificCodeSampleTest extends TestCase
{
    public function testImplementsFileSpecificCodeSampleInterface(): void
    {
        $sample = new FileSpecificCodeSample(file_get_contents(__FILE__), new \SplFileInfo(__FILE__));

        self::assertInstanceOf(FileSpecificCodeSampleInterface::class, $sample);
    }

    public function testDefaults(): void
    {
        $code = file_get_contents(__FILE__);
        $splFileInfo = new \SplFileInfo(__FILE__);

        $sample = new FileSpecificCodeSample(
            $code,
            $splFileInfo
        );

        self::assertSame($code, $sample->getCode());
        self::assertSame($splFileInfo, $sample->getSplFileInfo());
        self::assertNull($sample->getConfiguration());
    }

    public function testConstructorSetsValues(): void
    {
        $code = file_get_contents(__FILE__);
        $splFileInfo = new \SplFileInfo(__FILE__);
        $configuration = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];

        $sample = new FileSpecificCodeSample(
            $code,
            $splFileInfo,
            $configuration
        );

        self::assertSame($code, $sample->getCode());
        self::assertSame($splFileInfo, $sample->getSplFileInfo());
        self::assertSame($configuration, $sample->getConfiguration());
    }
}
