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

use Symfony\CS\Fixer\ShortTagFixer;

class ShortTagFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideClosingTagExamples
     */
    public function testOneLineFix($expected, $input)
    {
        $fixer = new ShortTagFixer();
        $file = $this->getTestFile();

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function provideClosingTagExamples()
    {
        return array(
            array('<?php echo \'Foo\';', '<? echo \'Foo\';'),
	    array('<?= echo \'Foo\';', '<?= echo \'Foo\';'),
            array('<?php echo \'Foo\'; ?> PLAIN TEXT', '<?php echo \'Foo\'; ?> PLAIN TEXT'),
            array('PLAIN TEXT<?php echo \'Foo\'; ?>', 'PLAIN TEXT<?php echo \'Foo\'; ?>'),
            array('<?php

echo \'Foo\';

',
                  '<?

echo \'Foo\';

')
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
