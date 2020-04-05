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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\NoTrailingWhitespaceInStringFixer
 */
final class NoTrailingWhitespaceInStringFixerTest extends AbstractFixerTestCase
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
        return [
            [
                "<?php \$a = ' foo\r bar\r\n\nbaz\n  ';",
                "<?php \$a = ' foo  \r bar \r\n  \nbaz  \n  ';",
            ],
            [
                "<?php \$a = \" foo\r bar\r\n\nbaz\n  \";",
                "<?php \$a = \" foo  \r bar \r\n  \nbaz  \n  \";",
            ],
            [
                "<?php \$a = \"  \$foo\n\";",
                "<?php \$a = \"  \$foo  \n\";",
            ],
            [
                " foo\r bar\r\nbaz\n",
                " foo  \r bar \r\nbaz  \n  ",
            ],
            [
                "\n<?php foo() ?>\n  foo",
                "  \n<?php foo() ?>  \n  foo",
            ],
            [
                "<?php foo() ?>\n<?php foo() ?>\n",
                "<?php foo() ?>  \n<?php foo() ?>  \n",
            ],
            [
                "<?php foo() ?>\n\nfoo",
                "<?php foo() ?>\n  \nfoo",
            ],
            [
                "<?php foo() ?>foo\n",
                "<?php foo() ?>foo  \n",
            ],
            [
                '',
                ' ',
            ],
            [
                '<?php echo 1; ?>',
                '<?php echo 1; ?> ',
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @requires PHP 7.3
     */
    public function testFix73()
    {
        $expected = '
<?php
    $a = <<<\'EOD\'
      foo
    bar

       baz
    EOD;
';
        $input = '
<?php
    $a = <<<\'EOD\'
      foo  '.'
    bar
        '.'
       baz  '.'
    EOD;
';

        $this->doTest($expected, $input);
    }
}
