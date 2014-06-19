<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\LowercaseKeywordsFixer;

class LowercaseKeywordsFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input)
    {
        $fixer = new LowercaseKeywordsFixer();
        $file = new \SplFileInfo(__FILE__);

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = (1 and 2);', '<?php $x = (1 AND 2);'),
            array('<?php foreach(array(1, 2, 3) as $val) {}', '<?php foreach(array(1, 2, 3) AS $val) {}'),
            array('<?php echo "GOOD AS NEW";', '<?php echo "GOOD AS NEW";'),
        );
    }
}
