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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer>
 *
 * @covers \PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class LowercaseKeywordsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php $x = (1 and 2);', '<?php $x = (1 AND 2);'];

        yield ['<?php foreach(array(1, 2, 3) as $val) {}', '<?php FOREACH(array(1, 2, 3) AS $val) {}'];

        yield ['<?php echo "GOOD AS NEW";'];

        yield ['<?php echo X::class ?>', '<?php echo X::ClASs ?>'];

        yield [
            '<?php $fn = fn() => true;',
            '<?php $fn = FN() => true;',
        ];

        yield ['<?php __HALT_COMPILER();'];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php
echo match (1) {
    1 => 9,
    2 => 7,
};',
            '<?php
echo MATCH (1) {
    1 => 9,
    2 => 7,
};',
        ];

        yield [
            '<?php
class Point {
    public function __construct(
        public float $x = 0.0,
        protected float $y = 0.0,
        private float $z = 0.0,
    ) {}
}
',
            '<?php
class Point {
    public function __construct(
        PUBLIC float $x = 0.0,
        Protected float $y = 0.0,
        privatE float $z = 0.0,
    ) {}
}
',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php
final class Foo
{
    public readonly string $prop;
}
            ',
            '<?php
final class Foo
{
    public READONLY string $prop;
}
            ',
        ];

        yield [
            '<?php
class Point {
    public function __construct(
        public readonly float $x = 0.0,
        readonly protected float $y = 0.0,
        private readonly float $z = 0.0,
    ) {}
}
',
            '<?php
class Point {
    public function __construct(
        PUBLIC rEADONLY float $x = 0.0,
        READonly Protected float $y = 0.0,
        privatE READONLY float $z = 0.0,
    ) {}
}
',
        ];

        yield 'enum full caps' => [
            '<?php
enum Suit {
    case Hearts;
}
',
            '<?php
ENUM Suit {
    case Hearts;
}
',
        ];
    }

    /**
     * @dataProvider provideFix84Cases
     *
     * @requires PHP 8.4
     */
    public function testFix84(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield 'asymmetric visibility' => [
            <<<'PHP'
                <?php class Foo
                {
                    public(set) Bar $a;
                    protected(set) Bar $b;
                    private(set) Baz $c;
                }
                PHP,
            <<<'PHP'
                <?php class Foo
                {
                    PUBLIC(SET) Bar $a;
                    PROTECTED(SET) Bar $b;
                    PRIVATE(SET) Baz $c;
                }
                PHP,
        ];
    }
}
