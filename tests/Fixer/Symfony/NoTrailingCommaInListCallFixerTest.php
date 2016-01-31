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
final class NoTrailingCommaInListCallFixerTest extends AbstractFixerTestCase
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
