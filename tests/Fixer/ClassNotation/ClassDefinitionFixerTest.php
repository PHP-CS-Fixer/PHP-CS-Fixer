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
use PhpCsFixer\Tests\Test\AbstractFixerWithAliasedOptionsTestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer
 */
final class ClassDefinitionFixerTest extends AbstractFixerWithAliasedOptionsTestCase
{
    /**
     * @group legacy
     */
    public function testLegacyConfigureDefaultToNull()
    {
        $this->expectDeprecation('Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.');
        $defaultConfig = [
            'multi_line_extends_each_single_line' => false,
            'single_item_single_line' => false,
            'single_line' => false,
        ];

        $fixer = new ClassDefinitionFixer();
        $fixer->configure($defaultConfig);
        static::assertConfigurationSame($defaultConfig, $fixer);

        $fixer->configure(null);
        static::assertConfigurationSame($defaultConfig, $fixer);
    }

    public function testConfigureDefaultToNull()
    {
        $defaultConfig = [
            'multi_line_extends_each_single_line' => false,
            'single_item_single_line' => false,
            'single_line' => false,
        ];

        $fixer = new ClassDefinitionFixer();
        $fixer->configure($defaultConfig);
        static::assertConfigurationSame($defaultConfig, $fixer);

        $fixer->configure([]);
        static::assertConfigurationSame($defaultConfig, $fixer);
    }

    /**
     * @param string              $expected PHP source code
     * @param string              $input    PHP source code
     * @param array<string, bool> $config
     *
     * @dataProvider provideAnonymousClassesCases
     *
     * @requires PHP 7.0
     */
    public function testFixingAnonymousClasses($expected, $input, array $config = [])
    {
        $this->configureFixerWithAliasedOptions($config);

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
        $this->fixer->configure([]);

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
        $this->configureFixerWithAliasedOptions($config);

        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected PHP source code
     * @param string $input    PHP source code
     *
     * @dataProvider provideInterfacesCases
     */
    public function testFixingInterfaces($expected, $input)
    {
        $this->fixer->configure([]);

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
        $this->fixer->configure([]);

        $this->doTest($expected, $input);
    }

    public function testInvalidConfigurationKey()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '/^\[class_definition\] Invalid configuration: The option "a" does not exist\. Defined options are: "multi_line_extends_each_single_line", "single_item_single_line", "single_line"\.$/'
        );

