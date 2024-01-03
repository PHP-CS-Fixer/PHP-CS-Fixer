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

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, null|string, array{syntax?: string}}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'default configuration' => [
            '<?php $a = []; $b = [];',
            '<?php $a = []; $b = array();',
            [],
        ];

        yield [
            '<?php $x = array();',
            '<?php $x = [];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(); $y = array();',
            '<?php $x = []; $y = [];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array( );',
            '<?php $x = [ ];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(\'foo\');',
            '<?php $x = [\'foo\'];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array( \'foo\' );',
            '<?php $x = [ \'foo\' ];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(($y ? true : false));',
            '<?php $x = [($y ? true : false)];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(($y ? array(true) : array(false)));',
            '<?php $x = [($y ? [true] : [false])];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(($y ? array(true) : array( false )));',
            '<?php $x = [($y ? [true] : [ false ])];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(($y ? array("t" => true) : array("f" => false)));',
            '<?php $x = [($y ? ["t" => true] : ["f" => false])];',
            ['syntax' => 'long'], ];

        yield [
            '<?php print_r(array(($y ? true : false)));',
            '<?php print_r([($y ? true : false)]);',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(array(array()));',
            '<?php $x = [[[]]];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(array(array())); $y = array(array(array()));',
            '<?php $x = [[[]]]; $y = [[[]]];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php function(array $foo = array()) {};',
            '<?php function(array $foo = []) {};',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = array(1, 2)[0];',
            '<?php $x = [1, 2][0];',
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x[] = 1;',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x[ ] = 1;',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x[2] = 1;',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x["a"] = 1;',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = func()[$x];',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = "foo"[$x];',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $text = "foo ${aaa[123]} bar $bbb[0] baz";',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php foreach ($array as [$x, $y]) {}',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php foreach ($array as $key => [$x, $y]) {}',
            null,
            ['syntax' => 'long'],
        ];

        yield [
            '<?php $x = [];',
            '<?php $x = array();',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = []; $y = [];',
            '<?php $x = array(); $y = array();',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [ ];',
            '<?php $x = array( );',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [\'foo\'];',
            '<?php $x = array(\'foo\');',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [ \'foo\' ];',
            '<?php $x = array( \'foo\' );',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [($y ? true : false)];',
            '<?php $x = array(($y ? true : false));',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [($y ? [true] : [false])];',
            '<?php $x = array(($y ? array(true) : array(false)));',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [($y ? [true] : [ false ])];',
            '<?php $x = array(($y ? array(true) : array( false )));',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [($y ? ["t" => true] : ["f" => false])];',
            '<?php $x = array(($y ? array("t" => true) : array("f" => false)));',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php print_r([($y ? true : false)]);',
            '<?php print_r(array(($y ? true : false)));',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [[[]]];',
            '<?php $x = array(array(array()));',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $x = [[[]]]; $y = [[[]]];',
            '<?php $x = array(array(array())); $y = array(array(array()));',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php function(array $foo = []) {};',
            '<?php function(array $foo = array()) {};',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php function(array $foo) {};',
            null,
            ['syntax' => 'short'],
        ];

        yield [
            '<?php function(array $foo = []) {};',
            '<?php function(array $foo = array()) {};',
            ['syntax' => 'short'],
        ];

        yield [
            '<?php $a  =   [  ];',
            '<?php $a  =  array (  );',
            ['syntax' => 'short'],
        ];
    }
}
