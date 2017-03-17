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

namespace PhpCsFixer\Tests\Fixer\Naming;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Victor Melnik <melnikvictorl@gmail.com>
 *
 * @internal
 */
final class SnakeCaseToCamelCaseFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string $expected
     * @param null   $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return array(
            // should not fix
            array('<?php $variable = 1;'),
            // should fix
            array(
                '<?php $camelCase = 1;',
                '<?php $camel_case = 1;',
            ),
            array(
                '<?php $camelCase = 1;',
                '<?php $Camel_case = 1;',
            ),
        );
    }
}
