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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class JoinFunctionFixerTest extends AbstractFixerTestBase
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
            // valid cases
            array('<?php $foo->join($a);'),
            array('<?php joinFoo($a);'),
            array('<?php foo_join($a);'),
            array('<?php new join($a);'),
            array('<?php new Foo\join($a);'),
            array('<?php Foo::join($a);'),
            array('<?php new join\bar($a);'),
            array('<?php join::bar($a);'),
            array('<?php join\bar($a);'),
            array('<?php \join($a);'),
            array('<?php "INSERT ... join($a) ...";'),
            array('<?php "INSERT ... JOIN($a) ...";'),
            array("<?php 'please'.'join' . 'me';"),
            array('<?php "please" . "join"."me";'),

            // cases to fix
            array(
                '<?php implode($a, $b);',
                '<?php join($a, $b);',
            ),
            array(
                '<?php $a = &implode($a, $b);',
                '<?php $a = &join($a, $b);',
            ),
            array(
                '<?php implode
                            ($a);',
                '<?php join
                            ($a);',
            ),
            array(
                '<?php /* foo */ implode /** bar */ ($a);',
                '<?php /* foo */ join /** bar */ ($a);',
            ),
            array(
                '<?php a(implode());',
                '<?php a(join());',
            ),
            array(
                '<?php
class Joining
{
    public function join(QueryBuilder $qb, $join)
    {
        //definition
    }
}',
            ),
        );
    }
}
