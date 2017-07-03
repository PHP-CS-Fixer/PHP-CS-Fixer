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
 * @covers \PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer
 */
final class NoClosingTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCasesWithFullOpenTag
     */
    public function testCasesWithFullOpenTag($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCasesWithShortOpenTag
     */
    public function testCasesWithShortOpenTag($expected, $input = null)
    {
        if (!ini_get('short_open_tag')) {
            $this->markTestSkipped('The short_open_tag option is required to be enabled.');

            return;
        }

        $this->doTest($expected, $input);
    }

    public function provideCasesWithFullOpenTag()
    {
        return [
            [
                '<?php echo \'Foo\';',
                '<?php echo \'Foo\'; ?>',
            ],
            [
                '<?php echo \'Foo\';',
                '<?php echo \'Foo\';?>',
            ],
            [
                '<?php echo \'Foo\'; ?> PLAIN TEXT',
            ],
            [
                'PLAIN TEXT<?php echo \'Foo\'; ?>',
            ],
            [
                '<?php

echo \'Foo\';',
                '<?php

echo \'Foo\';

?>',
            ],
            [
                '<?php echo \'Foo\'; ?>
<p><?php echo \'this is a template\'; ?></p>
<?php echo \'Foo\'; ?>',
            ],
            [
                '<?php echo "foo";',
                '<?php echo "foo" ?>',
            ],
            [
                '<?php
class foo
{
    public function bar()
    {
        echo "Here I am!";
    }
}',
                '<?php
class foo
{
    public function bar()
    {
        echo "Here I am!";
    }
}?>',
            ],
            [
                '<?php
function bar()
{
    echo "Here I am!";
}',
                '<?php
function bar()
{
    echo "Here I am!";
}?>',
            ],
            [
                '<?php
if (true) {
    echo "Here I am!";
}',
                '<?php
if (true) {
    echo "Here I am!";
}?>',
            ],
            'Trailing linebreak, priority issue with SingleBlankLineAtEofFixer.' => [
                '<?php echo 1;',
                "<?php echo 1;\n?>\n",
            ],
            'Trailing comment.' => [
                '<?php echo 1;// test',
                "<?php echo 1;// test\n?>",
            ],
            'No code' => [
                '<?php ',
                '<?php ?>',
            ],
            'No code, only comment' => [
                '<?php /* license */',
                '<?php /* license */ ?>',
            ],
            [
                '<?php ?>aa',
            ],
        ];
    }

    public function provideCasesWithShortOpenTag()
    {
        return [
            [
                '<? echo \'Foo\';',
                '<? echo \'Foo\'; ?>',
            ],
            [
                '<? echo \'Foo\';',
                '<? echo \'Foo\';?>',
            ],
            [
                '<? echo \'Foo\'; ?>
<p><? echo \'this is a template\'; ?></p>
<? echo \'Foo\'; ?>',
            ],
            [
                '<? /**/', '<? /**/?>',
            ],
            [
                '<?= "somestring"; ?> <?= "anotherstring"; ?>',
            ],
            [
                '<?= 1;',
                '<?= 1; ?>',
            ],
        ];
    }
}
