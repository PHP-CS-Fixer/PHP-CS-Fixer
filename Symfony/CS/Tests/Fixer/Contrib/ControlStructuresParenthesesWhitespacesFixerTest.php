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
 * @author Denis Platov <d.platov@owox.com>
 *
 * @internal
 */
final class ControlStructuresParenthesesWhitespacesFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string $expected
     * @param null   $input
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php if ($test == true) {}',
                '<?php if($test == true){}',
            ),
            array(
                '<?php for ($i=1; $i<4; $i++) {}',
                '<?php for($i=1; $i<4; $i++){}',
            ),
            array(
                '<?php foreach ($items as $key => $value) {}',
                '<?php foreach($items as $key => $value){}',
            ),
            array(
                '<?php if (true) {} elseif (false) {}',
                '<?php if(true){} elseif(false){}',
            ),
            array(
                '<?php do {} while (true);',
                '<?php do{} while(true);',
            ),
            array(
                '<?php while (true) {}',
                '<?php while(true){}',
            ),
        );
    }
}
