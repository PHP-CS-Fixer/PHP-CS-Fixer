<?php

declare(strict_types=1);

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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            ['<?php echo \'Foo\';', '<? echo \'Foo\';'],
            ['<?php echo \'Foo\';', '<?pHp echo \'Foo\';'],
            ['<?= \'Foo\';'],
            ['<?php echo \'Foo\'; ?> PLAIN TEXT'],
            ['PLAIN TEXT<?php echo \'Foo\'; ?>'],
            ['<?php $query = "SELECT .... FROM my_table WHERE id <? LIMIT 1";', '<? $query = "SELECT .... FROM my_table WHERE id <? LIMIT 1";'],
            ['<?php

echo \'Foo\';

',
                '<?

echo \'Foo\';

',
            ],
            [
                "<?php if ('<?php' === '<?') { }",
                "<? if ('<?php' === '<?') { }",
            ],
            [
                '<?php // <?php',
                '<?pHP // <?php',
            ],
            [
                "<?php
'<?
';",
            ],
            [
                '<?php
// Replace all <? with <?php !',
            ],
            [
                '<?php
// Replace all <? with <?pHp !',
            ],
            [
                '<?php
/**
 * Convert <?= ?> to long-form <?php echo ?> and <?php ?> to <?php ?>
 *
 */',
            ],
            [
                "<?php \$this->data = preg_replace('/<\\?(?!xml|php)/s', '<?php ',       \$this->data);",
            ],
            [
                'foo <?php  echo "-"; echo "aaa <?php bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <?php echo "<? ";',
            ],
            [
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
            ],
            'binary string' => [
                '<?php echo b\'Foo\';',
                '<? echo b\'Foo\';',
            ],
            ['<?php', '<?'],
            ["<?php\n", "<?\n"],
            ["<?php    \n", "<?    \n"],
            ["<?php    \n?><?= 1?>", "<?    \n?><?= 1?>"],
            [
                'foo <?php  echo "-"; echo "aaa <? bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <?php echo "<? ";',
                'foo <?  echo "-"; echo "aaa <? bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <? echo "<? ";',
            ],
        ];
    }
}
