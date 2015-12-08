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

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @internal
 */
final class ClassDefinitionFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        $cases = array(
            array(
                '<?php
class Aaa implements
    \RFb,
    \Fcc, '.'
\GFddZz
{
}',
                '<?php
class Aaa implements
    \RFb,
    \Fcc, \GFddZz
{
}',
            ),
            array(
                '<?php
class Aaa implements
    Symfony\CS\Tests\Fixer,
\RFb,
    \Fcc1, '.'
\GFdd
{
}',
                '<?php
class Aaa implements
    Symfony\CS\Tests\Fixer,\RFb,
    \Fcc1, \GFdd
{
}',
            ),
            array(
                '<?php
interface Test extends /*a*/ /*b*/
TestInterface1, /* test */
    TestInterface2, // test
    '.'

// test
TestInterface3, /**/     '.'
TestInterface4,
      TestInterface5, /**/
TestInterface6    {}',
                '<?php
interface Test
extends
  /*a*/    /*b*/TestInterface1   ,  /* test */
    TestInterface2   ,   // test
    '.'

// test
TestInterface3, /**/     TestInterface4   ,
      TestInterface5    ,     '.'
        /**/TestInterface6    {}',
            ),
            array(
                '<?php
class Test extends TestInterface8 implements /*a*/ /*b*/
TestInterface1, /* test */
    TestInterface2, // test
    '.'

// test
TestInterface3, /**/     '.'
TestInterface4,
      TestInterface5, /**/
TestInterface6
{
}',
                '<?php
class Test
extends
    TestInterface8
  implements  /*a*/    /*b*/TestInterface1   ,  /* test */
    TestInterface2   ,   // test
    '.'

// test
TestInterface3, /**/     TestInterface4   ,
      TestInterface5    ,    '.'
        /**/TestInterface6
{
}',
            ),
            array(
                '<?php
class /**/ Test123 extends /**/ \RuntimeException implements TestZ{
}',
                '<?php
class/**/Test123
extends  /**/        \RuntimeException    implements

TestZ{
}',
            ),
            array(
                '<?php
class /**/ Test125 //aaa
extends /*

*/
//
\Exception //
{}',
                '<?php
class/**/Test125 //aaa
extends  /*

*/
//
\Exception        //
{}',
            ),
            array(
                '<?php
class Test124 extends \Exception {}',
                '<?php
class
Test124

extends
\Exception {}',
            ),
            array(
                '<?php
class Aaa implements Fbb, Ccc
{
}',
            ),
            array(
                '<?php
    class Aaa implements Ebb, Ccc
    {
    }',
            ),
            array(
                '<?php
class Aaa implements \Dbb, Ccc
{
}',
            ),
            array(
                '<?php
class Aaa implements Cbb, \Ccc
{
}',
            ),
            array(
                '<?php
class Aaa implements \CFb, \Ccc
{
}',
            ),
            array(
                '<?php
if (1) {
    class IndentedClass
    {
    }
}',
            ),
            array(
                '<?php
namespace {
    class IndentedNameSpacedClass
    {
    }
}',
            ),
            array(
                '<?php
class Aaa implements
    \CFb,
    \Ccc,
    \CFdd
{
}', ),
        );

        return $cases;
    }

    /**
     * @dataProvider provide54Cases
     */
    public function testFix54($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provide54Cases()
    {
        if (!defined('T_TRAIT')) {
            $this->markTestSkipped('Test requires traits.');
        }

        return array(
            array(
            '<?php
trait traitTest
{}

trait /**/ traitTest2 //
/**/ {}',
            '<?php
trait
   traitTest
{}

trait/**/traitTest2//
/**/ {}',
            ),
        );
    }
}
