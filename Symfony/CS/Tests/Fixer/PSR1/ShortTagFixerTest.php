<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR1;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class ShortTagFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideClosingTagExamples
     */
    public function testOneLineFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideClosingTagExamples()
    {
        return array(
            array('<?php echo \'Foo\';', '<? echo \'Foo\';'),
            array('<?php echo \'Foo\'; ?> PLAIN TEXT'),
            array('PLAIN TEXT<?php echo \'Foo\'; ?>'),
            array('<?php $query = "SELECT .... FROM my_table WHERE id <? LIMIT 1";', '<? $query = "SELECT .... FROM my_table WHERE id <? LIMIT 1";'),
            array('<?php

echo \'Foo\';

',
                  '<?

echo \'Foo\';

',
            ),
            array(
                '<?php
// Replace all <? with <?php !',
            ),
            array(
                '<?php
/**
 * Convert <?= ?> to long-form <?php echo ?> and <?php ?> to <?php ?>
 *
 */',
            ),
            array(
                "<?php \$this->data = preg_replace('/<\?(?!xml|php)/s', '<?php ',       \$this->data);",
            ),
        );
    }

    /**
     * @dataProvider provideShortOpenCases
     */
    public function testShortOpen($expected, $input = null)
    {
        if (!ini_get('short_open_tag')) {
            $this->markTestSkipped('PHP short open tags are not enabled.');

            return;
        }

        $this->makeTest($expected, $input);
    }

    public function provideShortOpenCases()
    {
        return array(
            array(
                "<?php if ('<?php' === '<?') { }",
                "<? if ('<?php' === '<?') { }",
            ),
            array(
                'foo <?php  echo "-"; echo "aaa <?php bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <?php echo "<? ";',
                'foo <?  echo "-"; echo "aaa <?php bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <? echo "<? ";',
            ),
             array(
            "<?php
'<?
';",
             ),
        );
    }

    public function testShortEcho()
    {
        if (PHP_VERSION_ID < 50400 && '' === ini_get('short_open_tag')) {
            $this->markTestSkipped('PHP short echo tags are not enabled.');

            return;
        }

        $this->makeTest('<?= \'Foo\';');
    }
}
