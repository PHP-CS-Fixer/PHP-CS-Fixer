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
use PhpCsFixer\Tokenizer\Analyzer\ClassAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Pol Dellaiera <pol.dellaiera@protonmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\ClassAnalyzer
 */
final class ClassAnalyzerTest extends TestCase
{
    /**
     * @param string $code
     * @param bool   $hasExtends
     * @param mixed  $classIndex
     *
     * @dataProvider provideClassHasExtendsCases
     */
    public function testClassHasExtends($classIndex, $code, $hasExtends)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ClassAnalyzer();

        $extends = null !== $analyzer->getClassExtends($tokens, $classIndex);

        $this->assertSame($hasExtends, $extends);
    }

    public function provideClassHasExtendsCases()
    {
        return [
            [
                9,
                '<?php

namespace Foo\Bar;

class Nakano extends Izumi {

}',
                true,
            ],
            [
                9,
                '<?php

namespace Foo\Bar;

class Izumi {

}',
                false,
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
        $analyzer = new ClassAnalyzer();
        $this->assertSame(serialize($expected), serialize($analyzer->getClassDefinition($tokens, $classIndex)));
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
                    'implements' => false,
                    'anonymousClass' => false,
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
                    'anonymousClass' => false,
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
                    'extends' => false,
                    'implements' => false,
                    'anonymousClass' => true,
                ],
            ],
        ];
    }
}
