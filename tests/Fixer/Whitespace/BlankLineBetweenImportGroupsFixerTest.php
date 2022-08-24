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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\BlankLineBetweenImportGroupsFixer
 */
final class BlankLineBetweenImportGroupsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixTypesOrderAndWhitespaceCases
     */
    public function testFixTypesOrderAndNewlines(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixTypesOrderAndWhitespaceCases(): iterable
    {
        yield [
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use some\b\{
    ClassF,
    ClassG
};
use Some\Cloz;
use Aaa\Bbb;

use const some\a\{ConstD};
use const some\a\{ConstA};
use const some\a\{ConstB, ConstC as CC};
use const some\b\{ConstE};

use function some\f\{fn_g, fn_h, fn_i};
use function some\c\{fn_f};
use function some\a\{fn_x};
use function some\b\{fn_c, fn_d, fn_e};
use function some\a\{fn_a, fn_b};
',
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use some\b\{
    ClassF,
    ClassG
};
use Some\Cloz;
use Aaa\Bbb;
use const some\a\{ConstD};
use const some\a\{ConstA};
use const some\a\{ConstB, ConstC as CC};
use const some\b\{ConstE};
use function some\f\{fn_g, fn_h, fn_i};
use function some\c\{fn_f};
use function some\a\{fn_x};
use function some\b\{fn_c, fn_d, fn_e};
use function some\a\{fn_a, fn_b};
',
        ];

        yield [
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;

use function some\f\{fn_g, fn_h, fn_i};

use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;

use function some\c\{fn_f};

use some\b\{
    ClassF,
    ClassG
};

use const some\a\{ConstD};

use Some\Cloz;

use function some\a\{fn_x};

use const some\a\{ConstA};

use function some\b\{fn_c, fn_d, fn_e};

use const some\a\{ConstB, ConstC as CC};

use Aaa\Bbb;

use const some\b\{ConstE};

use function some\a\{fn_a, fn_b};
',
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;
use function some\f\{fn_g, fn_h, fn_i};
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use function some\c\{fn_f};
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstD};
use Some\Cloz;
use function some\a\{fn_x};
use const some\a\{ConstA};
use function some\b\{fn_c, fn_d, fn_e};
use const some\a\{ConstB, ConstC as CC};
use Aaa\Bbb;
use const some\b\{ConstE};
use function some\a\{fn_a, fn_b};
',
        ];

        yield [
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;
#use function some\f\{fn_g, fn_h, fn_i};
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;

use function some\c\{fn_f};

use some\b\{
    ClassF,
    ClassG
};

use const some\a\{ConstD};

use Some\Cloz;

use function some\a\{fn_x};

/** Import ConstA to do some nice magic */
use const some\a\{ConstA};

use function some\b\{fn_c, fn_d, fn_e};

use const some\a\{ConstB, ConstC as CC};

use Aaa\Bbb;

use const some\b\{ConstE};

use function some\a\{fn_a, fn_b};
',
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;
#use function some\f\{fn_g, fn_h, fn_i};
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use function some\c\{fn_f};
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstD};
use Some\Cloz;
use function some\a\{fn_x};
/** Import ConstA to do some nice magic */
use const some\a\{ConstA};
use function some\b\{fn_c, fn_d, fn_e};
use const some\a\{ConstB, ConstC as CC};
use Aaa\Bbb;
use const some\b\{ConstE};
use function some\a\{fn_a, fn_b};
',
        ];

        yield [
            '<?php
/**
use Aaa\Ccc;
use Foo\Zar\Baz;
 */
use function some\f\{fn_g, fn_h, fn_i};

use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;

use function some\c\{fn_f};

use some\b\{
    ClassF,
    ClassG
};

use const some\a\{ConstD};

use Some\Cloz;

// Ignore the following content
// use function some\a\{fn_x};
// use const some\a\{ConstA};

use function some\b\{fn_c, fn_d, fn_e};

use const some\a\{ConstB, ConstC as CC};

use Aaa\Bbb;

use const some\b\{ConstE};

use function some\a\{fn_a, fn_b};
',
            '<?php
/**
use Aaa\Ccc;
use Foo\Zar\Baz;
 */
use function some\f\{fn_g, fn_h, fn_i};

use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;

use function some\c\{fn_f};

use some\b\{
    ClassF,
    ClassG
};

use const some\a\{ConstD};

use Some\Cloz;
// Ignore the following content
// use function some\a\{fn_x};
// use const some\a\{ConstA};

use function some\b\{fn_c, fn_d, fn_e};

use const some\a\{ConstB, ConstC as CC};

use Aaa\Bbb;

use const some\b\{ConstE};

use function some\a\{fn_a, fn_b};
',
        ];

        yield [
            '<?php
use Aaa\Ccc;

/*use Foo\Zar\Baz;
use function some\f\{fn_g, fn_h, fn_i};
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use function some\c\{fn_f};
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstD};
use Some\Cloz;
use function some\a\{fn_x};
use const some\a\{ConstA};
use function some\b\{fn_c, fn_d, fn_e};
use const some\a\{ConstB, ConstC as CC};
use Aaa\Bbb;
use const some\b\{ConstE};
*/
use function some\a\{fn_a, fn_b};
',
            '<?php
use Aaa\Ccc;
/*use Foo\Zar\Baz;
use function some\f\{fn_g, fn_h, fn_i};
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use function some\c\{fn_f};
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstD};
use Some\Cloz;
use function some\a\{fn_x};
use const some\a\{ConstA};
use function some\b\{fn_c, fn_d, fn_e};
use const some\a\{ConstB, ConstC as CC};
use Aaa\Bbb;
use const some\b\{ConstE};
*/
use function some\a\{fn_a, fn_b};
',
        ];

        yield [
            '<?php
use Aaa\Ccc;

use function some\a\{fn_a, fn_b};
use function some\b\{
    fn_c,
    fn_d
};
',
            '<?php
use Aaa\Ccc;



use function some\a\{fn_a, fn_b};
use function some\b\{
    fn_c,
    fn_d
};
',
        ];

        yield [
            '<?php
use Aaa\Ccc;

use function some\a\{fn_a, fn_b}; // Do this because of reasons
use function some\b\{
    fn_c,
    fn_d
};
',
            '<?php
use Aaa\Ccc;



use function some\a\{fn_a, fn_b}; // Do this because of reasons
use function some\b\{
    fn_c,
    fn_d
};
',
        ];

        yield [
            '<?php
use Aaa\Ccc;

/** Beginning of line comment as well */use function some\a\{fn_a, fn_b};
use function some\b\{
    fn_c,
    fn_d
};
',
            '<?php
use Aaa\Ccc;



/** Beginning of line comment as well */use function some\a\{fn_a, fn_b};
use function some\b\{
    fn_c,
    fn_d
};
',
        ];

        yield [
            '<?php

use /* x */ function /* x */ Psl\Str\trim;

use /* x */ Psl\Str /* x */;

use /* x */ const /* x */ Psl\Str\OTHER_ALPHABET;
use /* x */ const /* x */ Psl\Str\ALPHABET;

use /* x */ function /* x */ Psl\Str\ /* x */ {
    /* x */ trim_left /* x */,
    /* x */ trim_right /* x */,
};
',
            '<?php

use /* x */ function /* x */ Psl\Str\trim;
use /* x */ Psl\Str /* x */;
use /* x */ const /* x */ Psl\Str\OTHER_ALPHABET;
use /* x */ const /* x */ Psl\Str\ALPHABET;
use /* x */ function /* x */ Psl\Str\ /* x */ {
    /* x */ trim_left /* x */,
    /* x */ trim_right /* x */,
};
',
        ];

        yield 'lots of inserts in same namespace' => [
            '<?php
namespace A\B6 {
    use C1\B1;

use const C6\Z1;

use C2\B2;

use const C7\Z2;

use C3\B3;

use const C8\Z3;

use C4\B4;

use const C9\Z4;

use C5\B5;

use const C0\Z5;
}
            ',
            '<?php
namespace A\B6 {
    use C1\B1;use const C6\Z1;
    use C2\B2;use const C7\Z2;
    use C3\B3;use const C8\Z3;
    use C4\B4;use const C9\Z4;
    use C5\B5;use const C0\Z5;
}
            ',
        ];

        yield 'lots of inserts in multiple namespaces' => [
            '<?php
namespace A\B1 {
    use C\B;

use const C\Z;
}
namespace A\B2 {
    use C\B;

use const C\Z;
}
namespace A\B3 {
    use C\B;

use const C\Z;
}
namespace A\B4 {
    use C\B;

use const C\Z;
}
namespace A\B5 {
    use C\B;

use const C\Z;
}
            ',
            '<?php
namespace A\B1 {
    use C\B;use const C\Z;
}
namespace A\B2 {
    use C\B;use const C\Z;
}
namespace A\B3 {
    use C\B;use const C\Z;
}
namespace A\B4 {
    use C\B;use const C\Z;
}
namespace A\B5 {
    use C\B;use const C\Z;
}
            ',
        ];

        yield [
            '<?php use A\B;    /*1*/

use const C\D;',
            '<?php use A\B;    /*1*/      use const C\D;',
        ];

        yield [
            '<?php
namespace Foo;
use A\B; /* foo */  /* A */ /* B */  # X

use const C\D; // bar
',
            '<?php
namespace Foo;
use A\B; /* foo */  /* A */ /* B */  # X
use const C\D; // bar
',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php

use Some\/**Namespace*/Weird\Be/**ha*/;

use function Other\/**Namespace*/Weird\/**Comments*/have;
',
            '<?php

use Some\/**Namespace*/Weird\Be/**ha*/;
use function Other\/**Namespace*/Weird\/**Comments*/have;
',
        ];
    }
}
