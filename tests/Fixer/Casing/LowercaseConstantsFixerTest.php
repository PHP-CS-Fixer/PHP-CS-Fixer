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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\LowercaseConstantsFixer
 */
final class LowercaseConstantsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideGeneratedCases
     */
    public function testFixGeneratedCases($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideGeneratedCases()
    {
        $cases = array();
        foreach (array('true', 'false', 'null') as $case) {
            $cases[] = array(
                sprintf('<?php $x = %s;', $case),
                sprintf('<?php $x = %s;', strtoupper($case)),
            );

            $cases[] = array(
                sprintf('<?php $x = %s;', $case),
                sprintf('<?php $x = %s;', ucfirst($case)),
            );

            $cases[] = array(sprintf('<?php $x = new %s;', ucfirst($case)));
            $cases[] = array(sprintf('<?php $x = new %s;', strtoupper($case)));
            $cases[] = array(sprintf('<?php $x = "%s story";', $case));
            $cases[] = array(sprintf('<?php $x = "%s";', $case));
        }

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
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
            array('<?php echo $null;'),
            array('<?php $x = False::foo();'),
            array('<?php namespace Foo\Null;'),
            array('<?php use Foo\Null;'),
            array('<?php use Foo\Null as Null;'),
            array('<?php class True {} class False {} class Null {}'),
            array('<?php class Foo extends True {}'),
            array('<?php class Foo implements False {}'),
            array('<?php Class Null { use True; }'),
            array('<?php interface True {}'),
            array('<?php $foo instanceof True; $foo instanceof False; $foo instanceof Null;'),
            array(
                '<?php
        class Foo
        {
            const TRUE = 1;
            const FALSE = 2;
            const NULL = null;
        }',
            ),
            array('<?php $x = new /**/False?>'),
            array('<?php Null/**/::test();'),
            array('<?php True//
                                    ::test();'),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix54Cases
     * @requires PHP 5.4
     */
    public function testFix54($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix54Cases()
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
