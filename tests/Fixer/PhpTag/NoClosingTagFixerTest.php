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
 * @covers \PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer
 */
final class NoClosingTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideWithFullOpenTagCases
     */
    public function testWithFullOpenTag(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideWithShortOpenTagCases
     */
    public function testWithShortOpenTag(string $expected, ?string $input = null): void
    {
        if (!\ini_get('short_open_tag')) {
            static::markTestSkipped('The short_open_tag option is required to be enabled.');
        }

        $this->doTest($expected, $input);
    }

    public static function provideWithFullOpenTagCases(): array
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

    public static function provideWithShortOpenTagCases(): array
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
