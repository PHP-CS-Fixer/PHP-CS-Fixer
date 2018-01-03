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
            'plain' => [
                '<?php shell_exec("ls -lah");',
                '<?php `ls -lah`;',
            ],
            'with double quote' => [
<<<'EOT'
<?php
shell_exec("ls -lah a\\\"m\\\\z");
EOT
,
<<<'EOT'
<?php
`ls -lah a\"m\\\\z`;
EOT
,
            ],
            'with variables' => [
                '<?php shell_exec("$var1 ls ${var2} -lah {$var3} file1.txt {$var4[0]} file2.txt {$var5->call()}");',
                '<?php `$var1 ls ${var2} -lah {$var3} file1.txt {$var4[0]} file2.txt {$var5->call()}`;',
            ],
            'with double quote and variable' => [
<<<'EOT'
<?php
shell_exec("ls -lah a\\\"m\\\\z $var");
EOT
,
<<<'EOT'
<?php
`ls -lah a\"m\\\\z $var`;
EOT
,
            ],
            'with backtick' => [
<<<'EOT'
<?php
shell_exec("echo 'foo`bar'");
EOT
,
<<<'EOT'
<?php
`echo 'foo\`bar'`;
EOT
,
            ],
        ];
    }
}
