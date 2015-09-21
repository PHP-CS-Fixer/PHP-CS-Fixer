<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Konrad Cerny <info@konradcerny.cz>
 */
class OneLineMethodArgumentFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php function xyz(
    $a=10,
    $b=20, //comment2
    $c=30
) {}',
                '<?php function xyz(
    $a=10, $b=20, //comment2
    $c=30) {}',
            ),
            array(
                '<?php function xyz(
    $a=10,
    $b=20 /* comment2 */,
    $c=30
) {}',
                '<?php function xyz(
    $a=10, $b=20 /* comment2 */, $c=30) {}',
            ),
            array('<?php
function functionName(
    \SomeClass $first,
    $second
) {',
                '<?php
function functionName(
    \SomeClass $first, $second
) {', ),
            array('<?php
function functionName(
    ClassTypeHint $arg1,
    &$arg2,
    array $arg3 = [],
    $third
) {',
                '<?php
function functionName(
    ClassTypeHint $arg1, &$arg2, array $arg3 = [],
    $third
) {', ),
            array(
                '<?php
function functionName(
    \SomeClass $first,
    $second,
    /** comment */ $third
) {',
                '<?php
function functionName(
    \SomeClass $first, $second,
    /** comment */ $third
) {', ),
            array(
                '<?php function applyOptions(
    RequestInterface $request,
    array $options = array(),
    $flags = self::OPTIONS_NONE
) {}',
                '<?php function applyOptions(
    RequestInterface $request, array $options = array(), $flags = self::OPTIONS_NONE) {}',
            ),
            array(
                '<?php class SomeClass
{
public function applyOptions(
    RequestInterface $request,
    array $options = array(),
    $flags = self::OPTIONS_NONE
) {}',
                '<?php class SomeClass
{
public function applyOptions(
    RequestInterface $request, array $options = array(), $flags = self::OPTIONS_NONE) {}',
            ),
            array(
                '<?php
$var = function(
    $first,
    $second
) {}',
                '<?php
$var = function(
    $first, $second
) {}',
            ),
            array(
                '<?php
$var = function(
    $first,
    $second
) use($third) {}',
                '<?php
$var = function(
    $first, $second
) use($third) {}',
            ),
            array(
                '<?php function functionName (
    1,
    \SomeClass $a
) {}',
                '<?php function functionName (
    1, \SomeClass $a
) {}',
            ),
            array(
                '<?php function functionName(
    $first,
    $second
) {}',
                '<?php function functionName($first,
                $second) {}',
            ),
            array(
                '<?php function functionName(
    $first, // comment
    $second
) {}',
                '<?php function functionName($first, // comment
                $second) {}',
            ),
            array(
                '<?php function functionName(
    1,
    \SomeClass $a
) {}',
                '<?php function functionName(1,
    \SomeClass $a
) {}',
            ),
            array('<?php function functionName() {}'),
            array('<?php function functionName($first) {}'),
            array('<?php function functionName($first, $second) {}'),
            array('<?php function functionName($first, \ClassName $second) {}'),
            array('<?php function functionName($first, /* comment */ $second) {}'),
            array('<?php function openFile($open_mode = \'r\', $use_include_path = false, $context = null) {}'),
            array('<?php $var = function($first, $second)'),
            array('<?php $var = function($first, $second) use ($third) {}'),
            array('<?php plus($a, $b) {}'),
        );
    }
}
