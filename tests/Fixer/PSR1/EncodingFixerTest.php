<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\PSR1;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class EncodingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input, $file)
    {
        $this->doTest($expected, $input, $file);
    }

    public function provideExamples()
    {
        return array(
            $this->prepareTestCase('test-utf8.case1.php', 'test-utf8.case1-bom.php'),
            $this->prepareTestCase('test-utf8.case2.php', 'test-utf8.case2-bom.php'),
        );
    }

    private function prepareTestCase($expectedFilename, $inputFilename = null)
    {
        $expectedFile = $this->getTestFile(__DIR__.'/../../Fixtures/FixerTest/encoding/'.$expectedFilename);
        $inputFile = $inputFilename ? $this->getTestFile(__DIR__.'/../../Fixtures/FixerTest/encoding/'.$inputFilename) : null;

        return array(
            file_get_contents($expectedFile->getRealPath()),
            $inputFile ? file_get_contents($inputFile->getRealPath()) : null,
            $inputFile ?: $expectedFile,
        );
    }
}
