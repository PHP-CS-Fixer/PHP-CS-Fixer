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

use Symfony\CS\Fixer\PhpClosingTagFixer;

class PhpClosingTagFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideClosingTagExamples
     */
    public function testOneLineFix($expected, $input)
    {
        $fixer = new PhpClosingTagFixer();
        $file = $this->getTestFile();

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function provideClosingTagExamples()
    {
        return array(
            array('<?php echo \'Foo\';', '<?php echo \'Foo\'; ?>'),
            array('<?php echo \'Foo\';', '<?php echo \'Foo\';?>'),
            array('<?php echo \'Foo\'; ?> PLAIN TEXT', '<?php echo \'Foo\'; ?> PLAIN TEXT'),
            array('PLAIN TEXT<?php echo \'Foo\'; ?>', 'PLAIN TEXT<?php echo \'Foo\'; ?>'),
            array('<?php

echo \'Foo\';

',
                  '<?php

echo \'Foo\';

?>
    ',
            ),
            array('<?php echo \'Foo\'; ?>
<p><?php echo \'this is a template\'; ?></p>
<?php echo \'Foo\'; ?>
',
                  '<?php echo \'Foo\'; ?>
<p><?php echo \'this is a template\'; ?></p>
<?php echo \'Foo\'; ?>
',
            ),
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
