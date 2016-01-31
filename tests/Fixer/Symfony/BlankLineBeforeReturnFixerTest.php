<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Symfony;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class BlankLineBeforeReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '
$a = $a;
return $a;',
            ),
            array(
                '<?php
$a = $a;

return $a;',
                '<?php
$a = $a; return $a;',
            ),
            array(
                '<?php
$b = $b;

return $b;',
                '<?php
$b = $b;return $b;',
            ),
            array(
                '<?php
$c = $c;

return $c;',
                '<?php
$c = $c;
return $c;',
            ),
            array(
                '<?php
    $d = $d;

    return $d;',
                '<?php
    $d = $d;
    return $d;',
            ),
            array(
                '<?php
    if (true) {
        return 1;
    }',
            ),
            array(
                '<?php
    if (true)
        return 1;
    ',
            ),
            array(
                '<?php
    if (true) {
        return 1;
    } else {
        return 2;
    }',
            ),
            array(
                '<?php
    if (true)
        return 1;
    else
        return 2;
    ',
            ),
            array(
                '<?php
    if (true) {
        return 1;
    } elseif (false) {
        return 2;
    }',
            ),
            array(
                '<?php
    if (true)
        return 1;
    elseif (false)
        return 2;
    ',
            ),
            array(
                '<?php
    throw new Exception("return true;");',
            ),
            array(
                '<?php
    function foo()
    {
        // comment
        return "foo";
    }',
            ),
            array(
                '<?php
    function foo()
    {
        // comment

        return "bar";
    }',
            ),
        );
    }
}
