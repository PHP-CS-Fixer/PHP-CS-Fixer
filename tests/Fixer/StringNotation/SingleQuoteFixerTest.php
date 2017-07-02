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
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer
 */
final class SingleQuoteFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            [
                '<?php $a = \'\';',
                '<?php $a = "";',
            ],
            [
                '<?php $a = \'foo bar\';',
                '<?php $a = "foo bar";',
            ],
            [
                '<?php $a = \'foo
                    bar\';',
                '<?php $a = "foo
                    bar";',
            ],
            [
                '<?php $a = \'foo\'.\'bar\'."$baz";',
                '<?php $a = \'foo\'."bar"."$baz";',
            ],
            [
                '<?php $a = \'foo "bar"\';',
                '<?php $a = "foo \"bar\"";',
            ],
            [<<<'EOF'
<?php $a = '\\foo\\bar\\\\';
EOF
                , <<<'EOF'
<?php $a = "\\foo\\bar\\\\";
EOF
            ],
            [
                '<?php $a = \'foo $bar7\';',
                '<?php $a = "foo \$bar7";',
            ],
            [
                '<?php $a = \'foo $(bar7)\';',
                '<?php $a = "foo \$(bar7)";',
            ],
            [
                '<?php $a = \'foo \\\\($bar8)\';',
                '<?php $a = "foo \\\\(\$bar8)";',
            ],
            ['<?php $a = "foo \\" \\$$bar";'],
            ['<?php $a = "foo \'bar\'";'],
            ['<?php $a = "foo $bar";'],
            ['<?php $a = "foo ${bar}";'],
            ['<?php $a = "foo\n bar";'],
            [<<<'EOF'
<?php $a = "\\\n";
EOF
            ],
        ];
    }
}
