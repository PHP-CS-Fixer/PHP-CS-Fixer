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
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer
 */
final class SingleQuoteFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases(): array
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
                '<?php $a = b\'\';',
                '<?php $a = b"";',
            ],
            [
                '<?php $a = B\'\';',
                '<?php $a = B"";',
            ],
            [
                '<?php $a = \'foo bar\';',
                '<?php $a = "foo bar";',
            ],
            [
                '<?php $a = b\'foo bar\';',
                '<?php $a = b"foo bar";',
            ],
            [
                '<?php $a = B\'foo bar\';',
                '<?php $a = B"foo bar";',
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
            [
                <<<'EOF'
<?php $a = '\\foo\\bar\\\\';
EOF
                ,
                <<<'EOF'
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
            ['<?php $a = b"foo \\" \\$$bar";'],
            ['<?php $a = B"foo \\" \\$$bar";'],
            ['<?php $a = "foo \'bar\'";'],
            ['<?php $a = b"foo \'bar\'";'],
            ['<?php $a = B"foo \'bar\'";'],
            ['<?php $a = "foo $bar";'],
            ['<?php $a = b"foo $bar";'],
            ['<?php $a = B"foo $bar";'],
            ['<?php $a = "foo ${bar}";'],
            ['<?php $a = b"foo ${bar}";'],
            ['<?php $a = B"foo ${bar}";'],
            ['<?php $a = "foo\n bar";'],
            ['<?php $a = b"foo\n bar";'],
            ['<?php $a = B"foo\n bar";'],
            [<<<'EOF'
<?php $a = "\\\n";
EOF
            ],
        ];
    }

    /**
     * @dataProvider provideTestSingleQuoteFixCases
     */
    public function testSingleQuoteFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'strings_containing_single_quote_chars' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideTestSingleQuoteFixCases(): array
    {
        return [
            [
                '<?php $a = \'foo \\\'bar\\\'\';',
                '<?php $a = "foo \'bar\'";',
            ],
            [
                <<<'EOT'
<?php
// none
$a = 'start \' end';
// one escaped baskslash
$b = 'start \\\' end';
// two escaped baskslash
$c = 'start \\\\\' end';
EOT
                ,
                <<<'EOT'
<?php
// none
$a = "start ' end";
// one escaped baskslash
$b = "start \\' end";
// two escaped baskslash
$c = "start \\\\' end";
EOT
                ,
            ],
            [
                <<<'EOT'
<?php
// one unescaped backslash
$a = "start \' end";
// one escaped + one unescaped baskslash
$b = "start \\\' end";
EOT
                ,
            ],
        ];
    }
}
