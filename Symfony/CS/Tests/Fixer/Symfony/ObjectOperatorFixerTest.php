<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
final class ObjectOperatorFixerTest extends AbstractFixerTestBase
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
                '<?php $object->method();',
                '<?php $object   ->method();',
            ),
            array(
                '<?php $object->method();',
                '<?php $object   ->   method();',
            ),
            array(
                '<?php $object->method();',
                '<?php $object->   method();',
            ),
            array(
                '<?php $object->method();',
                '<?php $object	->method();',
            ),
            array(
                '<?php $object->method();',
                '<?php $object->	method();',
            ),
            array(
                '<?php $object->method();',
                '<?php $object	->	method();',
            ),
            array(
                '<?php $object->method();',
            ),
            array(
                '<?php echo "use it as -> you want";',
            ),
            // Ensure that doesn't break chained multi-line statements
            array(
                '<?php $object->method()
                        ->method2()
                        ->method3();',
            ),
            array(
                '<?php $this
             ->add()
             // Some comment
             ->delete();',
            ),
        );
    }
}
