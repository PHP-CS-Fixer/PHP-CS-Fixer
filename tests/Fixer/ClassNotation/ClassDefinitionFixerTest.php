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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class ClassDefinitionFixerTest extends AbstractFixerTestCase
{
    private static $defaultTestConfig = array(
        'singleLine' => false,
        'singleItemSingleLine' => false,
        'multiLineExtendsEachSingleLine' => false,
    );

    public function testConfigureDefaultToNull()
    {
        $fixer = new ClassDefinitionFixer();
        $fixer->configure(self::$defaultTestConfig);
        $fixer->configure(null);

        $defaultConfigProperty = new \ReflectionProperty('PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer', 'defaultConfig');
        $defaultConfigProperty->setAccessible(true);

        $this->assertAttributeSame($defaultConfigProperty->getValue(), 'config', $fixer);
    }

    /**
     * @param string $expected PHP source code
     * @param string $input    PHP source code
     *
     * @dataProvider provideAnonymousClassesCases
     *
     * @requires PHP 7.0
     */
    public function testFixingAnonymousClasses($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected PHP source code
     * @param string $input    PHP source code
     *
     * @dataProvider provideClassesCases
     */
    public function testFixingClasses($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string              $expected PHP source code
     * @param string              $input    PHP source code
     * @param array<string, bool> $config
     *
     * @dataProvider provideClassesWithConfigCases
     */
    public function testFixingClassesWithConfig($expected, $input, array $config)
    {
        $fixer = $this->getFixer();
        $fixer->configure($config);

        $this->doTest($expected, $input, null, $fixer);
    }

    /**
     * @param string $expected PHP source code
     * @param string $input    PHP source code
     *
     * @dataProvider provideInterfacesCases
     */
    public function testFixingInterfaces($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected PHP source code
     * @param string $input    PHP source code
     *
     * @dataProvider provideTraitsCases
     */
    public function testFixingTraits($expected, $input)
    {
        if (!defined('T_TRAIT')) {
            $this->markTestSkipped('Test requires traits.');
        }

        $this->doTest($expected, $input);
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp /^\[class_definition\] Unknown configuration item "a", expected any of "singleLine, singleItemSingleLine, multiLineExtendsEachSingleLine".$/
     */
    public function testInvalidConfigurationKey()
    {
        $fixer = new ClassDefinitionFixer();
        $fixer->configure(array('a' => false));
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp /^\[class_definition\] Configuration value for item "singleLine" must be a bool, got "string".$/
     */
    public function testInvalidConfigurationValueType()
    {
        $fixer = new ClassDefinitionFixer();
        $fixer->configure(array('singleLine' => 'z'));
    }

    public function provideAnonymousClassesCases()
    {
        return array(
            array(
                "<?php \$a = new class\n{};",
                '<?php $a = new class{};',
            ),
            array(
                "<?php \$a = new class()\n{};",
                "<?php \$a = new\n class  (  ){};",
            ),
            array(
                "<?php \$a = new class(10, 1, /**/ 2)\n{};",
                '<?php $a = new class(  10, 1,/**/2  ){};',
            ),
            array(
                "<?php \$a = new class(10)\n{};",
                '<?php $a = new    class(10){};',
            ),
            array(
                "<?php \$a = new class(10) extends SomeClass implements SomeInterface, D\n{};",
                "<?php \$a = new    class(10)     extends\nSomeClass\timplements    SomeInterface, D {};",
            ),
        );
    }

    public function provideClassesCases()
    {
        return array_merge(
            $this->provideClassyCases('class'),
            $this->provideClassyExtendingCases('class'),
            $this->provideClassyImplementsCases()
        );
    }

    public function provideClassesWithConfigCases()
    {
        return array(
            array(
                "<?php class configA implements B, C\n{}",
                "<?php class configA implements\nB, C{}",
                array('singleLine' => true),
            ),
            array(
                "<?php class configA1 extends B\n{}",
                "<?php class configA1\n extends\nB{}",
                array('singleLine' => true),
            ),
            array(
                "<?php class configA1a extends B\n{}",
                "<?php class configA1a\n extends\nB{}",
                array('singleLine' => false, 'singleItemSingleLine' => true),
            ),
            array(
                "<?php class configA2 extends D implements B, C\n{}",
                "<?php class configA2 extends D implements\nB,\nC{}",
                array('singleLine' => true),
            ),
            array(
                "<?php class configA3 extends D implements B, C\n{}",
                "<?php class configA3\n extends\nD\n\t implements\nB,\nC{}",
                array('singleLine' => true),
            ),
            array(
                "<?php class configA4 extends D implements B, //\nC\n{}",
                "<?php class configA4\n extends\nD\n\t implements\nB,//\nC{}",
                array('singleLine' => true),
            ),
            array(
                "<?php class configA5 implements A\n{}",
                "<?php class configA5 implements\nA{}",
                array('singleLine' => false, 'singleItemSingleLine' => true),
            ),
            array(
                "<?php interface TestWithMultiExtendsMultiLine extends\nA,\nAb,\nC,\nD\n{}",
                "<?php interface TestWithMultiExtendsMultiLine extends A,\nAb,C,D\n{}",
                array(
                    'singleLine' => false,
                    'singleItemSingleLine' => false,
                    'multiLineExtendsEachSingleLine' => true,
                ),
            ),
        );
    }

    public function provideInterfacesCases()
    {
        $cases = array_merge(
            $this->provideClassyCases('interface'),
            $this->provideClassyExtendingCases('interface')
        );

        $cases[] = array(
    '<?php
interface Test extends
  /*a*/    /*b*/TestInterface1   , \A\B\C  ,  /* test */
    TestInterface2   ,   // test
    '.'

// Note: PSR does not have a rule for multiple extends
TestInterface3, /**/     TestInterface4   ,
      TestInterface5    ,     '.'
        /**/TestInterface65
{}
            ',
    '<?php
interface Test
extends
  /*a*/    /*b*/TestInterface1   , \A\B\C  ,  /* test */
    TestInterface2   ,   // test
    '.'

// Note: PSR does not have a rule for multiple extends
TestInterface3, /**/     TestInterface4   ,
      TestInterface5    ,     '.'
        /**/TestInterface65    {}
            ',
        );

        return $cases;
    }

    public function provideTraitsCases()
    {
        return $this->provideClassyCases('trait');
    }

    /**
     * @param string $source   PHP source code
     * @param array  $expected
     *
     * @dataProvider provideClassyDefinitionInfoCases
     */
    public function testClassyDefinitionInfo($source, array $expected)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        $fixer = $this->getFixer();
        $method = new \ReflectionMethod($fixer, 'getClassyDefinitionInfo');
        $method->setAccessible(true);

        $result = $method->invoke($fixer, $tokens, $expected['classy']);

        $this->assertSame($expected, $result);
    }

    public function provideClassyDefinitionInfoCases()
    {
        return array(
            array(
                '<?php class A{}',
                array(
                    'start' => 1,
                    'classy' => 1,
                    'open' => 4,
                    'extends' => false,
                    'implements' => false,
                ),
            ),
            array(
                '<?php final class A{}',
                array(
                    'start' => 1,
                    'classy' => 3,
                    'open' => 6,
                    'extends' => false,
                    'implements' => false,
                ),
            ),
            array(
                '<?php abstract /**/ class A{}',
                array(
                    'start' => 1,
                    'classy' => 5,
                    'open' => 8,
                    'extends' => false,
                    'implements' => false,
                ),
            ),
            array(
                '<?php class A extends B {}',
                array(
                    'start' => 1,
                    'classy' => 1,
                    'open' => 9,
                    'extends' => array(
                            'start' => 5,
                            'numberOfExtends' => 1,
                            'multiLine' => false,
                        ),
                    'implements' => false,
                ),
            ),
            array(
                '<?php interface A extends B,C,D {}',
                array(
                    'start' => 1,
                    'classy' => 1,
                    'open' => 13,
                    'extends' => array(
                            'start' => 5,
                            'numberOfExtends' => 3,
                            'multiLine' => false,
                        ),
                    'implements' => false,
                ),
            ),
        );
    }

    /**
     * @param string $source         PHP source code
     * @param int    $classOpenIndex classy curly brace open index
     * @param array  $expected
     *
     * @dataProvider provideClassyImplementsInfoCases
     */
    public function testClassyInheritanceInfo($source, $classOpenIndex, $label, array $expected)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        $fixer = $this->getFixer();
        $method = new \ReflectionMethod($fixer, 'getClassyInheritanceInfo');
        $method->setAccessible(true);

        $result = $method->invoke($fixer, $tokens, $expected['start'], $classOpenIndex, $label);

        $this->assertSame($expected, $result);
    }

    public function provideClassyImplementsInfoCases()
    {
        return array(
            array(
                '<?php
class X11 implements    Z   , T,R
{
}',
                15,
                'numberOfImplements',
                array('start' => 5, 'numberOfImplements' => 3, 'multiLine' => false),
            ),
            array(
                '<?php
class X10 implements    Z   , T,R    //
{
}',
                16,
                'numberOfImplements',
                array('start' => 5, 'numberOfImplements' => 3, 'multiLine' => false),
            ),
            array(
                '<?php class A implements B {}',
                9,
                'numberOfImplements',
                array('start' => 5, 'numberOfImplements' => 1, 'multiLine' => false),
            ),
            array(
                "<?php class A implements B,\n C{}",
                11,
                'numberOfImplements',
                array('start' => 5, 'numberOfImplements' => 2, 'multiLine' => true),
            ),
            array(
                "<?php class A implements Z\\C\\B,C,D  {\n\n\n}",
                17,
                'numberOfImplements',
                array('start' => 5, 'numberOfImplements' => 3, 'multiLine' => false),
            ),
            array(
                '<?php
namespace A {
    interface C {}
}

namespace {
    class B{}

    class A extends //
        B     implements /*  */ \A
        \C, Z{
        public function test()
        {
            echo 1;
        }
    }

    $a = new A();
    $a->test();
}',
                48,
                'numberOfImplements',
                array('start' => 36, 'numberOfImplements' => 2, 'multiLine' => true),
            ),
        );
    }

    /**
     * @dataProvider providePHP7Cases
     * @requires PHP 7.0
     */
    public function testFixPHP7($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function providePHP7Cases()
    {
        return array(
            array(
            '<?php
$a = new class implements
    \RFb,
    \Fcc,
\GFddZz
{
};',
            '<?php
$a = new class implements
    \RFb,
    \Fcc, \GFddZz
{
};',
            ),
            array(
            '<?php
$a = new class implements
    \RFb,
    \Fcc,
\GFddZz
{
}?>',
            '<?php
$a = new class implements
    \RFb,
    \Fcc, \GFddZz
{
}?>',
            ),
        );
    }

    protected function getFixerConfiguration()
    {
        return self::$defaultTestConfig;
    }

    private function provideClassyCases($classy)
    {
        return array(
            array(
                sprintf("<?php %s A\n{}", $classy),
                sprintf('<?php %s    A   {}', $classy),
            ),
            array(
                sprintf("<?php %s B\n{}", $classy),
                sprintf('<?php %s    B{}', $classy),
            ),
            array(
                sprintf("<?php %s C\n{}", $classy),
                sprintf("<?php %s\n\tC{}", $classy),
            ),
            array(
                sprintf("<?php %s D //\n{}", $classy),
                sprintf("<?php %s    D//\n{}", $classy),
            ),
            array(
                sprintf("<?php %s /**/ E //\n{}", $classy),
                sprintf("<?php %s/**/E//\n{}", $classy),
            ),
            array(
                sprintf(
                    "<?php
%s A
{}

%s /**/ B //
/**/\n{}", $classy, $classy
                ),
                sprintf(
                    '<?php
%s
   A
{}

%s/**/B//
/**/ {}', $classy, $classy
                ),
            ),
            array(
                sprintf('<?php
namespace {
    %s IndentedNameSpacedClass
{
    }
}', $classy
                ),
                sprintf('<?php
namespace {
    %s IndentedNameSpacedClass    {
    }
}', $classy
                ),
            ),
        );
    }

    private function provideClassyExtendingCases($classy)
    {
        return array(
            array(
                sprintf("<?php %s AE0 extends B\n{}", $classy),
                sprintf('<?php %s    AE0    extends B    {}', $classy),
            ),
            array(
                sprintf("<?php %s /**/ AE1 /**/ extends /**/ B /**/\n{}", $classy),
                sprintf('<?php %s/**/AE1/**/extends/**/B/**/{}', $classy),
            ),
            array(
                sprintf("<?php %s /*%s*/ AE2 extends\nB\n{}", $classy, $classy),
                sprintf("<?php %s /*%s*/ AE2 extends\nB{}", $classy, $classy),
            ),
            array(
                sprintf('<?php
%s Test124 extends
\Exception
{}', $classy),
                sprintf('<?php
%s
Test124

extends
\Exception {}', $classy),
            ),
        );
    }

    private function provideClassyImplementsCases()
    {
        return array(
            array(
                "<?php class E implements B\n{}",
                "<?php class    E   \nimplements     B       \t{}",
            ),
            array(
                "<?php abstract class F extends B implements C\n{}",
                '<?php abstract    class    F    extends     B    implements C {}',
            ),
            array(
                "<?php abstract class G extends       //
B /*  */ implements C\n{}",
                '<?php abstract    class     G     extends       //
B/*  */implements C{}',
            ),
            array(
                '<?php
class Aaa IMPLEMENTS
    \RFb,
    \Fcc,
\GFddZz
{
}',
                '<?php
class Aaa IMPLEMENTS
    \RFb,
    \Fcc, \GFddZz
{
}',
            ),
            array(
                '<?php
class        //
X            //
extends      //
Y            //
implements   //
Z,       //
U            //
{}           //',
                '<?php
class        //
X            //
extends      //
Y            //
implements   //
Z    ,       //
U            //
{}           //',
            ),
            array(
                '<?php
class Aaa implements
    PhpCsFixer\Tests\Fixer,
\RFb,
    \Fcc1,
\GFdd
{
}',
                '<?php
class Aaa implements
    PhpCsFixer\Tests\Fixer,\RFb,
    \Fcc1, \GFdd
{
}',
            ),
            array(
                '<?php
class /**/ Test123 EXtends  /**/ \RuntimeException implements
TestZ
{
}',
                '<?php
class/**/Test123
EXtends  /**/        \RuntimeException    implements
TestZ
{
}',
            ),
            array(
                '<?php
    class Aaa implements Ebb, \Ccc
    {
    }',
                '<?php
    class Aaa    implements    Ebb,    \Ccc
    {
    }',
            ),
            array(
                '<?php
class X2 IMPLEMENTS
Z, //
U,
D
{
}',
                '<?php
class X2 IMPLEMENTS
Z    , //
U, D
{
}',
            ),
            array(
                '<?php
                    class VeryLongClassNameWithLotsOfLetters extends AnotherVeryLongClassName implements
    VeryLongInterfaceNameThatIDontWantOnTheSameLine
{
}',
                '<?php
                    class      VeryLongClassNameWithLotsOfLetters    extends AnotherVeryLongClassName implements
    VeryLongInterfaceNameThatIDontWantOnTheSameLine
{
}',
            ),
            array(
                '<?php
class /**/ Test125 //aaa
extends  /*

*/
//
\Exception        //
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
class Test extends TestInterface8 implements      /*a*/      /*b*/
TestInterface1,  /* test */
    TestInterface2,   // test
    '.'

// test
TestInterface3, /**/
TestInterface4,
      TestInterface5,    '.'
        /**/TestInterface6c
{
}',
                '<?php
class Test
extends
    TestInterface8
  implements      /*a*/      /*b*/TestInterface1   ,  /* test */
    TestInterface2   ,   // test
    '.'

// test
TestInterface3, /**/     TestInterface4   ,
      TestInterface5    ,    '.'
        /**/TestInterface6c
{
}',
            ),
        );
    }
}
