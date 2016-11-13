<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @internal
 */
final class ClassDefinitionWhitespacesFixerTest extends AbstractFixerTestCase
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
        return array(
            array(
                '<?php
interface Test extends TestInterface, /* test */
    TestInterface2, // test
     TestInterface3, /**/ TestInterface3,
      TestInterface4
{}
'
            ,
                '<?php
interface Test extends    TestInterface   ,  /* test */
    TestInterface2   ,   // test
     TestInterface3, /**/     TestInterface3,
      TestInterface4
{}
',
            ),
            array(
                 '<?php
class Abc {}
'
            ,
                 '<?php
class    Abc    {}
',
             ),
            array(
                '<?php
class Test extends \Exception
             implements TestInterface
  {
}
'
            ,
                '<?php
class Test   extends     \Exception
             implements  TestInterface       '.'
  {
}
',
            ),
            array(
                '<?php
interface Test extends TestInterface, /* */ TestInterface2 {
}
'
            ,
                '<?php
interface Test extends TestInterface  ,  /* */   TestInterface2   {
}
',
            ),
        );
    }
}
