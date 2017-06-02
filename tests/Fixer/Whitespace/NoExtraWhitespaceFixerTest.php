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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Tristan Strathearn <r3oath@gmail.com>
 *
 * @internal
 */
final class NoExtraWhitespaceFixerTest extends AbstractFixerTestCase
{
    /**
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
$a = 1;',
                '<?php
$a  =   1;',
            ),
            array(
                '<?php
if ( $foo = 9 && $bar != false) { return; }',
                '<?php
if  ( $foo  = 9 &&  $bar  != false) { return; }',
            ),
            array(
                '<?php
$something = array(
    "foo" => "bar",
    "bar" => "yoghurt",
);',
                '<?php
$something = array(
    "foo"   => "bar",
    "bar"   =>    "yoghurt",
);',
            ),
            array(
                '<?php
$a = 1; // Comment with    whitespace
return;',
                '<?php
$a  =   1; // Comment with    whitespace
return;',
            ),
            array(
                "<?php\r\n \$a = 1; // Comment with    whitespace\r\n",
                "<?php\r\n \$a     =    1; // Comment with    whitespace\r\n",
            ),
            array(
                "<?php \$a = 1; // Comment with    whitespace\n",
                "<?php \$a  =   1; // Comment with    whitespace\n",
            ),
            array(
                '<?php
if ($var == 5 ) {
    return $something; //    extra   whitespace!!!
}',
                '<?php
if ($var  == 5  ) {
    return  $something; //    extra   whitespace!!!
}',
            ),
            array(
                '<?php require_once "foo.php" ;',
                '<?php require_once   "foo.php"  ;',
            ),
            array(
                '<?php $a * -$b;',
                '<?php $a   *  -$b;',
            ),
            array(
                '<?php  $a = -2/ +5;',
                '<?php   $a =   -2/  +5;',
            ),
            array(
                '<?php $a = &$b;',
                '<?php $a  = &$b;',
            ),
            array(
                '<?php  $a++ + $b;',
                '<?php  $a++   +  $b;',
            ),
            array(
                '<?php __LINE__ - 1; // foo bar    !',
                '<?php __LINE__       -     1; // foo bar    !',
            ),
            array(
                '<?php  `echo     1` +1;',
                '<?php   `echo     1`     +1;',
            ),
        );
    }
}
