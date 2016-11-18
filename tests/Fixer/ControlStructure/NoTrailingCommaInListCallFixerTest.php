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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class NoTrailingCommaInListCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
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
                '<?php
    list($a, $b) = foo();
    list($a, , $c, $d) = foo();
    list($a, , $c) = foo();
    list($a) = foo();
    list($a , $b) = foo();
    list($a, /* $b */, $c) = foo();
',
                '<?php
    list($a, $b) = foo();
    list($a, , $c, $d, ) = foo();
    list($a, , $c, , ) = foo();
    list($a, , , , , ) = foo();
    list($a , $b , ) = foo();
    list($a, /* $b */, $c, ) = foo();
',
            ),
        );
    }
}
