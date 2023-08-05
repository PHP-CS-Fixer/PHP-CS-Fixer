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
            self::markTestSkipped('The short_open_tag option is required to be enabled.');
        }

        $this->doTest($expected, $input);
    }

    public static function provideWithFullOpenTagCases(): iterable
    {
        yield [
            '<?php echo \'Foo\';',
            '<?php echo \'Foo\'; ?>',
        ];

        yield [
            '<?php echo \'Foo\';',
            '<?php echo \'Foo\';?>',
        ];

        yield [
            '<?php echo \'Foo\'; ?> PLAIN TEXT',
        ];

        yield [
            'PLAIN TEXT<?php echo \'Foo\'; ?>',
        ];

        yield [
            '<?php

echo \'Foo\';',
            '<?php

echo \'Foo\';

?>',
        ];

        yield [
            '<?php echo \'Foo\'; ?>
<p><?php echo \'this is a template\'; ?></p>
<?php echo \'Foo\'; ?>',
        ];

        yield [
            '<?php echo "foo";',
            '<?php echo "foo" ?>',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
if (true) {
    echo "Here I am!";
}',
            '<?php
if (true) {
    echo "Here I am!";
}?>',
        ];

        yield 'Trailing linebreak, priority issue with SingleBlankLineAtEofFixer.' => [
            '<?php echo 1;',
            "<?php echo 1;\n?>\n",
        ];

        yield 'Trailing comment.' => [
            '<?php echo 1;// test',
            "<?php echo 1;// test\n?>",
        ];

        yield 'No code' => [
            '<?php ',
            '<?php ?>',
        ];

        yield 'No code, only comment' => [
            '<?php /* license */',
            '<?php /* license */ ?>',
        ];

        yield [
            '<?php ?>aa',
        ];
    }

    public static function provideWithShortOpenTagCases(): iterable
    {
        yield [
            '<? echo \'Foo\';',
            '<? echo \'Foo\'; ?>',
        ];

        yield [
            '<? echo \'Foo\';',
            '<? echo \'Foo\';?>',
        ];

        yield [
            '<? echo \'Foo\'; ?>
<p><? echo \'this is a template\'; ?></p>
<? echo \'Foo\'; ?>',
        ];

        yield [
            '<? /**/', '<? /**/?>',
        ];

        yield [
            '<?= "somestring"; ?> <?= "anotherstring"; ?>',
        ];

        yield [
            '<?= 1;',
            '<?= 1; ?>',
        ];
    }
}
