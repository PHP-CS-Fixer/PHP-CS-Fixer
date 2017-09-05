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

namespace PhpCsFixer\Tests\Fixer\PhpTag;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer
 */
final class FullOpeningTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array('<?php echo \'Foo\';', '<? echo \'Foo\';'),
            array('<?php echo \'Foo\';', '<?pHp echo \'Foo\';'),
            array('<?= \'Foo\';'),
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
                "<?php if ('<?php' === '<?') { }",
                "<? if ('<?php' === '<?') { }",
            ),
            array(
                '<?php // <?php',
                '<?pHP // <?php',
            ),
            array(
                "<?php
'<?
';",
            ),
            array(
                '<?php
// Replace all <? with <?php !',
            ),
            array(
                '<?php
// Replace all <? with <?pHp !',
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
            array(
                'foo <?php  echo "-"; echo "aaa <?php bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <?php echo "<? ";',
            ),
            array(
                '<?php
$a = <<<           "TEST"
<?Php <?
TEST;?>
TEST;

?>
<?php $a = <<<           \'TEST\'
<?PHP <?
TEST;?>
TEST;

?>
',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixLT70Cases
     * @requires PHP <7.0
     */
    public function testFixLT70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixLT70Cases()
    {
        return array(
            array(
                'foo <?php  echo "-"; echo "aaa <? bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <?php echo "<? ";',
                'foo <?  echo "-"; echo "aaa <? bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <? echo "<? ";',
            ),
        );
    }
}
