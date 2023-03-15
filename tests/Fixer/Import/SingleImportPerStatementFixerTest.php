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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer
 */
final class SingleImportPerStatementFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            [
                '<?php
                    /**/use Foo;
                    use FooB;
                ',
                '<?php
                    /**/use Foo,FooB;
                ',
            ],
            [
                <<<'EOF'
use Some, Not, PHP, Like, Use, Statement;
<?php

use Foo;
use FooA;
use FooB;
use FooC;
use FooD as D;
use FooE;
use FooF;
use FooG as G;
use FooH;
use FooI;
use FooJ;
use FooZ;

EOF
                ,
                <<<'EOF'
use Some, Not, PHP, Like, Use, Statement;
<?php

use Foo;
use FooA, FooB;
use FooC, FooD as D, FooE;
use FooF,
    FooG as G,
  FooH,     FooI,
        FooJ;
use FooZ;

EOF
            ],
            [
                <<<'EOF'
<?php

namespace {
    use Foo;
    use FooA;
    use FooB;
    use FooC;
    use FooD as D;
    use FooE;
    use FooF;
    use FooG as G;
    use FooH;
    use FooI;
    use FooJ;
    use FooZ;
}

namespace Boo {
    use Bar;
    use BarA;
    use BarB;
    use BarC;
    use BarD as D;
    use BarE;
    use BarF;
    use BarG as G;
    use BarH;
    use BarI;
    use BarJ;
    use BarZ;
}

EOF
                ,
                <<<'EOF'
<?php

namespace {
    use Foo;
    use FooA, FooB;
    use FooC, FooD as D, FooE;
    use FooF,
        FooG as G,
      FooH,     FooI,
            FooJ;
    use FooZ;
}

namespace Boo {
    use Bar;
    use BarA, BarB;
    use BarC, BarD as D, BarE;
    use BarF,
        BarG as G,
      BarH,     BarI,
            BarJ;
    use BarZ;
}

EOF
            ],
            [
                '<?php
                    use FooA;
                    use FooB;
                ',
                '<?php
                    use FooA, FooB;
                ',
            ],
            [
                '<?php use FooA;
use FooB?>',
                '<?php use FooA, FooB?>',
            ],
            [
                '<?php
use B;
use C;
    use E;
    use F;
        use G;
        use H;
',
                '<?php
use B,C;
    use E,F;
        use G,H;
',
            ],
            [
                '<?php
use B;
/*
*/use C;
',
                '<?php
use B,
/*
*/C;
',
            ],
            [
                '<?php
use A;
use B;
//,{} use ; :
#,{} use ; :
/*,{} use ; :*/
use C  ; ',
                '<?php
use A,B,
//,{} use ; :
#,{} use ; :
/*,{} use ; :*/
C  ; ',
            ],
            [
                '<?php use Z ;
use X ?><?php new X(); // run before white space around semicolon',
                '<?php use Z , X ?><?php new X(); // run before white space around semicolon',
            ],
            [
                '<?php use FooA#
;#
#
use FooB;',
                '<?php use FooA#
,#
#
FooB;',
            ],
            [
                '<?php use some\b\ClassB;
use function some\b\CC as C;
use function some\b\D;
use const some\b\E;
use function some\b\A\B;',
                '<?php use some\b\{ClassB, function CC as C, function D, const E, function A\B};',
            ],
            [
                '<?php
use Foo\Bar;
use Foo\Baz;',
                '<?php
use Foo\ {
    Bar, Baz
};',
            ],
            [
                '<?php
use Foo\Bar;
use Foo\Baz;',
                '<?php
use Foo\
{
    Bar, Baz
};',
            ],
            [
                '<?php
use function md5;
use function str_repeat;
use const true;
use const false;
use A;
use B;
',
                '<?php
use function md5, str_repeat;
use const true, false;
use A,B;
',
            ],
            [
                '<?php
use D\E;
use D\F;
use G\H;
use G\I/*1*//*2*/;
',
                '<?php
use D\{E,F,};
use G\{H,I/*1*/,/*2*/};
',
            ],
        ];
    }

    public function testWithConfig(): void
    {
        $expected = '<?php
use Space\Models\TestModelA;
use Space\Models\TestModelB;
use Space\Models\TestModel;';

        $input = '<?php
use Space\Models\ {
    TestModelA,
    TestModelB,
    TestModel,
};';

        $this->doTest($expected, $input);

        $this->fixer->configure(['group_to_single_imports' => false]);

        $this->doTest($input);
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): iterable
    {
        yield [
            "<?php\r\n    use FooA;\r\n    use FooB;",
            "<?php\r\n    use FooA, FooB;",
        ];
    }

    /**
     * @dataProvider provideFixPrePHP80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPrePHP80Cases(): iterable
    {
        yield [
            '<?php
use some\a\ClassA;
use some\a\ClassB;
use some\a\ClassC as C;
use function some\b\fn_a;
use function some\b\fn_b;
use function some\b\fn_c;
use const some\c\ConstA/**/as/**/E; /* group comment */
use const some\c\ConstB as D;
use const some\c\// use.,{}
ConstC;
use A\{B};
use D\E;
use D\F;
                ',
            '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use    function some\b\{fn_a, fn_b, fn_c};
use const/* group comment */some\c\{ConstA/**/as/**/ E   ,    ConstB   AS    D, '.'
// use.,{}
ConstC};
use A\{B};
use D\{E,F};
                ',
        ];

        yield 'messy comments' => [
            '<?php
use D\/*1*//*2*//*3*/E;
use D\/*4*//*5*//*6*//*7*//*8*//*9*/F/*10*//*11*//*12*/;
',
            '<?php
use D\{
/*1*//*2*//*3*/E,/*4*//*5*//*6*/
/*7*//*8*//*9*/F/*10*//*11*//*12*/
};
',
        ];
    }
}
