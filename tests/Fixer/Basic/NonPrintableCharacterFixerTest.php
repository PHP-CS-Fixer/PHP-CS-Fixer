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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Ivan Boprzenkov <ivan.borzenkov@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer
 */
final class NonPrintableCharacterFixerTest extends AbstractFixerTestCase
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
        return array(
            array(
                '<?php echo "Hello World !";',
                '<?php echo "'.pack('CCC', 0xe2, 0x80, 0x8b).'Hello'.pack('CCC', 0xe2, 0x80, 0x87).'World'.pack('CC', 0xc2, 0xa0).'!";',
            ),
            array(
                '<?php echo "Hello World !";',
                '<?php echo "'.
                    pack('CCC', 0xe2, 0x80, 0x8b).
                    pack('CCC', 0xe2, 0x80, 0x8b).
                    pack('CCC', 0xe2, 0x80, 0x8b).
                    pack('CCC', 0xe2, 0x80, 0x8b).
                    pack('CCC', 0xe2, 0x80, 0x8b).
                    pack('CCC', 0xe2, 0x80, 0x8b).
                'Hello World !";',
            ),
            array(
                '<?php
// echo
echo "Hello World !";',
                '<?php
// ec'.pack('CCC', 0xe2, 0x80, 0x8b).'ho
echo "Hello'.pack('CCC', 0xe2, 0x80, 0xaf).'World'.pack('CC', 0xc2, 0xa0).'!";',
            ),
            array(
                '<?php

                /**
                 * @param string $p Param
                 */
                function f(string $p)
                {
                    echo $p;
                }',
                '<?php

                /**
                 * @param '.pack('CCC', 0xe2, 0x80, 0x8b).'string $p Param
                 */
                function f(string $p)
                {
                    echo $p;
                }',
            ),
            array(
                '<?php echo "$a[0] ${a}";',
                '<?php echo "$a'.pack('CCC', 0xe2, 0x80, 0x8b).'[0]'.pack('CCC', 0xe2, 0x80, 0x8b).' ${a'.pack('CCC', 0xe2, 0x80, 0x8b).'}";',
            ),
            array(
                '<?php echo \'12345\';?>abc<?php ?>',
                '<?php echo \'123'.pack('CCC', 0xe2, 0x80, 0x8b).'45\';?>a'.pack('CCC', 0xe2, 0x80, 0x8b).'bc<?php ?>',
            ),
        );
    }
}
