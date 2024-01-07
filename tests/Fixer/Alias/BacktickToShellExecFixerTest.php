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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\BacktickToShellExecFixer
 */
final class BacktickToShellExecFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'plain' => [
            '<?php shell_exec("ls -lah");',
            '<?php `ls -lah`;',
        ];

        yield 'with variables' => [
            '<?php shell_exec("$var1 ls ${var2} -lah {$var3} file1.txt {$var4[0]} file2.txt {$var5->call()}");',
            '<?php `$var1 ls ${var2} -lah {$var3} file1.txt {$var4[0]} file2.txt {$var5->call()}`;',
        ];

        yield 'with single quote' => [
            <<<'EOT'
                <?php
                `echo a\'b`;
                `echo 'ab'`;
                EOT,
        ];

        yield 'with double quote' => [
            <<<'EOT'
                <?php
                `echo a\"b`;
                `echo 'a"b'`;
                EOT,
        ];

        yield 'with backtick' => [
            <<<'EOT'
                <?php
                `echo 'a\`b'`;
                `echo a\\\`b`;
                EOT,
        ];
    }
}
