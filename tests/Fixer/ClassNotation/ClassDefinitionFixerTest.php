<?php

declare(strict_types=1);

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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer
 */
final class ClassDefinitionFixerTest extends AbstractFixerTestCase
{
    public function testConfigureDefaultToFalse(): void
    {
        $defaultConfig = [
            'multi_line_extends_each_single_line' => false,
            'single_item_single_line' => false,
            'single_line' => false,
            'space_before_parenthesis' => false,
            'inline_constructor_arguments' => true,
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
     */
    public function testFixingAnonymousClasses(string $expected, string $input, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideClassesCases
     */
    public function testFixingClasses(string $expected, string $input): void
    {
        $this->fixer->configure([]);
        $this->doTest($expected, $input);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideClassesWithConfigCases
     */
    public function testFixingClassesWithConfig(string $expected, string $input, array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideInterfacesCases
     */
    public function testFixingInterfaces(string $expected, string $input): void
    {
        $this->fixer->configure([]);
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideTraitsCases
     */
    public function testFixingTraits(string $expected, string $input): void
    {
        $this->fixer->configure([]);
        $this->doTest($expected, $input);
    }

    public function testInvalidConfigurationKey(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '/^\[class_definition\] Invalid configuration: The option "a" does not exist\. Defined options are: "inline_constructor_arguments", "multi_line_extends_each_single_line", "single_item_single_line", "single_line", "space_before_parenthesis"\.$/'
        );

        $fixer = new ClassDefinitionFixer();
        $fixer->configure(['a' => false]);
    }

    public function testInvalidConfigurationValueType(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '/^\[class_definition\] Invalid configuration: The option "single_line" with value "z" is expected to be of type "bool", but is of type "string"\.$/'
        );

        $fixer = new ClassDefinitionFixer();
        $fixer->configure(['single_line' => 'z']);
    }

    public function provideAnonymousClassesCases(): array
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
                "<?php \$a = new class('1a')   implements\nA{};",
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
                '<?php $a = new class(  ) {};',
                "<?php \$a = new\n class  (  ){};",
                ['inline_constructor_arguments' => false],
            ],
            [
                '<?php $a = new class implements Foo {};',
                "<?php \$a = new\n class    implements Foo {};",
                ['inline_constructor_arguments' => false],
            ],
            [
                '<?php $a = new class( $this->foo() , bar ( $a) ) {};',
                "<?php \$a = new\n class  ( \$this->foo() , bar ( \$a) ){};",
                ['inline_constructor_arguments' => false],
            ],
            [
                '<?php $a = new class(10, 1, /**/ 2) {};',
                '<?php $a = new class(  10, 1,/**/2  ){};',
            ],
            [
                '<?php $a = new class(  10, 1,/**/2  ) {};',
                '<?php $a = new class(  10, 1,/**/2  ){};',
                ['inline_constructor_arguments' => false],
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
                '<?php $a = new class(   $this->prop   ) {};',
                '<?php $a = new class(   $this->prop   ){};',
                ['inline_constructor_arguments' => false],
            ],
            [
                "<?php \$a = new class(\n\t\$a,\n\t\$b,\n\t\$c,\n\t\$d) implements A, B {};",
                "<?php \$a = new class(\n\t\$a,\n\t\$b,\n\t\$c,\n\t\$d) implements  A, \t B{};",
                ['inline_constructor_arguments' => false],
            ],
            [
                "<?php \$a = new class(\n\t\$a,\n\t\$b,\n\t\$c,\n\t\$d) implements A, B {};",
                "<?php \$a = new   class  (\n\t\$a,\n\t\$b,\n\t\$c,\n\t\$d)    implements  A, \t B{};",
                ['inline_constructor_arguments' => false],
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
$instance = new class   extends \Foo  implements
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
            'space_before_parenthesis 1' => [
                '<?php $z = new class () {};',
                '<?php $z = new class()  {};',
                ['space_before_parenthesis' => true],
            ],
            'space_before_parenthesis 2' => [
                '<?php $z = new class () {};',
                '<?php $z = new class   ()  {};',
                ['space_before_parenthesis' => true],
            ],
            'space_before_parenthesis and inline_constructor_arguments' => [
                '<?php $z = new class ( static::foo($this->bar())  ,baz() ) {};',
                '<?php $z = new class   ( static::foo($this->bar())  ,baz() )  {};',
                ['space_before_parenthesis' => true, 'inline_constructor_arguments' => false],
            ],
        ];
    }

    public function provideClassesCases(): array
    {
        return array_merge(
            $this->provideClassyCases('class'),
            $this->provideClassyExtendingCases('class'),
            $this->provideClassyImplementsCases()
        );
    }

    public function provideClassesWithConfigCases(): array
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

    public function provideInterfacesCases(): array
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

    public function provideTraitsCases(): array
    {
        return $this->provideClassyCases('trait');
    }

    /**
     * @param array<string, mixed> $expected
     *
     * @dataProvider provideClassyDefinitionInfoCases
     */
    public function testClassyDefinitionInfo(string $source, array $expected): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);

        $method = new \ReflectionMethod($this->fixer, 'getClassyDefinitionInfo');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $expected['classy']);

