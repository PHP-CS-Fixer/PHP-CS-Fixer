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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\NoTrailingWhitespaceInStringFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\StringNotation\NoTrailingWhitespaceInStringFixer>
 *
 * @author Gregor Harlan
 */
final class NoTrailingWhitespaceInStringFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            "<?php \$a = ' foo\r bar\r\n\nbaz\n  ';",
            "<?php \$a = ' foo  \r bar \r\n  \nbaz  \n  ';",
        ];

        yield [
            "<?php \$a = \" foo\r bar\r\n\nbaz\n  \";",
            "<?php \$a = \" foo  \r bar \r\n  \nbaz  \n  \";",
        ];

        yield [
            "<?php \$a = \"  \$foo\n\";",
            "<?php \$a = \"  \$foo  \n\";",
        ];

        yield [
            " foo\r bar\r\nbaz\n",
            " foo  \r bar \r\nbaz  \n  ",
        ];

        yield [
            "\n<?php foo() ?>\n  foo",
            "  \n<?php foo() ?>  \n  foo",
        ];

        yield [
            "<?php foo() ?>\n<?php foo() ?>\n",
            "<?php foo() ?>  \n<?php foo() ?>  \n",
        ];

        yield [
            "<?php foo() ?>\n\nfoo",
            "<?php foo() ?>\n  \nfoo",
        ];

        yield [
            "<?php foo() ?>foo\n",
            "<?php foo() ?>foo  \n",
        ];

        yield [
            '',
            ' ',
        ];

        yield [
            '<?php echo 1; ?>',
            '<?php echo 1; ?> ',
        ];

        yield [
            '
<?php
$a = <<<EOD
  foo
bar
  $a
$b

   baz
EOD;
                ',
            '
<?php
$a = <<<EOD
  foo  '.'
bar
  $a '.'
$b
    '.'
   baz  '.'
EOD;
                ',
        ];

        yield [
            '
<?php
$a = <<<\'EOD\'
  foo
bar

   baz
EOD;
                ',
            '
<?php
$a = <<<\'EOD\'
  foo  '.'
bar
    '.'
   baz  '.'
EOD;
                ',
        ];

        yield [
            '
<?php
    $a = <<<\'EOD\'
      foo
    bar

       baz
    EOD;
',
            '
<?php
    $a = <<<\'EOD\'
      foo  '.'
    bar
        '.'
       baz  '.'
    EOD;
',
        ];

        yield 'binary string' => [
            "<?php \$a = b\"  \$foo\n\";",
            "<?php \$a = b\"  \$foo  \n\";",
        ];
    }
}
