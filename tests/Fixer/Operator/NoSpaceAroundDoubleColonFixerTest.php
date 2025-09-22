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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\NoSpaceAroundDoubleColonFixer>
 *
 * @covers \PhpCsFixer\Fixer\Operator\NoSpaceAroundDoubleColonFixer
 */
final class NoSpaceAroundDoubleColonFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php echo self::$a;',
            '<?php echo self :: $a;',
        ];

        yield [
            '<?php echo static::$a;',
            '<?php echo static ::$a;',
        ];

        yield [
            '<?php
                echo F\B::class;
                echo A\B::     /**/ c;
                echo C\B/**/::c;
            ',
            '<?php
                echo F\B::    class;
                echo A\B   ::     /**/ c;
                echo C\B/**/::   c;
            ',
        ];

        yield [
            '<?php
namespace {
    class Foo { public const a = 1; }

    echo Foo::a; // Fix
    echo "\n".Place\Bar::$a."\n"; // Fix
}

namespace Somewhere\Over\The\Rainbow {
    class Bar {
        public static $a = "BAR-A:: ";

        public function v(?string $z = "zzz"): void
        {
            echo "\n".self::$a.$z; // Fix
            echo "\n".static::class; // Fix
            echo "\n".static # do ...
              :: # ... not ...
            $a.$z; // ... fix
        }
    }

    $bar = new Bar();
    $bar->v();
}

 # ; echo A :: B;
// ; echo A :: B;
/* ; echo A :: B; */
',
            '<?php
namespace {
    class Foo { public const a = 1; }

    echo Foo:: a; // Fix
    echo "\n".Place\Bar  ::   $a."\n"; // Fix
}

namespace Somewhere\Over\The\Rainbow {
    class Bar {
        public static $a = "BAR-A:: ";

        public function v(?string $z = "zzz"): void
        {
            echo "\n".self  ::  $a.$z; // Fix
            echo "\n".static  ::  class; // Fix
            echo "\n".static # do ...
              :: # ... not ...
            $a.$z; // ... fix
        }
    }

    $bar = new Bar();
    $bar->v();
}

 # ; echo A :: B;
// ; echo A :: B;
/* ; echo A :: B; */
',
        ];
    }
}
