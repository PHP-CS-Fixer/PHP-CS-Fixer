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
        );
    }
}
