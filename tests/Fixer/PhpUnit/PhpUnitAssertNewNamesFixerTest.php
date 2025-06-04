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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitAssertNewNamesFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitAssertNewNamesFixer>
 */
final class PhpUnitAssertNewNamesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            self::generateTest(
                '
                    $this->assertFileDoesNotExist($a);
                    $this->assertIsNotReadable($a);
                    $this->assertIsNotWritable($a);
                    $this->assertDirectoryDoesNotExist($a);
                    $this->assertDirectoryIsNotReadable($a);
                    $this->assertDirectoryIsNotWriteable($a);
                    $this->assertFileIsNotReadable($a);
                    $this->assertFileIsNotWriteable($a);
                    $this->assertMatchesRegularExpression($a);
                    $this->assertDoesNotMatchRegularExpression($a);
                '
            ),
            self::generateTest(
                '
                    $this->assertFileNotExists($a);
                    $this->assertNotIsReadable($a);
                    $this->assertNotIsWritable($a);
                    $this->assertDirectoryNotExists($a);
                    $this->assertDirectoryNotIsReadable($a);
                    $this->assertDirectoryNotIsWritable($a);
                    $this->assertFileNotIsReadable($a);
                    $this->assertFileNotIsWritable($a);
                    $this->assertRegExp($a);
                    $this->assertNotRegExp($a);
                '
            ),
        ];
    }

    private static function generateTest(string $content): string
    {
        return "<?php final class FooTest extends \\PHPUnit_Framework_TestCase {\n    public function testSomething() {\n        ".$content."\n    }\n}\n";
    }
}
