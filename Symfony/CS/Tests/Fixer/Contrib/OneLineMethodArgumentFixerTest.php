<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

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
public function functionName(
    \SomeClass $first,
    $second
) {',
                '<?php
public function functionName(
    \SomeClass $first, $second
) {', ),
            array('<?php
public function functionName(
    ClassTypeHint $arg1,
    &$arg2,
    array $arg3 = [],
    $third
) {',
                '<?php
public function functionName(
    ClassTypeHint $arg1, &$arg2, array $arg3 = [],
    $third
) {', ),
            array(
                '<?php
public function functionName(
    \SomeClass $first,
    $second,
    /** comment */ $third
) {',
                '<?php
public function functionName(
    \SomeClass $first, $second,
    /** comment */ $third
) {', ),
            array(
                '<?php public function applyOptions(
    RequestInterface $request,
    array $options = array(),
    $flags = self::OPTIONS_NONE
) {}',
                '<?php public function applyOptions(
    RequestInterface $request, array $options = array(), $flags = self::OPTIONS_NONE) {}',
            ),
            array('<?php public function functionName() {}'),
            array('<?php public function functionName($first) {}'),
            array('<?php public function functionName($first, $second) {}'),
            array('<?php public function functionName($first, \ClassName $second) {}'),
            array('<?php public function functionName($first, /* comment */ $second) {}'),
            array('<?php public function openFile($open_mode = \'r\', $use_include_path = false, $context = null) {}'),
        );
    }
}
