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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer>
 *
 * @author Ceeram <ceeram@cakephp.org>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
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

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
$a = function(){
                    echo 1;
                };',
            '<?php $a = function(){
                    echo 1;
                };',
        ];

        yield [
            '<?php $foo = true; ?>',
        ];

        yield [
            '<?php $foo = true; ?>
',
        ];

        yield [
            '<?php


$foo = true;
?>',
        ];

        yield [
            '<?php
$foo = true;
$bar = false;
?>',
            '<?php $foo = true;
$bar = false;
?>',
        ];

        yield [
            '<?php $foo = true; ?>
Html here
<?php $bar = false; ?>',
        ];

        yield [
            '<?= $bar;
$foo = $bar;
?>',
        ];

        yield [
            str_replace("\n", "\r\n", '<?php
// linebreak already present in file with Windows line endings
'),
        ];

        yield 'file with shebang' => [
            <<<'EOD'
                #!x
                <?php
                echo 1;
                echo 2;
                EOD,
            <<<'EOD'
                #!x
                <?php echo 1;
                echo 2;
                EOD,
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php\r\n\$foo = true;\n",
            "<?php \$foo = true;\n",
        ];
    }
}
