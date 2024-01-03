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
 * @covers \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer
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

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php

    $a = 0;
    echo 1;',
            '<?php
    $a = 0;
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
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

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