        $fixer = new ClassDefinitionFixer();
        $fixer->configure(['a' => false]);
    }

    public function testInvalidConfigurationValueType()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '/^\[class_definition\] Invalid configuration: The option "single_line" with value "z" is expected to be of type "bool", but is of type "string"\.$/'
        );

        $fixer = new ClassDefinitionFixer();
        $fixer->configure(['single_line' => 'z']);
    }

    public function provideAnonymousClassesCases()
    {
        return [
            [
                '<?php $a = new class(0) extends SomeClass implements SomeInterface, D {};',
                "<?php \$a = new    class(0)     extends\nSomeClass\timplements    SomeInterface, D {};",
            ],
            [
                '<?php $a = new class(1) extends SomeClass implements SomeInterface, D {};',
                "<?php \$a = new    class(1)     extends\nSomeClass\timplements    SomeInterface, D {};",
                ['single_line' => true],
            ],
            [
                "<?php \$a = new class('1a') implements\nA\n{};",
                "<?php \$a = new class('1a')   implements\nA{};",
            ],
            [
                "<?php \$a = new class('1a') implements A {};",
                "<?php \$a = new class('1a')   implements A{};",
                ['single_item_single_line' => true],
            ],
            [
                '<?php $a = new class {};',
                '<?php $a = new class{};',
            ],
            [
                '<?php $a = new class {};',
                "<?php \$a = new class\n{};",
            ],
            [
                '<?php $a = new class() {};',
                "<?php \$a = new\n class  (  ){};",
            ],
            [
                '<?php $a = new class(10, 1, /**/ 2) {};',
                '<?php $a = new class(  10, 1,/**/2  ){};',
            ],
            [
                '<?php $a = new class(2) {};',
                '<?php $a = new    class(2){};',
            ],
            [
                '<?php $a = new class($this->prop) {};',
                '<?php $a = new class(   $this->prop   ){};',
            ],
            [
                '<?php $a = new class($this->prop, $v[3], 4) {};',
                '<?php $a = new class(   $this->prop,$v[3],   4)         {};',
            ],
            'PSR-12 Extends/Implements Parenthesis on the next line.' => [
                '<?php
$instance = new class extends \Foo implements
\ArrayAccess,
    \Countable,
    \Serializable
{};',
                '<?php
$instance = new class extends \Foo implements
\ArrayAccess,\Countable,\Serializable{};',
            ],
            'PSR-12 Implements Parenthesis on the next line.' => [
                '<?php
$instance = new class implements
\ArrayAccess,
    \Countable,
    \Serializable
{};',
                '<?php
$instance = new class implements
\ArrayAccess,\Countable,\Serializable{};',
            ],
            'PSR-12 Extends Parenthesis on the next line.' => [
                '<?php
$instance = new class extends
ArrayAccess
{};',
                '<?php
$instance = new class
extends
ArrayAccess
{};',
            ],
            [
                "<?php \$a = new #
class #
( #
'1a', #
1 #
) #
implements#
A, #
B,
    C #
{#
#
}#
;",
                "<?php \$a = new#
class#
(#
'1a',#
1 #
)#
implements#
A, #
B,C#
{#
#
}#
;",
            ],
            [
                "<?php \$a = new #
class #
( #
'1a', #
1 #
) #
implements #
A #
{#
#
}#
;",
                "<?php \$a = new#
class#
(#
'1a',#
1 #
)#
implements#
A#
{#
#
}#
;",
                ['single_item_single_line' => true],
            ],
            [
                '<?php $a = new class() #
{};',
                '<?php $a = new class()#
{};',
            ],
        ];
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
        return [
            [
                "<?php class configA implements B, C\n{}",
                "<?php class configA implements\nB, C{}",
                ['single_line' => true],
            ],
            [
                "<?php class configA1 extends B\n{}",
                "<?php class configA1\n extends\nB{}",
                ['single_line' => true],
            ],
            [
                "<?php class configA1a extends B\n{}",
                "<?php class configA1a\n extends\nB{}",
                ['single_line' => false, 'single_item_single_line' => true],
            ],
            [
                "<?php class configA2 extends D implements B, C\n{}",
                "<?php class configA2 extends D implements\nB,\nC{}",
                ['single_line' => true],
            ],
            [
                "<?php class configA3 extends D implements B, C\n{}",
                "<?php class configA3\n extends\nD\n\t implements\nB,\nC{}",
                ['single_line' => true],
            ],
            [
                "<?php class configA4 extends D implements B, #\nC\n{}",
                "<?php class configA4\n extends\nD\n\t implements\nB,#\nC{}",
                ['single_line' => true],
            ],
            [
                "<?php class configA5 implements A\n{}",
                "<?php class configA5 implements\nA{}",
                ['single_line' => false, 'single_item_single_line' => true],
            ],
            [
                "<?php interface TestWithMultiExtendsMultiLine extends\n    A,\nAb,\n    C,\n    D\n{}",
                "<?php interface TestWithMultiExtendsMultiLine extends A,\nAb,C,D\n{}",
                [
                    'single_line' => false,
                    'single_item_single_line' => false,
                    'multi_line_extends_each_single_line' => true,
                ],
            ],
        ];
    }

    public function provideInterfacesCases()
    {
        $cases = array_merge(
            $this->provideClassyCases('interface'),
            $this->provideClassyExtendingCases('interface')
        );

        $cases[] = [
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
        ];

        return $cases;
    }

    public function provideTraitsCases()
    {
        return $this->provideClassyCases('trait');
    }

    /**
     * @param string $source PHP source code
     *
     * @dataProvider provideClassyDefinitionInfoCases
     */
    public function testClassyDefinitionInfo($source, array $expected)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        $method = new \ReflectionMethod($this->fixer, 'getClassyDefinitionInfo');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $expected['classy']);

        static::assertSame($expected, $result->toArray());
    }

    public function provideClassyDefinitionInfoCases()
    {
        return [
            [
                '<?php class A{}',
                [
                    'start' => 1,
                    'classy' => 1,
                    'open' => 4,
                    'extends' => [],
                    'implements' => [],
                    'anonymous' => false,
                ],
            ],
            [
                '<?php final class A{}',
                [
                    'start' => 1,
                    'classy' => 3,
                    'open' => 6,
                    'extends' => [],
                    'implements' => [],
                    'anonymous' => false,
                ],
            ],
            [
                '<?php abstract /**/ class A{}',
                [
                    'start' => 1,
                    'classy' => 5,
                    'open' => 8,
                    'extends' => [],
                    'implements' => [],
                    'anonymous' => false,
                ],
            ],
            [
                '<?php class A extends B {}',
                [
                    'start' => 1,
                    'classy' => 1,
                    'open' => 9,
                    'extends' => [
                        'start' => 5,
                        'numberOfExtends' => 1,
                        'multiLine' => false,
                    ],
                    'implements' => [],
                    'anonymous' => false,
                ],
            ],
            [
                '<?php interface A extends B,C,D {}',
                [
                    'start' => 1,
                    'classy' => 1,
                    'open' => 13,
                    'extends' => [
                        'start' => 5,
                        'numberOfExtends' => 3,
                        'multiLine' => false,
                    ],
                    'implements' => [],
                    'anonymous' => false,
                ],
            ],
        ];
    }

    /**
     * @param string $source PHP source code
     * @param string $label
     *
     * @dataProvider provideClassyImplementsInfoCases
     */
    public function testClassyInheritanceInfo($source, $label, array $expected)
    {
        $this->doTestClassyInheritanceInfo($source, $label, $expected);
    }

    /**
     * @param string $source PHP source code
     * @param string $label
     *
     * @requires PHP 7.0
     * @dataProvider provideClassyInheritanceInfo7Cases
     */
    public function testClassyInheritanceInfo7($source, $label, array $expected)
    {
        $this->doTestClassyInheritanceInfo($source, $label, $expected);
    }

    public function provideClassyImplementsInfoCases()
    {
        $tests = [
            '1' => [
                '<?php
class X11 implements    Z   , T,R
{
}',
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 3, 'multiLine' => false],
            ],
            '2' => [
                '<?php
class X10 implements    Z   , T,R    //
{
}',
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 3, 'multiLine' => false],
            ],
            '3' => [
                '<?php class A implements B {}',
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 1, 'multiLine' => false],
            ],
            '4' => [
                "<?php class A implements B,\n I{}",
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 2, 'multiLine' => true],
            ],
            '5' => [
                "<?php class A implements Z\\C\\B,C,D  {\n\n\n}",
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 3, 'multiLine' => false],
            ],
        ];

        foreach ($tests as $index => $test) {
            yield $index => $test;
        }

        if (\PHP_VERSION_ID < 80000) {
            $multiLine = true;
            $code = '<?php
namespace A {
    interface X {}
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
}';
        } else {
            $multiLine = false;
            $code = '<?php
namespace A {
    interface X {}
}

namespace {
    class B{}

    class A extends //
        B     implements /*  */ \A\C, Z{
        public function test()
        {
            echo 1;
        }
    }

    $a = new A();
    $a->test();
}';
        }

        yield [
            $code,
            'numberOfImplements',
            ['start' => 36, 'numberOfImplements' => 2, 'multiLine' => $multiLine],
        ];
    }

    public function provideClassyInheritanceInfo7Cases()
    {
        return [
            [
                "<?php \$a = new    class(3)     extends\nSomeClass\timplements    SomeInterface, D {};",
                'numberOfExtends',
                ['start' => 12, 'numberOfExtends' => 1, 'multiLine' => true],
            ],
            [
                "<?php \$a = new class(4) extends\nSomeClass\timplements SomeInterface, D\n\n{};",
                'numberOfImplements',
                ['start' => 16, 'numberOfImplements' => 2, 'multiLine' => false],
            ],
            [
                "<?php \$a = new class(5) extends SomeClass\nimplements    SomeInterface, D {};",
                'numberOfExtends',
                ['start' => 12, 'numberOfExtends' => 1, 'multiLine' => true],
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider providePHP70Cases
     * @requires PHP 7.0
     */
    public function testFixPHP70($expected, $input = null)
    {
        $this->fixer->configure([]);

        $this->doTest($expected, $input);
    }

    public function providePHP70Cases()
    {
        return [
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider providePHP73Cases
     * @requires PHP 7.3
     */
    public function testFixPHP73($expected, $input)
    {
        $this->fixer->configure([]);
        $this->doTest($expected, $input);
    }

    public function providePHP73Cases()
    {
        return [
            [
                '<?php new class(1, 2, 3, ) {};',
                '<?php new class(1, 2, 3,) {};',
            ],
            [
                '<?php new class(1, 2, 3, ) {};',
                '<?php new class(
                    1,
                    2,
                    3,
                ) {};',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure([]);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php\r\nclass Aaa implements\r\n\tBbb,\r\n\tCcc,\r\n\tDdd\r\n\t{\r\n\t}",
                "<?php\r\nclass Aaa implements\r\n\tBbb, Ccc,\r\n\tDdd\r\n\t{\r\n\t}",
            ],
        ];
    }

    private static function assertConfigurationSame(array $expected, ClassDefinitionFixer $fixer)
    {
        $reflectionProperty = new \ReflectionProperty($fixer, 'configuration');
        $reflectionProperty->setAccessible(true);

        static::assertSame($expected, $reflectionProperty->getValue($fixer));
    }

    private function doTestClassyInheritanceInfo($source, $label, array $expected)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);
        static::assertTrue($tokens[$expected['start']]->isGivenKind([T_IMPLEMENTS, T_EXTENDS]), sprintf('Token must be "implements" or "extends", got "%s".', $tokens[$expected['start']]->getContent()));
        $method = new \ReflectionMethod($this->fixer, 'getClassyInheritanceInfo');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $expected['start'], $label);

        static::assertSame($expected, $result);
    }

    private function provideClassyCases($classy)
    {
        return [
            [
                sprintf("<?php %s A\n{}", $classy),
                sprintf('<?php %s    A   {}', $classy),
            ],
            [
                sprintf("<?php %s B\n{}", $classy),
                sprintf('<?php %s    B{}', $classy),
            ],
            [
                sprintf("<?php %s C\n{}", $classy),
                sprintf("<?php %s\n\tC{}", $classy),
            ],
            [
                sprintf("<?php %s D //\n{}", $classy),
                sprintf("<?php %s    D//\n{}", $classy),
            ],
            [
                sprintf("<?php %s /**/ E //\n{}", $classy),
                sprintf("<?php %s/**/E//\n{}", $classy),
            ],
            [
                sprintf(
                    "<?php
%s A
{}

%s /**/ B //
/**/\n{}",
                    $classy,
                    $classy
                ),
                sprintf(
                    '<?php
%s
   A
{}

%s/**/B //
/**/ {}',
                    $classy,
                    $classy
                ),
            ],
            [
                sprintf(
                    '<?php
namespace {
    %s IndentedNameSpacedClass
{
    }
}',
                    $classy
                ),
                sprintf(
                    '<?php
namespace {
    %s IndentedNameSpacedClass    {
    }
}',
                    $classy
                ),
            ],
        ];
    }

    private function provideClassyExtendingCases($classy)
    {
        return [
            [
                sprintf("<?php %s AE0 extends B\n{}", $classy),
                sprintf('<?php %s    AE0    extends B    {}', $classy),
            ],
            [
                sprintf("<?php %s /**/ AE1 /**/ extends /**/ B /**/\n{}", $classy),
                sprintf('<?php %s/**/AE1/**/extends/**/B/**/{}', $classy),
            ],
            [
                sprintf("<?php %s /*%s*/ AE2 extends\nB\n{}", $classy, $classy),
                sprintf("<?php %s /*%s*/ AE2 extends\nB{}", $classy, $classy),
            ],
            [
                sprintf('<?php
%s Test124 extends
\Exception
{}', $classy),
                sprintf('<?php
%s
Test124

extends
\Exception {}', $classy),
            ],
        ];
    }

    private function provideClassyImplementsCases()
    {
        return [
            [
                "<?php class E implements B\n{}",
                "<?php class    E   \nimplements     B       \t{}",
            ],
            [
                "<?php abstract class F extends B implements C\n{}",
                '<?php abstract    class    F    extends     B    implements C {}',
            ],
            [
                "<?php abstract class G extends       //
B /*  */ implements C\n{}",
                '<?php abstract    class     G     extends       //
B/*  */implements C{}',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
class /**/ Test123 EXtends /**/ \RuntimeException implements
TestZ
{
}',
                '<?php
class/**/Test123
EXtends  /**/        \RuntimeException    implements
TestZ
{
}',
            ],
            [
                '<?php
    class Aaa implements Ebb, \Ccc
    {
    }',
                '<?php
    class Aaa    implements    Ebb,    \Ccc
    {
    }',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }
}
