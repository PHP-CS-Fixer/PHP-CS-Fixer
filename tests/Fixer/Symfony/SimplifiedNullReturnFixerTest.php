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

namespace PhpCsFixer\Tests\Fixer\Symfony;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class SimplifiedNullReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
    {
        return array(
            // check correct statements aren't changed
            array('<?php return;'),
            array('<?php return  ;'),
            array('<?php return \'null\';'),
            array('<?php return false;'),
            array('<?php return (false );'),
            array('<?php return null === foo();'),
            array('<?php return array() == null ;'),

            // check we modified those that can be changed
            array('<?php return;', '<?php return'.' null;'),
            array('<?php return;', '<?php return'.' (null);'),
            array('<?php return;', '<?php return'.' ( null    );'),
            array('<?php return;', '<?php return'.' ( (( null)));'),
            array('<?php return /* hello */;', '<?php return /* hello */'.' null  ;'),
            array('<?php return;', '<?php return NULL;'),
        );
    }
}
