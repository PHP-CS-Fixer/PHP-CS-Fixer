<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Fixer\PSR2\LowercaseKeywordsFixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class LowercaseKeywordsFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input)
    {
        $fixer = new LowercaseKeywordsFixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = (1 and 2);', '<?php $x = (1 AND 2);'),
            array('<?php foreach(array(1, 2, 3) as $val) {}', '<?php foreach(array(1, 2, 3) AS $val) {}'),
            array('<?php echo "GOOD AS NEW";', '<?php echo "GOOD AS NEW";'),
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
