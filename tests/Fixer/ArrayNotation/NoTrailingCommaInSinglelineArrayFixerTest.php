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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array('<?php $x = array();'),
            array('<?php $x = array("foo");'),
            array(
                '<?php $x = array("foo");',
                '<?php $x = array("foo", );',
            ),
            array("<?php \$x = array(\n'foo', \n);"),
            array("<?php \$x = array('foo', \n);"),
            array("<?php \$x = array(array('foo'), \n);", "<?php \$x = array(array('foo',), \n);"),
            array("<?php \$x = array(array('foo',\n), \n);"),
            array(
                '<?php
    $test = array("foo", <<<TWIG
        foo
TWIG
        , $twig);',
                '<?php
    $test = array("foo", <<<TWIG
        foo
TWIG
        , $twig, );',
            ),
            array(
                '<?php
    $test = array(
        "foo", <<<TWIG
        foo
TWIG
        , $twig, );',
            ),
            array(
                '<?php
    $test = array("foo", <<<\'TWIG\'
        foo
TWIG
        , $twig);',
                '<?php
    $test = array("foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
            ),
            array(
                '<?php
    $test = array(
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, );',
            ),

            // Short syntax
            array('<?php $x = array([]);'),
            array('<?php $x = [[]];'),
            array('<?php $x = ["foo"];', '<?php $x = ["foo",];'),
            array('<?php $x = bar(["foo"]);', '<?php $x = bar(["foo",]);'),
            array("<?php \$x = bar([['foo'],\n]);"),
            array("<?php \$x = ['foo', \n];"),
            array('<?php $x = array([]);', '<?php $x = array([],);'),
            array('<?php $x = [[]];', '<?php $x = [[],];'),
            array('<?php $x = [$y[""]];', '<?php $x = [$y[""],];'),
            array(
                '<?php
    $test = ["foo", <<<TWIG
        foo
TWIG
        , $twig];',
                '<?php
    $test = ["foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
            ),
            array(
                '<?php
    $test = [
        "foo", <<<TWIG
        foo
TWIG
        , $twig, ];',
            ),
            array(
                '<?php
    $test = ["foo", <<<\'TWIG\'
        foo
TWIG
        , $twig];',
                '<?php
    $test = ["foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
            ),
            array(
                '<?php
    $test = [
        "foo", <<<\'TWIG\'
        foo
TWIG
        , $twig, ];',
            ),
        );
    }
}
