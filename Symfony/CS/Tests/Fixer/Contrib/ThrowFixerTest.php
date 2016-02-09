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
 * @author Lucas Michot <lucas@semalead.com>
 */
class ThrowFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '
$a = $a;
throw $a;',
            ),
            array(
                '<?php
$a = $a;

throw $a;',
                '<?php
$a = $a; throw $a;',
            ),
            array(
                '<?php
$b = $b;

throw $b;',
                '<?php
$b = $b;throw $b;',
            ),
            array(
                '<?php
$c = $c;

throw $c;',
                '<?php
$c = $c;
throw $c;',
            ),
            array(
                '<?php
    $d = $d;

    throw $d;',
                '<?php
    $d = $d;
    throw $d;',
            ),
            array(
                '<?php
    if (true) {
        throw new \Exception();
    }',
            ),
            array(
                '<?php
    if (true)
        throw new \Exception();
    ',
            ),
            array(
                '<?php
    if (true) {
        throw new \Exception();
    } else {
        throw new \Exception();
    }',
            ),
            array(
                '<?php
    if (true)
        throw new \Exception();
    else
        throw new \Exception();
    ',
            ),
            array(
                '<?php
    if (true) {
        throw new \Exception();
    } elseif (false) {
        throw new \Exception();
    }',
            ),
            array(
                '<?php
    if (true)
        throw new \Exception();
    elseif (false)
        throw new \Exception();
    ',
            ),
            array(
                '<?php
    throw new Exception("throw new \Exception();");',
            ),
            array(
                '<?php
    function foo()
    {
        // comment
        throw new \Exception();
    }',
            ),
            array(
                '<?php
    function foo()
    {
        // comment

        throw new \Exception();
    }',
            ),
        );
    }
}