        static::assertSame($expected, $result);
    }

    public function provideClassyDefinitionInfoCases(): array
    {
        return [
            [
                '<?php class A{}',
                [
                    'start' => 1,
                    'classy' => 1,
                    'open' => 4,
                    'extends' => false,
                    'implements' => false,
                    'anonymousClass' => false,
                ],
            ],
            [
                '<?php final class A{}',
                [
                    'start' => 1,
                    'classy' => 3,
                    'open' => 6,
                    'extends' => false,
                    'implements' => false,
                    'anonymousClass' => false,
                ],
            ],
            [
                '<?php abstract /**/ class A{}',
                [
                    'start' => 1,
                    'classy' => 5,
                    'open' => 8,
                    'extends' => false,
                    'implements' => false,
                    'anonymousClass' => false,
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
                    'implements' => false,
                    'anonymousClass' => false,
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
                    'implements' => false,
                    'anonymousClass' => false,
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     *
     * @dataProvider provideClassyImplementsInfoCases
     */
    public function testClassyInheritanceInfo(string $source, string $label, array $expected): void
    {
        $this->doTestClassyInheritanceInfo($source, $label, $expected);
    }

    public function provideClassyImplementsInfoCases(): iterable
    {
        yield from [
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

        yield from [
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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([]);
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
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
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure([]);
        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases(): array
    {
        return [
            [
                "<?php\r\nclass Aaa implements\r\n\tBbb,\r\n\tCcc,\r\n\tDdd\r\n\t{\r\n\t}",
                "<?php\r\nclass Aaa implements\r\n\tBbb, Ccc,\r\n\tDdd\r\n\t{\r\n\t}",
            ],
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield [
            "<?php enum SomeEnum implements SomeInterface, D\n{};",
            "<?php enum SomeEnum \timplements    SomeInterface, D {};",
        ];

        yield [
            "<?php enum SomeEnum : int\n{}",
            '<?php enum   SomeEnum  :  int   {}',
        ];

        yield [
            "<?php enum SomeEnum\n{}",
            "<?php enum\tSomeEnum{}",
        ];
    }

    /**
     * @param array<string, mixed> $expected
     */
    private static function assertConfigurationSame(array $expected, ClassDefinitionFixer $fixer): void
    {
        $reflectionProperty = new \ReflectionProperty($fixer, 'configuration');
        $reflectionProperty->setAccessible(true);

        static::assertSame($expected, $reflectionProperty->getValue($fixer));
    }

    /**
     * @param array<string, mixed> $expected
     */
    private function doTestClassyInheritanceInfo(string $source, string $label, array $expected): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);
        static::assertTrue($tokens[$expected['start']]->isGivenKind([T_IMPLEMENTS, T_EXTENDS]), sprintf('Token must be "implements" or "extends", got "%s".', $tokens[$expected['start']]->getContent()));
        $method = new \ReflectionMethod($this->fixer, 'getClassyInheritanceInfo');
        $method->setAccessible(true);

        $result = $method->invoke($this->fixer, $tokens, $expected['start'], $label);

        static::assertSame($expected, $result);
    }

    private function provideClassyCases(string $classy): array
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

    private function provideClassyExtendingCases(string $classy): array
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

    private function provideClassyImplementsCases(): array
    {
        return [
            [
                '<?php class LotOfImplements implements A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q
{}',
                '<?php class LotOfImplements implements A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q{}',
            ],
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
