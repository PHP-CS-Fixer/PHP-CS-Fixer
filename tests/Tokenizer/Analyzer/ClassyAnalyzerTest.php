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
use PhpCsFixer\Tokenizer\Analyzer\ClassyAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\ClassyAnalyzer
 */
final class ClassyAnalyzerTest extends TestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideIsClassyInvocationCases
     */
    public function testIsClassyInvocation($source, array $expected)
    {
        $tokens = Tokens::fromCode($source);
        $analyzer = new ClassyAnalyzer();

        foreach ($expected as $index => $isClassy) {
            static::assertSame($isClassy, $analyzer->isClassyInvocation($tokens, $index), 'Token at index '.$index.' should match the expected value.');
        }
    }

    public function provideIsClassyInvocationCases()
    {
        return [
            [
                '<?php new Foo;',
                [3 => true],
            ],
            [
                '<?php new \Foo;',
                [4 => true],
            ],
            [
                '<?php new Bar\Foo;',
                [3 => false, 5 => true],
            ],
            [
                '<?php new namespace\Foo;',
                [5 => true],
            ],
            [
                '<?php Foo::bar();',
                [1 => true, 3 => false],
            ],
            [
                '<?php \Foo::bar();',
                [2 => true, 4 => false],
            ],
            [
                '<?php Bar\Foo::bar();',
                [1 => false, 3 => true, 5 => false],
            ],
            [
                '<?php $foo instanceof Foo;',
                [5 => true],
            ],
            [
                '<?php class Foo extends \A {}',
                [3 => false, 8 => true],
            ],
            [
                '<?php class Foo implements A, B\C, \D, E {}',
                [3 => false, 7 => true, 10 => false, 12 => true, 16 => true, 19 => true],
            ],
            [
                '<?php class Foo { use A, B\C, \D, E { A::bar insteadof \E; } }',
                [3 => false, 9 => true, 12 => false, 14 => true, 18 => true, 21 => true, 25 => true, 32 => true],
            ],
            [
                '<?php function foo(Foo $foo, Bar &$bar, \Baz ...$baz, Foo\Bar $fooBar) {}',
                [3 => false, 5 => true, 10 => true, 17 => true, 23 => false, 25 => true],
            ],
            [
                '<?php class Foo { function bar() { parent::bar(); self::baz(); $a instanceof self; } }',
                [3 => false, 9 => false, 15 => false, 17 => false, 22 => false, 24 => false, 33 => false],
            ],
            [
                '<?php echo FOO, \BAR;',
                [3 => false, 7 => false],
            ],
            [
                '<?php FOO & $bar;',
                [1 => false],
            ],
            [
                '<?php foo(); \bar();',
                [1 => false, 7 => false],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsClassyInvocation70Cases
     * @requires PHP 7.0
     */
    public function testIsClassyInvocation70($source, array $expected)
    {
        $tokens = Tokens::fromCode($source);
        $analyzer = new ClassyAnalyzer();

        foreach ($expected as $index => $isClassy) {
            static::assertSame($isClassy, $analyzer->isClassyInvocation($tokens, $index), 'Token at index '.$index.' should match the expected value.');
        }
    }

    public function provideIsClassyInvocation70Cases()
    {
        return [
            [
                '<?php function foo(int $foo, string &$bar): self {}',
                [3 => false, 5 => false, 10 => false, 17 => false],
            ],
            [
                '<?php function foo(): Foo {}',
                [3 => false, 8 => true],
            ],
            [
                '<?php function foo(): \Foo {}',
                [3 => false, 9 => true],
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @dataProvider provideIsClassyInvocation71Cases
     * @requires PHP 7.1
     */
    public function testIsClassyInvocation71($source, array $expected)
    {
        $tokens = Tokens::fromCode($source);
        $analyzer = new ClassyAnalyzer();

        foreach ($expected as $index => $isClassy) {
            static::assertSame($isClassy, $analyzer->isClassyInvocation($tokens, $index), 'Token at index '.$index.' should match the expected value.');
        }
    }

    public function provideIsClassyInvocation71Cases()
    {
        return [
            [
                '<?php function foo(): \Foo {}',
                [3 => false, 9 => true],
            ],
            [
                '<?php function foo(?Foo $foo, ?Foo\Bar $fooBar): ?\Foo {}',
                [3 => false, 6 => true, 12 => false, 14 => true, 22 => true],
            ],
            [
                '<?php function foo(iterable $foo): string {}',
                [3 => false, 5 => false, 11 => false],
            ],
            [
                '<?php function foo(?int $foo): ?string {}',
                [3 => false, 6 => false, 13 => false],
            ],
        ];
    }
}
