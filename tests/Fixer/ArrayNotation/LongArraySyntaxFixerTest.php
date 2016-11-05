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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class LongArraySyntaxFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = array();', '<?php $x = [];'),
            array('<?php $x = array(); $y = array();', '<?php $x = []; $y = [];'),
            array('<?php $x = array( );', '<?php $x = [ ];'),
            array('<?php $x = array(\'foo\');', '<?php $x = [\'foo\'];'),
            array('<?php $x = array( \'foo\' );', '<?php $x = [ \'foo\' ];'),
            array('<?php $x = array(($y ? true : false));', '<?php $x = [($y ? true : false)];'),
            array('<?php $x = array(($y ? array(true) : array(false)));', '<?php $x = [($y ? [true] : [false])];'),
            array('<?php $x = array(($y ? array(true) : array( false )));', '<?php $x = [($y ? [true] : [ false ])];'),
            array('<?php $x = array(($y ? array("t" => true) : array("f" => false)));', '<?php $x = [($y ? ["t" => true] : ["f" => false])];'),
            array('<?php print_r(array(($y ? true : false)));', '<?php print_r([($y ? true : false)]);'),
            array('<?php $x = array(array(array()));', '<?php $x = [[[]]];'),
            array('<?php $x = array(array(array())); $y = array(array(array()));', '<?php $x = [[[]]]; $y = [[[]]];'),
            array('<?php function(array $foo = array()) {};', '<?php function(array $foo = []) {};'),
            array('<?php $x = array(1, 2)[0];', '<?php $x = [1, 2][0];'),
            array('<?php $x[] = 1;'),
            array('<?php $x[ ] = 1;'),
            array('<?php $x[2] = 1;'),
            array('<?php $x["a"] = 1;'),
            array('<?php $x = func()[$x];'),
            array('<?php $x = "foo"[$x];'),
            array('<?php $text = "foo ${aaa[123]} bar $bbb[0] baz";'),
        );
    }
}
