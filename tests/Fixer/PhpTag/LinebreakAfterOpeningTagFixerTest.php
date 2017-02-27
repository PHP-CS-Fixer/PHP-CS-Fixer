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

namespace PhpCsFixer\Tests\Fixer\PhpTag;

use PhpCsFixer\Test\AbstractFixerTestCase;
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
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
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php\r\n\$foo = true;\n",
                "<?php \$foo = true;\n",
            ],
        ];
    }
}
