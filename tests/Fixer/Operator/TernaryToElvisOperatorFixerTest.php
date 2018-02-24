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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gabriel Caruso <carusogabriel34@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\TernaryToElvisOperatorFixer
 */
final class TernaryToElvisOperatorFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return [
            ['<?php $foo = $bar ?  : $bax;'],
            ["<?php \$foo = \$bar ? 'bar' : \$bax;"],
            'Common fix case.' => [
                '<?php $foo = $bar ?  : $bax;',
                '<?php $foo = $bar ? $bar : $bax;',
            ],
            'Array index fix case.' => [
                "<?php \$foo = \$bar['quux'] ?  : \$bax;",
                "<?php \$foo = \$bar['quux'] ? \$bar['quux'] : \$bax;",
            ],
            'Minimal number of tokens case.' => [
                '<?php
$foo=$bar?:$bax;',
                '<?php
$foo=$bar?$bar:$bax;',
            ],
            'With comments case.' => [
                '<?php $foo = $bar /* foo */ ? /* bar */  : $baz;',
                '<?php $foo = $bar /* foo */ ? /* bar */ $bar : $baz;',
            ],
            'With arguments in different lines.' => [
                '<?php $foo = $bar
? '.'
: $baz;',
                '<?php $foo = $bar
? $bar
: $baz;',
],
            'With arguments in dirrefent lines with comments.' => [
                '<?php $foo = $bar // check
?  // value if true
: $baz; // value if false',
                '<?php $foo = $bar // check
? $bar // value if true
: $baz; // value if false',
            ],
        ];
    }
}
