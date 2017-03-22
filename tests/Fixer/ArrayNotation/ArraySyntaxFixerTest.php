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
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Gregor Harlan <gharlan@web.de>
 * @author SpacePossum
 *
 * @internal
 */
final class ArraySyntaxFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfiguration()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[array_syntax\] Invalid configuration: The option "a" does not exist\. (Known|Defined) options are: "syntax"\.$#'
        );

        $this->fixer->configure(array('a' => 1));
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.
     */
    public function testLegacyFixWithDefaultConfiguration()
    {
        $this->fixer->configure(null);
        $this->doTest(
            '<?php $a = array(); $b = array();',
            '<?php $a = array(); $b = [];'
        );
    }

    public function testFixWithDefaultConfiguration()
    {
        $this->fixer->configure(array());
        $this->doTest(
            '<?php $a = array(); $b = array();',
            '<?php $a = array(); $b = [];'
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideLongSyntaxCases
     */
    public function testFixLongSyntax($expected, $input = null)
    {
        $this->fixer->configure(array('syntax' => 'long'));
        $this->doTest($expected, $input);
    }

    public function provideLongSyntaxCases()
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

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 5.4
     * @dataProvider provideShortSyntaxCases
     */
    public function testFixShortSyntax($expected, $input = null)
    {
        $this->fixer->configure(array('syntax' => 'short'));
        $this->doTest($expected, $input);
    }

    public function provideShortSyntaxCases()
    {
        return array(
            array('<?php $x = [];', '<?php $x = array();'),
            array('<?php $x = []; $y = [];', '<?php $x = array(); $y = array();'),
            array('<?php $x = [ ];', '<?php $x = array( );'),
            array('<?php $x = [\'foo\'];', '<?php $x = array(\'foo\');'),
            array('<?php $x = [ \'foo\' ];', '<?php $x = array( \'foo\' );'),
            array('<?php $x = [($y ? true : false)];', '<?php $x = array(($y ? true : false));'),
            array('<?php $x = [($y ? [true] : [false])];', '<?php $x = array(($y ? array(true) : array(false)));'),
            array('<?php $x = [($y ? [true] : [ false ])];', '<?php $x = array(($y ? array(true) : array( false )));'),
            array('<?php $x = [($y ? ["t" => true] : ["f" => false])];', '<?php $x = array(($y ? array("t" => true) : array("f" => false)));'),
            array('<?php print_r([($y ? true : false)]);', '<?php print_r(array(($y ? true : false)));'),
            array('<?php $x = [[[]]];', '<?php $x = array(array(array()));'),
            array('<?php $x = [[[]]]; $y = [[[]]];', '<?php $x = array(array(array())); $y = array(array(array()));'),
            array('<?php function(array $foo = []) {};', '<?php function(array $foo = array()) {};'),
            array('<?php function(array $foo) {};'),
            array('<?php function(array $foo = []) {};', '<?php function(array $foo = array()) {};'),
            array('<?php $a  =   [  ];', '<?php $a  =  array (  );'),
        );
    }
}
