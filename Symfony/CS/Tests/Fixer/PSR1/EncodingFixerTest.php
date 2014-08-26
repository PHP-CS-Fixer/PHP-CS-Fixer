<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR1;

use Symfony\CS\Fixer\PSR1\EncodingFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class EncodingFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input, $file)
    {
        $fixer = new Fixer();

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function provideExamples()
    {
        return array(
            $this->prepareTestCase('test-utf8.php', 'test-utf8.php'),
            $this->prepareTestCase('test-utf8.php', 'test-utf8-bom.php'),
        );
    }

    private function prepareTestCase($expectedFilename, $inputFilename)
    {
        $expectedFile = $this->getTestFile(__DIR__.'/../../Fixtures/FixerTest/encoding/'.$expectedFilename);
        $inputFile = $this->getTestFile(__DIR__.'/../../Fixtures/FixerTest/encoding/'.$inputFilename);

        return array(
            file_get_contents($expectedFile->getRealpath()),
            file_get_contents($inputFile->getRealpath()),
            $inputFile,
        );
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
