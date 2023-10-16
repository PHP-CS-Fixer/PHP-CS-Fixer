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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer
 */
final class ArraySyntaxFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[array_syntax\] Invalid configuration: The option "a" does not exist\. Defined options are: "syntax"\.$#');

        $this->fixer->configure(['a' => 1]);
    }

    public function testFixWithDefaultConfiguration(): void
    {
        $this->fixer->configure([]);
        $this->doTest(
            '<?php $a = []; $b = [];',
            '<?php $a = []; $b = array();'
        );
    }

    /**
     * @dataProvider provideFixLongSyntaxCases
     */
    public function testFixLongSyntax(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['syntax' => 'long']);
        $this->doTest($expected, $input);
    }

    public static function provideFixLongSyntaxCases(): iterable
    {
        yield ['<?php $x = array();', '<?php $x = [];'];

        yield ['<?php $x = array(); $y = array();', '<?php $x = []; $y = [];'];

        yield ['<?php $x = array( );', '<?php $x = [ ];'];

        yield ['<?php $x = array(\'foo\');', '<?php $x = [\'foo\'];'];

        yield ['<?php $x = array( \'foo\' );', '<?php $x = [ \'foo\' ];'];

        yield ['<?php $x = array(($y ? true : false));', '<?php $x = [($y ? true : false)];'];

        yield ['<?php $x = array(($y ? array(true) : array(false)));', '<?php $x = [($y ? [true] : [false])];'];

        yield ['<?php $x = array(($y ? array(true) : array( false )));', '<?php $x = [($y ? [true] : [ false ])];'];

        yield ['<?php $x = array(($y ? array("t" => true) : array("f" => false)));', '<?php $x = [($y ? ["t" => true] : ["f" => false])];'];

        yield ['<?php print_r(array(($y ? true : false)));', '<?php print_r([($y ? true : false)]);'];

        yield ['<?php $x = array(array(array()));', '<?php $x = [[[]]];'];

        yield ['<?php $x = array(array(array())); $y = array(array(array()));', '<?php $x = [[[]]]; $y = [[[]]];'];

        yield ['<?php function(array $foo = array()) {};', '<?php function(array $foo = []) {};'];

        yield ['<?php $x = array(1, 2)[0];', '<?php $x = [1, 2][0];'];

        yield ['<?php $x[] = 1;'];

        yield ['<?php $x[ ] = 1;'];

        yield ['<?php $x[2] = 1;'];

        yield ['<?php $x["a"] = 1;'];

        yield ['<?php $x = func()[$x];'];

        yield ['<?php $x = "foo"[$x];'];

        yield ['<?php $text = "foo ${aaa[123]} bar $bbb[0] baz";'];

        yield ['<?php foreach ($array as [$x, $y]) {}'];

        yield ['<?php foreach ($array as $key => [$x, $y]) {}'];
    }

    /**
     * @dataProvider provideFixShortSyntaxCases
     */
    public function testFixShortSyntax(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['syntax' => 'short']);
        $this->doTest($expected, $input);
    }

    public static function provideFixShortSyntaxCases(): iterable
    {
        yield ['<?php $x = [];', '<?php $x = array();'];

        yield ['<?php $x = []; $y = [];', '<?php $x = array(); $y = array();'];

        yield ['<?php $x = [ ];', '<?php $x = array( );'];

        yield ['<?php $x = [\'foo\'];', '<?php $x = array(\'foo\');'];

        yield ['<?php $x = [ \'foo\' ];', '<?php $x = array( \'foo\' );'];

        yield ['<?php $x = [($y ? true : false)];', '<?php $x = array(($y ? true : false));'];

        yield ['<?php $x = [($y ? [true] : [false])];', '<?php $x = array(($y ? array(true) : array(false)));'];

        yield ['<?php $x = [($y ? [true] : [ false ])];', '<?php $x = array(($y ? array(true) : array( false )));'];

        yield ['<?php $x = [($y ? ["t" => true] : ["f" => false])];', '<?php $x = array(($y ? array("t" => true) : array("f" => false)));'];

        yield ['<?php print_r([($y ? true : false)]);', '<?php print_r(array(($y ? true : false)));'];

        yield ['<?php $x = [[[]]];', '<?php $x = array(array(array()));'];

        yield ['<?php $x = [[[]]]; $y = [[[]]];', '<?php $x = array(array(array())); $y = array(array(array()));'];

        yield ['<?php function(array $foo = []) {};', '<?php function(array $foo = array()) {};'];

        yield ['<?php function(array $foo) {};'];

        yield ['<?php function(array $foo = []) {};', '<?php function(array $foo = array()) {};'];

        yield ['<?php $a  =   [  ];', '<?php $a  =  array (  );'];
    }
}
