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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\ClassesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Pol Dellaiera <pol.dellaiera@protonmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\ClassesAnalyzer
 */
final class ClassesAnalyzerTest extends TestCase
{
    /**
     * @param int         $classIndex
     * @param string      $code
     * @param null|string $name
     * @param int         $startIndex
     * @param int         $endIndex
     *
     * @dataProvider provideGetExtendsClassCases
     */
    public function testGetClassExtends($classIndex, $code, $name = null, $startIndex = 0, $endIndex = 0)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ClassesAnalyzer();

        $extend = $analyzer->getClassExtends($tokens, $classIndex);

        if (null !== $extend) {
            static::assertSame($name, $extend->getName());
            static::assertSame($startIndex, $extend->getStartIndex());
            static::assertSame($endIndex, $extend->getEndIndex());
        } else {
            static::assertNull($extend);
        }
    }

    public function provideGetExtendsClassCases()
    {
        return [
            [
                9,
                '<?php

namespace Foo\Bar;

class Nakano extends Izumi {

}',
                'Izumi',
                15,
                15,
            ],
            [
                9,
                '<?php

namespace Foo\Bar;

class Izumi {

}',
            ],
            [
                28,
                '<?php

namespace Foo\Bar;

foreach (["izumi", "nakano"] as $cat) {

}

class Cats {

}',
            ],
        ];
    }

    /**
     * @param string $code
     * @param int    $classIndex
     * @param array  $expected
     *
     * @dataProvider provideClassDefinitionInfoCases
     */
    public function testClassDefinitionInfo($code, $classIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ClassesAnalyzer();
        static::assertSame(serialize($expected), serialize($analyzer->getClassDefinition($tokens, $classIndex)->toArray()));
    }

    public function provideClassDefinitionInfoCases()
    {
        return [
            [
                '<?php

namespace Foo\Bar;

class Nakano extends Izumi {

}',
                9,
                [
                    'start' => 9,
                    'classy' => 9,
                    'open' => 17,
                    'extends' => [
                        'start' => 13,
                        'numberOfExtends' => 1,
                        'multiLine' => false,
                    ],
                    'implements' => [],
                    'anonymous' => false,
                ],
            ],
            [
                '<?php

namespace Foo\Bar;

class Nakano extends Izumi implements CatInterface {

}',
                9,
                [
                    'start' => 9,
                    'classy' => 9,
                    'open' => 21,
                    'extends' => [
                        'start' => 13,
                        'numberOfExtends' => 1,
                        'multiLine' => false,
                    ],
                    'implements' => [
                        'start' => 17,
                        'numberOfImplements' => 1,
                        'multiLine' => false,
                    ],
                    'anonymous' => false,
                ],
            ],
            [
                '<?php

namespace Foo\Bar;

new class {};',
                11,
                [
                    'start' => 11,
                    'classy' => 11,
                    'open' => 13,
                    'extends' => [],
                    'implements' => [],
                    'anonymous' => true,
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
        return [
            [
                '<?php
class X11 implements    Z   , T,R
{
}',
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 3, 'multiLine' => false],
            ],
            [
                '<?php
class X10 implements    Z   , T,R    //
{
}',
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 3, 'multiLine' => false],
            ],
            [
                '<?php class A implements B {}',
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 1, 'multiLine' => false],
            ],
            [
                "<?php class A implements B,\n C{}",
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 2, 'multiLine' => true],
            ],
            [
                "<?php class A implements Z\\C\\B,C,D  {\n\n\n}",
                'numberOfImplements',
                ['start' => 5, 'numberOfImplements' => 3, 'multiLine' => false],
            ],
            [
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
                'numberOfImplements',
                ['start' => 36, 'numberOfImplements' => 2, 'multiLine' => true],
            ],
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

    private function doTestClassyInheritanceInfo($source, $label, array $expected)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);
        static::assertTrue($tokens[$expected['start']]->isGivenKind([T_IMPLEMENTS, T_EXTENDS]), sprintf('Token must be "implements" or "extends", got "%s".', $tokens[$expected['start']]->getContent()));

        $analyzer = new ClassesAnalyzer();
        $result = $analyzer->getClassInheritanceInfo($tokens, $expected['start'], $label);

        static::assertSame($expected, $result);
    }
}
