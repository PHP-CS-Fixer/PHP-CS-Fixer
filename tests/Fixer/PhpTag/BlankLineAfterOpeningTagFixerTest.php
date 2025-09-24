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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer>
 *
 * @covers \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer
 *
 * @author Ceeram <ceeram@cakephp.org>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class BlankLineAfterOpeningTagFixerTest extends AbstractFixerTestCase
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

    echo 1;',
            '<?php
    echo 1;',
        ];

        yield [
            '<?php

    $b = 2;
    echo 3;',
            '<?php     $b = 2;
    echo 3;',
        ];

        yield [
            '<?php
    '.'
    $c = 4;
    echo 5;',
        ];

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
            '<?php

 class SomeClass
 {
     const VERSION = "1.1.1";
     const FOO = "bar";
 }
',
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
            '<?php
$foo = true;
?>',
        ];

        yield [
            '<?php

$foo = true;
$bar = false;
',
            '<?php $foo = true;
$bar = false;
',
        ];

        yield [
            '<?php

$foo = true;
?>
Html here
<?php $bar = false;',
        ];

        yield [
            '<?php
$foo = true;
?>
Html here
<?php $bar = false;
',
        ];

        yield [
            '<?= $bar;
$foo = $bar;
?>',
        ];

        yield 'empty file with open tag without new line' => [
            '<?php',
        ];

        yield 'empty file with open tag with new line' => [
            "<?php\n",
        ];

        yield 'file with shebang' => [
            <<<'EOD'
                #!x
                <?php

                echo 1;
                EOD,
            <<<'EOD'
                #!x
                <?php
                echo 1;
                EOD,
        ];

        yield 'file starting with multi-line comment' => [
            <<<'PHP'
                <?php

                /**
                 * @author yes
                 */
                PHP,
            <<<'PHP'
                <?php
                /**
                 * @author yes
                 */
                PHP,
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
            "<?php\r\n\r\n\$foo = true;\r\n",
            "<?php \$foo = true;\r\n",
        ];

        yield [
            "<?php\r\n\r\n\$foo = true;\r\n",
            "<?php\r\n\$foo = true;\r\n",
        ];
    }
}
