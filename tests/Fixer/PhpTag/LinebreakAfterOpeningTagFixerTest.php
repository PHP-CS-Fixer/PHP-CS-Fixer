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
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Ceeram <ceeram@cakephp.org>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer
 */
final class LinebreakAfterOpeningTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            [
                '<?php
$a = function(){
                    echo 1;
                };',
                '<?php $a = function(){
                    echo 1;
                };',
            ],
            [
                '<?php $foo = true; ?>',
            ],
            [
                '<?php $foo = true; ?>
',
            ],
            [
                '<?php


$foo = true;
?>',
            ],
            [
                '<?php
$foo = true;
$bar = false;
?>',
                '<?php $foo = true;
$bar = false;
?>',
            ],
            [
                '<?php $foo = true; ?>
Html here
<?php $bar = false; ?>',
            ],
            [
                '<?= $bar;
$foo = $bar;
?>',
            ],
            [
                str_replace("\n", "\r\n", '<?php
// linebreak already present in file with Windows line endings
'),
            ],
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): array
    {
        return [
            [
                "<?php\r\n\$foo = true;\n",
                "<?php \$foo = true;\n",
            ],
        ];
    }
}
