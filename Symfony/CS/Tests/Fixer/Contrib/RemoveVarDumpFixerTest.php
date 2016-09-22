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
 * @author Andrew Kovalyov <andrew.kovalyoff@gmail.com>
 *
 * @internal
 */
final class RemoveVarDumpFixerTest extends AbstractFixerTestBase
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
            array('<?php echo "This should not be changed";'),
            array(
                '<?php echo "This should be changed";',
                '<?php echo "This should be changed"; var_dump(true);'
            ),
            array(
                '<?php 
                $a = 1;
                $b = 1;',

                '<?php 
                $a = 1;
                var_dump($a);
                $b = 1;
                dump($b);',
            ),
        );
    }
}