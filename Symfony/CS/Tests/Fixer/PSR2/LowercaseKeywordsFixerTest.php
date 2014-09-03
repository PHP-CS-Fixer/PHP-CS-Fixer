<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class LowercaseKeywordsFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = (1 and 2);', '<?php $x = (1 AND 2);'),
            array('<?php foreach(array(1, 2, 3) as $val) {}', '<?php foreach(array(1, 2, 3) AS $val) {}'),
            array('<?php echo "GOOD AS NEW";'),
        );
    }
}
