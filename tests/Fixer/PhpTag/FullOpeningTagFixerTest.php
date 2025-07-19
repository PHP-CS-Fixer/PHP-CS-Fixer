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
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer>
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

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php echo \'Foo\';', '<? echo \'Foo\';'];

        yield ['<?php echo \'Foo\';', '<?pHp echo \'Foo\';'];

        yield ['<?= \'Foo\';'];

        yield ['<?php echo \'Foo\'; ?> PLAIN TEXT'];

        yield ['PLAIN TEXT<?php echo \'Foo\'; ?>'];

        yield ['<?php $query = "SELECT .... FROM my_table WHERE id <? LIMIT 1";', '<? $query = "SELECT .... FROM my_table WHERE id <? LIMIT 1";'];

        yield ['<?php

echo \'Foo\';

',
            '<?

echo \'Foo\';

',
        ];

        yield [
            "<?php if ('<?php' === '<?') { }",
            "<? if ('<?php' === '<?') { }",
        ];

        yield [
            '<?php // <?php',
            '<?pHP // <?php',
        ];

        yield [
            "<?php
'<?
';",
        ];

        yield [
            '<?php
// Replace all <? with <?php !',
        ];

        yield [
            '<?php
// Replace all <? with <?pHp !',
        ];

        yield [
            '<?php
/**
 * Convert <?= ?> to long-form <?php echo ?> and <?php ?> to <?php ?>
 *
 */',
        ];

        yield [
            "<?php \$this->data = preg_replace('/<\\?(?!xml|php)/s', '<?php ',       \$this->data);",
        ];

        yield [
            'foo <?php  echo "-"; echo "aaa <?php bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <?php echo "<? ";',
        ];

        yield [
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
        ];

        yield 'binary string' => [
            '<?php echo b\'Foo\';',
            '<? echo b\'Foo\';',
        ];

        yield ['<?php', '<?'];

        yield ["<?php\n", "<?\n"];

        yield ["<?php    \n", "<?    \n"];

        yield ["<?php    \n?><?= 1?>", "<?    \n?><?= 1?>"];

        yield [
            'foo <?php  echo "-"; echo "aaa <? bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <?php echo "<? ";',
            'foo <?  echo "-"; echo "aaa <? bbb <? ccc"; echo \'<? \'; /* <? */ /** <? */ ?> bar <? echo "<? ";',
        ];
    }
}
