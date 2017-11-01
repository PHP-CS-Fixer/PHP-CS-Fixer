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

namespace PhpCsFixer\tests\Fixer\Phpdoc;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Jonathan Daigle
 *
 * @internal
 */
final class SingleBlankLineBeforePhpdocFixerTest extends AbstractFixerTestCase
{
    public function providePhpdocExample()
    {
        $return = array();

        // All valid cases
        $return[] = array('
<?php
/**
 * I am a file doc-block
 */

/** @var array $var */
$var1 = 1;

if (true) {
    /** @var int $var2 */
    $var2 = 1;
} else if (false) {
    /** @var int $var3 */
    $var3 = 1;
} 
else 
{
    /** @var int $var4 */
    $var4 = 1;
}

/** @var int $key */
/** @var int $value */
foreach ($var2 as $key => $value) {
    /** @var string $bob */
    $bob = (string) $key + $value;
}

/**
 * I am a class dockblock
 */
class MyClass
{
    /**
     * I am the first property of a class
     *
     * @var int
     */
     public $prop1;
     
     /**
      * @return void
      */
     public function foo()
     {
         /** @var bool $bar */
         $bar = false;
     }
}
');
        $return[] = array("#!/bin/php\n<?php\n/**\n * File Doc\n */");
        $return[] = array("#!/usr/bin/env php\n<?php\n/**\n * File Doc\n */");

        // Test that a NL is added if docblock start on same line a previous token.
        $return[] = array(
            "<?php \n/** @var int \$foo */\n\$foo=1;\n\n/** @var bool \$bar */\n",
            "<?php /** @var int \$foo */\n\$foo=1; /** @var bool \$bar */\n",
        );

        $return[] = array(
'<?php
/**
 * File Doc
 */
 
/**
 * Class Doc
 */
abstract class MyClass {
    /**
     * PropertyDoc
     */
     public $prop1;

    /**
     * PropertyDoc2
     *
     * @var int
     */
     public static $prop2;

     /**
      * Function Doc
      */
      static public function myMethod()
      {
        $this->prop2 = 1;
      }

      /**
       * Function Doc
       */
      abstract static public function myMethod2();
}',
'<?php
/**
 * File Doc
 */ 
/**
 * Class Doc
 */
abstract class MyClass {
    /**
     * PropertyDoc
     */
     public $prop1;
    /**
     * PropertyDoc2
     *
     * @var int
     */
     public static $prop2;
     /**
      * Function Doc
      */
      static public function myMethod()
      {
        $this->prop2 = 1;
      }
      /**
       * Function Doc
       */
      abstract static public function myMethod2();
}',
        );

        return $return;
    }

    /**
     * @dataProvider providePhpdocExample
     */
    public function testSingleLineBeforePhpdoc($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }
}
