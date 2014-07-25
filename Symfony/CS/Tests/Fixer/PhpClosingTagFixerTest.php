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

use Symfony\CS\Fixer\PhpClosingTagFixer as Fixer;

class PhpClosingTagFixerTest extends \PHPUnit_Framework_TestCase
{
    private function makeTest($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    /**
     * @dataProvider provideCasesWithFullOpenTag
     */
    public function testCasesWithFullOpenTag($expected, $input)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provideCasesWithShortOpenTag
     */
    public function testCasesWithShortOpenTag($expected, $input)
    {
        if (!ini_get('short_open_tag')) {
            $this->markTestSkipped('PHP short open tags are not enabled.');
            return;
        }

        $this->makeTest($expected, $input);
    }

    public function provideCasesWithFullOpenTag()
    {
        return array(
            array('<?php echo \'Foo\';', '<?php echo \'Foo\'; ?>'),
            array('<?php echo \'Foo\';', '<?php echo \'Foo\';?>'),
            array('<?php echo \'Foo\'; ?> PLAIN TEXT', '<?php echo \'Foo\'; ?> PLAIN TEXT'),
            array('PLAIN TEXT<?php echo \'Foo\'; ?>', 'PLAIN TEXT<?php echo \'Foo\'; ?>'),
            array('<?php

echo \'Foo\';',
                  '<?php

echo \'Foo\';

?>',
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

    public function provideCasesWithShortOpenTag()
    {
        return array(
            array('<? echo \'Foo\';', '<? echo \'Foo\'; ?>'),
            array('<? echo \'Foo\';', '<? echo \'Foo\';?>'),
            array('<? echo \'Foo\'; ?>
<p><? echo \'this is a template\'; ?></p>
<? echo \'Foo\'; ?>
',
                  '<? echo \'Foo\'; ?>
<p><? echo \'this is a template\'; ?></p>
<? echo \'Foo\'; ?>
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
