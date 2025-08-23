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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\EncodingFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Basic\EncodingFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class EncodingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, ?\SplFileInfo $file = null): void
    {
        $this->doTest($expected, $input, $file);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string, 2?: \SplFileInfo}>
     */
    public static function provideFixCases(): iterable
    {
        yield self::prepareTestCase('test-utf8.case1.php', 'test-utf8.case1-bom.php');

        yield self::prepareTestCase('test-utf8.case2.php', 'test-utf8.case2-bom.php');

        yield ['<?php '];
    }

    /**
     * @return array{string, null|string, \SplFileInfo}
     */
    private static function prepareTestCase(string $expectedFilename, ?string $inputFilename = null): array
    {
        $expectedFile = new \SplFileInfo(__DIR__.'/../../Fixtures/FixerTest/encoding/'.$expectedFilename);
        $inputFile = null !== $inputFilename ? new \SplFileInfo(__DIR__.'/../../Fixtures/FixerTest/encoding/'.$inputFilename) : null;

        return [
            (string) file_get_contents($expectedFile->getRealPath()),
            null !== $inputFile ? (string) file_get_contents($inputFile->getRealPath()) : null,
            $inputFile ?? $expectedFile,
        ];
    }
}
