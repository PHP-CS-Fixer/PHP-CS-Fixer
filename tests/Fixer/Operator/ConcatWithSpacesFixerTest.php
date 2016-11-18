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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ConcatWithSpacesFixerTest extends AbstractFixerTestCase
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
        return array(
            array(
                '<?php
                    $a =   //
                    $c .   /**/
                    $d     #
                    . $e   /**  */
                    . $f . //
                    $z;
                ',
                '<?php
                    $a =   //
                    $c   .   /**/
                    $d     #
                    .   $e   /**  */
                    .   $f   . //
                    $z;
                ',
            ),
            array(
                '<?php $foo = "a" . \'b\' . "c" . "d" . $e . ($f + 1);',
                '<?php $foo = "a" . \'b\' ."c". "d"    .  $e.($f + 1);',
            ),
            array(
                '<?php $foo = "a" .
"b";',
                '<?php $foo = "a".
"b";',
            ),
            array(
                '<?php $a = "foobar"
    . "baz";',
                '<?php $a = "foobar"
    ."baz";',
            ),
            array(
                '<?php echo $a . $b;
                    echo $d . $e .   //
                        $f;
                    echo $a . $b?>
                 <?php
                    echo $c;
                ',
                '<?php echo $a.$b;
                    echo $d    .            $e          .   //
                        $f;
                    echo $a   .                  $b?>
                 <?php
                    echo $c;
                ',
            ),
        );
    }
}
