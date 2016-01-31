<?php

/*
 * This file is part of the PHP CS Fixer.
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
 * @author Matteo Beccati <matteo@beccati.com>
 */
class EregToPregFixerTest extends AbstractFixerTestBase
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
            array('<?php $x = 1;'),
            array('<?php $x = "ereg";'),

            array('<?php $x = ereg("[A-Z]"."foo", $m);'),

            array('<?php $x = ereg("^*broken", $m);'),

            array('<?php $x = Foo::split("[A-Z]", $m);'),
            array('<?php $x = $foo->split("[A-Z]", $m);'),

            array('<?php $x = preg_match(\'/[A-Z]/D\');', '<?php $x = ereg(\'[A-Z]\');'),
            array('<?php $x = preg_match(\'/[A-Z]/D\', $m);', '<?php $x = ereg(\'[A-Z]\', $m);'),

            array('<?php $x = preg_match("/[A-Z]/D", $m);', '<?php $x = ereg("[A-Z]", $m);'),
            array('<?php $x = preg_match("/[A-Z]/Di", $m);', '<?php $x = eregi("[A-Z]", $m);'),
            array('<?php $x = preg_match("#/[AZ]#D", $m);', '<?php $x = ereg("/[AZ]", $m);'),
            array('<?php $x = preg_match("#[AZ]/#D", $m);', '<?php $x = ereg("[AZ]/", $m);'),
            array('<?php $x = preg_match("!#[A]/!D", $m);', '<?php $x = ereg("#[A]/", $m);'),
            array('<?php $x = preg_match("!##[A\!]//!D", $m);', '<?php $x = ereg("##[A!]//", $m);'),
            array('<?php $x = preg_match("/##[A!!]\/\//D", $m);', '<?php $x = ereg("##[A!!]//", $m);'),
            array('<?php $x = preg_match("#\#\#[A!!]///#D", $m);', '<?php $x = ereg("##[A!!]///", $m);'),

            array('<?php $x = preg_replace("/[A-Z]/D", "", $m);', '<?php $x = ereg_replace("[A-Z]", "", $m);'),
            array('<?php $x = preg_replace("/[A-Z]/Di", "", $m);', '<?php $x = eregi_replace("[A-Z]", "", $m);'),
            array('<?php $x = preg_split("/[A-Z]/D", $m);', '<?php $x = split("[A-Z]", $m);'),
            array('<?php $x = preg_split("/[A-Z]/Di", $m);', '<?php $x = spliti("[A-Z]", $m);'),
        );
    }
}
