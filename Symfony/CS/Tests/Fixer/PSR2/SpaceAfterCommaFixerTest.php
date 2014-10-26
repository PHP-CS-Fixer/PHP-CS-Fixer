<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Fixer;
use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 */
class SpaceAfterCommaFixerTest extends AbstractFixerTestBase
{
    /**
     * @covers Symfony\CS\Fixer\SpaceAfterComma::fix
     */
    public function testSpaceAfterComma()
    {
        $this->makeTest(
            '<?php function xyz ($a=10, b=20, c=30) {
            }',
            '<?php function xyz ($a=10,b=20,c=30) {
            }'
        );

        $this->makeTest(
            '<?php array($a=10, b=20, c=30);',
            '<?php array($a=10,b=20,c=30);'
        );

        $this->makeTest(
            '<?php array($a=10, b=20, c=30);',
            '<?php array($a=10 , b=20 ,c=30);'
        );
    }
}
