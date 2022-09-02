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
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer
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

    public function provideFixCases(): array
    {
        return [
            ['<?php $x = array();'],
            ['<?php $x = array("foo");'],
            [
                '<?php $x = array("foo");',
                '<?php $x = array("foo", );',
            ],
            ["<?php \$x = array(\n'foo', \n);"],
            ["<?php \$x = array('foo', \n);"],
            ["<?php \$x = array(array('foo'), \n);", "<?php \$x = array(array('foo',), \n);"],
            ["<?php \$x = array(array('foo',\n), \n);"],
            [
                '<?php
    $test = array("foo", <<<TWIG
        foo
TWIG
        , $twig, );',
            ],
            [
                '<?php
    $test = array(
        "foo", <<<TWIG
        foo
TWIG
        , $twig, );',
            ],
            [
                '<?php
    $test = array("foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
            ],
            [
                '<?php
    $test = array(
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
            ],

            // Short syntax
            ['<?php $x = array([]);'],
            ['<?php $x = [[]];'],
            ['<?php $x = ["foo"];', '<?php $x = ["foo",];'],
            ['<?php $x = bar(["foo"]);', '<?php $x = bar(["foo",]);'],
            ["<?php \$x = bar([['foo'],\n]);"],
            ["<?php \$x = ['foo', \n];"],
            ['<?php $x = array([]);', '<?php $x = array([],);'],
            ['<?php $x = [[]];', '<?php $x = [[],];'],
            ['<?php $x = [$y[""]];', '<?php $x = [$y[""],];'],
            [
                '<?php
    $test = ["foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
            ],
            [
                '<?php
    $test = [
        "foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
            ],
            [
                '<?php
    $test = ["foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
            ],
            [
                '<?php
    $test = [
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
            ],
            [
                '<?php $x = array(...$foo);',
                '<?php $x = array(...$foo, );',
            ],
            [
                '<?php $x = [...$foo];',
                '<?php $x = [...$foo, ];',
            ],
        ];
    }
}
