<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\EncodingFixer;

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
        $this->expectOutputString($expected);

        $fixer = new EncodingFixer();

        $fixer->fix($file, $input);
    }

    public function provideExamples()
    {

        $examples = array(
            $this->prepareTestCase('test-ascii.php'),
            $this->prepareTestCase('test-other.php', 'SJIS'),
            $this->prepareTestCase('test-utf8.php'),
            $this->prepareTestCase('test-utf8-bom.php', 'UTF-8 BOM'),
        );

        return $examples;
    }

    private function prepareTestCase($file, $errorEncoding = null)
    {
        $file = new \SplFileInfo(__DIR__.'/../Fixtures/FixerTest/encoding/'.$file);

        return array(
            $errorEncoding ? '! File '.strtr($file->getRealPath(), '\\', '/').' with incorrect encoding: '.$errorEncoding.PHP_EOL : '',
            file_get_contents($file->getRealpath()),
            $file,
        );
    }
}
