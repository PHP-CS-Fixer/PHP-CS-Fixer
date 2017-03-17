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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Ivan Boprzenkov <ivan.borzenkov@gmail.com>
 *
 * @internal
 */
final class NonPrintableCharacterFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string       $expected
     * @param string       $input
     * @param \SplFileInfo $file
     *
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input, $file)
    {
        $this->doTest($expected, $input, $file);
    }

    public function provideExamples()
    {
        return array(
            $this->prepareTestCase('test-utf.case1.php', 'test-utf.case1-utf.php'),
        );
    }

    private function prepareTestCase($expectedFilename, $inputFilename = null)
    {
        $expectedFile = $this->getTestFile(__DIR__.'/../../Fixtures/FixerTest/utf/'.$expectedFilename);
        $inputFile = $inputFilename ? $this->getTestFile(__DIR__.'/../../Fixtures/FixerTest/utf/'.$inputFilename) : null;

        return array(
            file_get_contents($expectedFile->getRealPath()),
            $inputFile ? file_get_contents($inputFile->getRealPath()) : null,
            $inputFile ?: $expectedFile,
        );
    }
}
