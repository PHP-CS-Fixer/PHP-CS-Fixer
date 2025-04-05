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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\ReferenceAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\ReferenceAnalyzer
 *
 * @internal
 */
final class ReferenceAnalyzerTest extends TestCase
{
    public function testNonAmpersand(): void
    {
        $analyzer = new ReferenceAnalyzer();

        self::assertFalse($analyzer->isReference(Tokens::fromCode('<?php $foo;$bar;$baz;'), 3));
    }

    public function testReferenceAndNonReferenceTogether(): void
    {
        $analyzer = new ReferenceAnalyzer();

        $tokens = Tokens::fromCode('<?php function foo(&$bar = BAZ & QUX) {};');

        self::assertTrue($analyzer->isReference($tokens, 5));
        self::assertFalse($analyzer->isReference($tokens, 12));
    }

    /**
     * @dataProvider provideReferenceCases
     */
    public function testReference(string $code): void
    {
        $this->doTestCode(true, $code);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideReferenceCases(): iterable
    {
        yield ['<?php $foo =& $bar;'];

        yield ['<?php $foo =& find_var($bar);'];

        yield ['<?php $foo["bar"] =& $baz;'];

        yield ['<?php function foo(&$bar) {};'];

        yield ['<?php function foo($bar, &$baz) {};'];

        yield ['<?php function &() {};'];

        yield ['<?php
class Foo {
    public $value = 42;
    public function &getValue() {
        return $this->value;
    }
}'];

        yield ['<?php function foo(\Bar\Baz &$qux) {};'];

        yield ['<?php function foo(array &$bar) {};'];

        yield ['<?php function foo(callable &$bar) {};'];

        yield ['<?php function foo(int &$bar) {};'];

        yield ['<?php function foo(string &$bar) {};'];

        yield ['<?php foreach($foos as &$foo) {}'];

        yield ['<?php foreach($foos as $key => &$foo) {}'];

        yield ['<?php function foo(?int &$bar) {};'];
    }

    /**
     * @dataProvider provideNonReferenceCases
     */
    public function testNonReference(string $code): void
    {
        $this->doTestCode(false, $code);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideNonReferenceCases(): iterable
    {
        yield ['<?php $foo & $bar;'];

        yield ['<?php FOO & $bar;'];

        yield ['<?php Foo::BAR & $baz;'];

        yield ['<?php foo(1, 2) & $bar;'];

        yield ['<?php foo($bar & $baz);'];

        yield ['<?php foo($bar, $baz & $qux);'];

        yield ['<?php foo($bar->baz & $qux);'];

        yield ['<?php foo(Bar::BAZ & $qux);'];

        yield ['<?php foo(Bar\Baz::qux & $quux);'];

        yield ['<?php foo(\Bar\Baz::qux & $quux);'];

        yield ['<?php foo($bar["mode"] & $baz);'];

        yield ['<?php foo(0b11111111 & $bar);'];

        yield ['<?php foo(127 & $bar);'];

        yield ['<?php foo("bar" & $baz);'];

        yield ['<?php foo($bar = BAZ & $qux);'];

        yield ['<?php function foo($bar = BAZ & QUX) {};'];

        yield ['<?php function foo($bar = BAZ::QUX & QUUX) {};'];

        yield ['<?php function foo(array $bar = BAZ & QUX) {};'];

        yield ['<?php function foo(callable $bar = BAZ & QUX) {};'];

        yield ['<?php foreach($foos as $foo) { $foo & $bar; }'];

        yield ['<?php if ($foo instanceof Bar & 0b01010101) {}'];

        yield ['<?php function foo(?int $bar = BAZ & QUX) {};'];
    }

    /**
     * @dataProvider provideNonReferencePre84Cases
     *
     * @requires PHP <8.4
     */
    public function testNonReferencePre84(string $code): void
    {
        $this->doTestCode(false, $code);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideNonReferencePre84Cases(): iterable
    {
        yield ['<?php foo($bar{"mode"} & $baz);'];
    }

    private function doTestCode(bool $expected, string $code): void
    {
        $analyzer = new ReferenceAnalyzer();

        $tokens = Tokens::fromCode($code);

        foreach ($tokens as $index => $token) {
            if ('&' === $token->getContent()) {
                self::assertSame($expected, $analyzer->isReference($tokens, $index));
            }
        }
    }
}
