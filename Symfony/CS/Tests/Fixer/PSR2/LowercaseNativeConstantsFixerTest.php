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
class LowercaseNativeConstantsFixerTest extends AbstractFixerTestBase
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
            array('<?php $x = true;'),
            array('<?php $x = true;', '<?php $x = True;'),
            array('<?php $x = true;', '<?php $x = TruE;'),
            array('<?php $x = true;', '<?php $x = TRUE;'),
            array('<?php $x = false;'),
            array('<?php $x = false;', '<?php $x = False;'),
            array('<?php $x = false;', '<?php $x = FalsE;'),
            array('<?php $x = false;', '<?php $x = FALSE;'),
            array('<?php $x = null;'),
            array('<?php $x = null;', '<?php $x = Null;'),
            array('<?php $x = null;', '<?php $x = NulL;'),
            array('<?php $x = null;', '<?php $x = NULL;'),
            array('<?php $x = "true story";'),
            array('<?php $x = "false";'),
            array('<?php $x = "that is null";'),
            array('<?php $x = new True;'),
            array('<?php $x = new True();'),
            array('<?php $x = False::foo();'),
            array('<?php namespace Foo\Null;'),
            array('<?php use Foo\Null;'),
            array('<?php use Foo\Null as Null;'),
        );
    }
}
