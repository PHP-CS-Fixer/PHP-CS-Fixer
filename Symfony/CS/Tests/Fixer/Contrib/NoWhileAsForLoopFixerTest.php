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

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class NoWhileAsForLoopFixerTest extends AbstractFixerTestBase
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
            array(
                '<?php
                    while(  $a>1) {
                        echo $a;
                    }
                ',
                '<?php
                    for( ; $a>1;) {
                        echo $a;
                    }
                ',
            ),
            array(
                '<?php while($a>1) echo $a;',
                '<?php for(;$a>1;) echo $a;',
            ),
            array(
                '<?php while(/**/$a>1
//
) echo $a;
                ',
                '<?php for(/**/;$a>1;
//
) echo $a;
                ',
            ),
            array(
                '<?php for($b=1;$a>1;) echo $a;',
            ),
            array(
                '<?php for(;$a>1;++$b) echo $a;',
            ),
            array(
                '<?php for($c=1;$a>1;++$b) echo $a;',
            ),
            array(
                '<?php
                    while(true) {
                        echo 1;
                        break;
                    }
                ',
                '<?php
                    for(;;) {
                        echo 1;
                        break;
                    }
                ',
            ),
            array(
                '<?php
                    while(true /* */) {
                        echo 1;
                        break;
                    }
                ',
                '<?php
                    for(; /* */;) {
                        echo 1;
                        break;
                    }
                ',
            ),
        );
    }
}
