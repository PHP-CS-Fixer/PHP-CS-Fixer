<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Ceeram <ceeram@cakephp.org>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class NewlineAfterOpenTagFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @requires PHP 5.4
     * @dataProvider provideCases54
     */
    public function testFix54($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php $foo = true; ?>',
            ),
            array(
                '<?php $foo = true; ?>
',
            ),
            array(
                '<?php


$foo = true;
?>',
            ),
            array(
                '<?php
$foo = true;
$bar = false;
?>',
                '<?php $foo = true;
$bar = false;
?>',
            ),
            array(
                '<?php $foo = true; ?>
Html here
<?php $bar = false; ?>',
            ),
        );
    }

    public function provideCases54()
    {
        return array(
            array(
                '<?= $bar;
$foo = $bar;
?>',
            ),
        );
    }
}
