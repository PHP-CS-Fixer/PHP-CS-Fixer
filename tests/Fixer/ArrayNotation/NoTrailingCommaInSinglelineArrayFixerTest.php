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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer>
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
final class NoTrailingCommaInSinglelineArrayFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php $x = array();'];

        yield ['<?php $x = array("foo");'];

        yield [
            '<?php $x = array("foo");',
            '<?php $x = array("foo", );',
        ];

        yield ["<?php \$x = array(\n'foo', \n);"];

        yield ["<?php \$x = array('foo', \n);"];

        yield ["<?php \$x = array(array('foo'), \n);", "<?php \$x = array(array('foo',), \n);"];

        yield ["<?php \$x = array(array('foo',\n), \n);"];

        yield [
            '<?php
    $test = array("foo", <<<TWIG
        foo
TWIG
        , $twig, );',
        ];

        yield [
            '<?php
    $test = array(
        "foo", <<<TWIG
        foo
TWIG
        , $twig, );',
        ];

        yield [
            '<?php
    $test = array("foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
        ];

        yield [
            '<?php
    $test = array(
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
        ];

        // Short syntax
        yield ['<?php $x = array([]);'];

        yield ['<?php $x = [[]];'];

        yield ['<?php $x = ["foo"];', '<?php $x = ["foo",];'];

        yield ['<?php $x = bar(["foo"]);', '<?php $x = bar(["foo",]);'];

        yield ["<?php \$x = bar([['foo'],\n]);"];

        yield ["<?php \$x = ['foo', \n];"];

        yield ['<?php $x = array([]);', '<?php $x = array([],);'];

        yield ['<?php $x = [[]];', '<?php $x = [[],];'];

        yield ['<?php $x = [$y[""]];', '<?php $x = [$y[""],];'];

        yield [
            '<?php
    $test = ["foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
        ];

        yield [
            '<?php
    $test = [
        "foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
        ];

        yield [
            '<?php
    $test = ["foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
        ];

        yield [
            '<?php
    $test = [
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
        ];

        yield [
            '<?php $x = array(...$foo);',
            '<?php $x = array(...$foo, );',
        ];

        yield [
            '<?php $x = [...$foo];',
            '<?php $x = [...$foo, ];',
        ];
    }
}
