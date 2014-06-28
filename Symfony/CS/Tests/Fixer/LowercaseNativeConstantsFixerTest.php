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

use Symfony\CS\Fixer\LowercaseNativeConstantsFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class LowercaseNativeConstantsFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = new \SplFileInfo(__FILE__);

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = true;', '<?php $x = true;'),
            array('<?php $x = true;', '<?php $x = True;'),
            array('<?php $x = true;', '<?php $x = TruE;'),
            array('<?php $x = true;', '<?php $x = TRUE;'),
            array('<?php $x = false;', '<?php $x = false;'),
            array('<?php $x = false;', '<?php $x = False;'),
            array('<?php $x = false;', '<?php $x = FalsE;'),
            array('<?php $x = false;', '<?php $x = FALSE;'),
            array('<?php $x = null;', '<?php $x = null;'),
            array('<?php $x = null;', '<?php $x = Null;'),
            array('<?php $x = null;', '<?php $x = NulL;'),
            array('<?php $x = null;', '<?php $x = NULL;'),
            array('<?php $x = "true story";', '<?php $x = "true story";'),
            array('<?php $x = "false";', '<?php $x = "false";'),
            array('<?php $x = "that is null";', '<?php $x = "that is null";'),
        );
    }
}
