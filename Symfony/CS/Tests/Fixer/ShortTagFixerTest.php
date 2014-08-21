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

use Symfony\CS\Fixer\ShortTagFixer as Fixer;

class ShortTagFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideClosingTagExamples
     */
    public function testOneLineFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function provideClosingTagExamples()
    {
        return array(
            array('<?php echo \'Foo\';', '<? echo \'Foo\';'),
            array('<?= echo \'Foo\';', '<?= echo \'Foo\';'),
            array('<?php echo \'Foo\'; ?> PLAIN TEXT', '<?php echo \'Foo\'; ?> PLAIN TEXT'),
            array('<?php $query = "SELECT .... FROM my_table WHERE id <? LIMIT 1";', '<? $query = "SELECT .... FROM my_table WHERE id <? LIMIT 1";'),
            array('PLAIN TEXT<?php echo \'Foo\'; ?>', 'PLAIN TEXT<?php echo \'Foo\'; ?>'),
            array('<?php

echo \'Foo\';

',
                  '<?

echo \'Foo\';

',
            ),
            array(
                "<?php if ('<?php' === '<?') { }",
                "<? if ('<?php' === '<?') { }",
            ),
            array(
                'foo <?php  echo "-"; echo "aaa <?php bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <?php echo "<? ";',
                'foo <?  echo "-"; echo "aaa <?php bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <? echo "<? ";',
            ),
            array(
                '<?php
// Replace all <? with <?php !',
                '<?php
// Replace all <? with <?php !',
            ),
            array(
                '<?php
/**
 * Convert <?= ?> to long-form <?php echo ?> and <?php ?> to <?php ?>
 *
 */',
                '<?php
/**
 * Convert <?= ?> to long-form <?php echo ?> and <?php ?> to <?php ?>
 *
 */',
            ),
            array(
                "<?php \$this->data = preg_replace('/<\?(?!xml|php)/s', '<?php ',       \$this->data);",
                "<?php \$this->data = preg_replace('/<\?(?!xml|php)/s', '<?php ',       \$this->data);",
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
