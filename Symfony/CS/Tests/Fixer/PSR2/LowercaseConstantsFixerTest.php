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

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class LowercaseConstantsFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provide54Cases
     * @requires PHP 5.4
     */
    public function test54($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
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
            array(
                '<?php if (true) if (false) if (null) {}',
                '<?php if (TRUE) if (FALSE) if (NULL) {}',
            ),
            array(
                '<?php if (!true) if (!false) if (!null) {}',
                '<?php if (!TRUE) if (!FALSE) if (!NULL) {}',
            ),
            array(
                '<?php if ($a == true) if ($a == false) if ($a == null) {}',
                '<?php if ($a == TRUE) if ($a == FALSE) if ($a == NULL) {}',
            ),
            array(
                '<?php if ($a === true) if ($a === false) if ($a === null) {}',
                '<?php if ($a === TRUE) if ($a === FALSE) if ($a === NULL) {}',
            ),
            array(
                '<?php if ($a != true) if ($a != false) if ($a != null) {}',
                '<?php if ($a != TRUE) if ($a != FALSE) if ($a != NULL) {}',
            ),
            array(
                '<?php if ($a !== true) if ($a !== false) if ($a !== null) {}',
                '<?php if ($a !== TRUE) if ($a !== FALSE) if ($a !== NULL) {}',
            ),
            array(
                '<?php if (true && true and true AND true || false or false OR false xor null XOR null) {}',
                '<?php if (TRUE && TRUE and TRUE AND TRUE || FALSE or FALSE OR FALSE xor NULL XOR NULL) {}',
            ),
            array(
                '<?php /* foo */ true; /** bar */ false;',
                '<?php /* foo */ TRUE; /** bar */ FALSE;',
            ),
            array('<?php class True {} class False {}, class Null {}'),
            array('<?php class Foo extends True {}'),
            array('<?php class Foo implements False {}'),
            array('<?php Class Null { use True; }'),
            array('<?php interface True {}'),
            array('<?php $foo instanceof True; $foo instanceof False; $foo instanceof Null;'),
            array(
                '<?php
    class Foo
    {
        const TRUE;
        const FALSE;
        const NULL;
    }',
            ),
        );
    }

    public function provide54Cases()
    {
        return array(
            array('<?php trait False {}'),
            array(
                '<?php
    class Null {
        use True, False {
            False::bar insteadof True;
            True::baz insteadof False;
            False::baz as Null;
        }
    }',
            ),
        );
    }
}
