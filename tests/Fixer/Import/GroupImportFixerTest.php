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

/**
 * @author Volodymyr Kupriienko <vldmr.kuprienko@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\GroupImportFixer
 */
final class GroupImportFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield [
            '<?php

namespace Test;

use Foo\{Bar, Baz, Test};
',
            '<?php

namespace Test;

use Foo\Bar;
use Foo\Baz;
use Foo\Test;
',
        ];

        yield [
            '<?php

use App\First;
use Test\Second;
use Foo\{Bar, Baz};
',
            '<?php

use App\First;
use Foo\Bar;
use Test\Second;
use Foo\Baz;
',
        ];

        yield [
            '<?php

use App\{First, Second};
use Foo\{Bar, Baz};
',
            '<?php

use Foo\Bar;
use Foo\Baz;
use App\First;
use App\Second;
',
        ];

        yield [
            '<?php

use App\{First, Second};
use Foo\{Bar, Baz};
',
            '<?php

use Foo\Bar;
use App\First;
use Foo\Baz;
use App\Second;
',
        ];

        yield [
            '<?php

use Foo\{Bar as Test, Baz};
use App;
',
            '<?php

use Foo\Bar as Test;
use Foo\Baz;
use App;
',
        ];

        yield [
            '<?php

use App\Repository\{Customer as Client, Profile, User};
',
            '<?php

use App\Repository\User;
use App\Repository\Profile;
use App\Repository\Customer as Client;
',
        ];

        yield [
            '<?php

use function Foo\{Bar, Baz, Test as Alias};
',
            '<?php

use function Foo\Bar;
use function Foo\Baz;
use function Foo\Test as Alias;
',
        ];

        yield [
            '<?php

use const Some\Place\{A, B, C as D};
',
            '<?php

use const Some\Place\A;
use const Some\Place\B;
use const Some\Place\C as D;
',
        ];

        yield [
            '<?php
use Foo\Bar;
use Foo\Baz\Lorem\Ipsum\Lets\Write\Some\More\Strings\{One, Two};
',
            '<?php
use Foo\Bar;
use Foo\Baz\Lorem\Ipsum\Lets\Write\Some\More\Strings\One;
use Foo\Baz\Lorem\Ipsum\Lets\Write\Some\More\Strings\Two;
',
        ];

        yield [
            '<?php
use Foo\Baz\John\Smith\Junior;
use Foo\{Bar, Baz};
use Foo\Baz\John\{Doe, Smith};
use Foo\Baz\Johnny\{DoeSecond, SmithSecond};
',
            '<?php
use Foo\Bar;
use Foo\Baz;
use Foo\Baz\John\Doe;
use Foo\Baz\John\Smith;
use Foo\Baz\John\Smith\Junior;
use Foo\Baz\Johnny\DoeSecond;
use Foo\Baz\Johnny\SmithSecond;
',
        ];

        yield [
            '<?php
use PhpCsFixer\Tokenizer\{AbstractTransformer, CT, Token, Tokens};
',
            '<?php
use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
',
        ];

        yield [
            '<?php
use Foo\{Bar, Baz};
',
            '<?php
use Foo\Bar;use Foo\Baz;
',
        ];

        yield [
            '<?php
use Foo\{Bar, Baz};
\DontTouch::me();
',
            '<?php
use Foo\Bar;use Foo\Baz;\DontTouch::me();
',
        ];

        yield [
            '<?php

use Foo\{Bar, Baz};
use ReflectionClass;
use ReflectionMethod;
',
            '<?php

use Foo\Bar;
use Foo\Baz;
use ReflectionClass;
use ReflectionMethod;
',
        ];

        yield [
            '<?php

use Foo\{Bar, Baz};
use \ReflectionClass;
use \ReflectionMethod;
',
            '<?php

use Foo\Bar;
use Foo\Baz;
use \ReflectionClass;
use \ReflectionMethod;
',
        ];

        yield [
            '<?php

use Framework\Support\{Arr, Collection};
use Framework\Database\ORM\{Model, SoftDeletes};
use Framework\Notifications\{Notifiable, Notification};
use Framework\Support\Facades\{DB, Log};
use Framework\Database\ORM\Relations\{BelongsTo, HasOne};
use Framework\Database\Query\JoinClause;
',
            '<?php

use Framework\Database\ORM\Model;
use Framework\Database\ORM\Relations\BelongsTo;
use Framework\Database\ORM\Relations\HasOne;
use Framework\Database\ORM\SoftDeletes;
use Framework\Database\Query\JoinClause;
use Framework\Notifications\Notifiable;
use Framework\Notifications\Notification;
use Framework\Support\Arr;
use Framework\Support\Collection;
use Framework\Support\Facades\DB;
use Framework\Support\Facades\Log;
',
        ];

        yield [
            '<?php

use Framework\Baz\Class6;
use Framework\Bar\{Class3, Class4, Class5};
use Framework\Foo\{Class1, Class2, Class7};
',
            '<?php

use Framework\Foo\Class1;
use Framework\Foo\Class2;
use Framework\Bar\Class3;
use Framework\Bar\Class4;
use Framework\Bar\Class5;
use Framework\Baz\Class6;
use Framework\Foo\Class7;
',
        ];

        yield [
            '<?php

use function Foo\baz;
use Foo\{Bar, Baz};
',
            '<?php

use Foo\Bar;
use function Foo\baz;
use Foo\Baz;
',
        ];

        yield [
            '<?php

use Foo\{D, E};
use function Foo\{a, b};
use Foo\Bar\Baz\{A, B};
use Foo\Bar\C;
',
            '<?php

use Foo\Bar\Baz\A;
use Foo\Bar\Baz\B;
use Foo\Bar\C;
use Foo\D;
use Foo\E;
use function Foo\a;
use function Foo\b;
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

/*1*//*2*//*3*//*4*//*5*//*6*/
/*7*//*8*//*9*//*10*//*11*//*12*/
/*13*//*14*//*15*//*16*//*17*/use A\{B, C, D};
/*18*/
',
            '<?php

/*1*/use/*2*/A/*3*/\/*4*/B/*5*/;/*6*/
/*7*/use/*8*/A/*9*/\/*10*/C/*11*/;/*12*/
/*13*/use/*14*/A/*15*/\/*16*/D/*17*/;/*18*/
',
        ];
    }
}
