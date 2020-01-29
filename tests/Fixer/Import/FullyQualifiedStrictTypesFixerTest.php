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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer
 */
final class FullyQualifiedStrictTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP 7.0
     *
     * @dataProvider provideCodeWithReturnTypesCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testCodeWithReturnTypes($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @requires PHP 7.1
     *
     * @dataProvider provideCodeWithReturnTypesCasesWithNullableCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testCodeWithReturnTypesWithNullable($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideCodeWithoutReturnTypesCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testCodeWithoutReturnTypes($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCodeWithReturnTypesCases()
    {
        return [
            'Import common strict types' => [
                '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(Bar $foo): Baz
    {
    }
}',
                '<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo): \Foo\Bar\Baz
    {
    }
}',
            ],
            'Test namespace fixes' => [
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz): Baz
    {
    }
}',
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): \Foo\Bar\Baz
    {
    }
}',
            ],
            'Partial class name looks like FQCN' => [
                '<?php

namespace One;

use Two\Three;

class Two
{
    /**
     * Note that for this example, the following classes exist:
     *
     * - One\Two
     * - One\Two\Three
     * - Two\Three\Four
     */
    public function three(Three\Four $four): Two\Three
    {
    }
}',
            ],
            'Test multi namespace fixes' => [
                '<?php
namespace Foo\Other {
}

namespace Foo\Bar {
    use Foo\Bar\Baz;

    class SomeClass
    {
        public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): Baz
        {
        }
    }
}',
            ],
            'Test fixes in interface' => [
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz): Baz;
}',
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): \Foo\Bar\Baz;
}',
            ],
            'Test fixes in trait' => [
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

trait SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz): Baz
    {
    }
}',
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

trait SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): \Foo\Bar\Baz
    {
    }
}',
            ],
            'Test fixes in regular functions' => [
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz): Baz
{
}',
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): \Foo\Bar\Baz
{
}',
            ],
        ];
    }

    public function provideCodeWithoutReturnTypesCases()
    {
        return [
            'Import common strict types' => [
                '<?php

use Foo\Bar;

class SomeClass
{
    public function doSomething(Bar $foo)
    {
    }
}',
                '<?php

use Foo\Bar;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo)
    {
    }
}',
            ],
            'Test namespace fixes' => [
                '<?php

namespace Foo\Bar;

class SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz)
    {
    }
}',
                '<?php

namespace Foo\Bar;

class SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
    {
    }
}',
            ],
            'Partial class name looks like FQCN' => [
                '<?php

namespace One;

use Two\Three;

class Two
{
    /**
     * Note that for this example, the following classes exist:
     *
     * - One\Two
     * - One\Two\Three
     * - Two\Three
     */
    public function three(Two\Three $three, Three $other)
    {
    }
}',
            ],

            'Test multi namespace fixes' => [
                '<?php
namespace Foo\Other {
}

namespace Foo\Bar {
    class SomeClass
    {
        public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
        {
        }
    }
}',
            ],
            'Test fixes in interface' => [
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz);
}',
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

interface SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz);
}',
            ],
            'Test fixes in trait' => [
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

trait SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz)
    {
    }
}',
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

trait SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
    {
    }
}',
            ],
            'Test fixes in regular functions' => [
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz)
{
}',
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
{
}',
            ],
            'Test partial namespace and use imports' => [
                '<?php

namespace Ping\Pong;

use Foo\Bar;
use Ping;
use Ping\Pong\Pang;
use Ping\Pong\Pyng\Pung;

class SomeClass
{
    public function doSomething(
        Ping\Something $something,
        Pung\Pang $pungpang,
        Pung $pongpung,
        Pang\Pung $pangpung,
        Pyng\Pung\Pong $pongpyngpangpang,
        Bar\Baz\Buz $bazbuz
    ){}
}',
                '<?php

namespace Ping\Pong;

use Foo\Bar;
use Ping;
use Ping\Pong\Pang;
use Ping\Pong\Pyng\Pung;

class SomeClass
{
    public function doSomething(
        \Ping\Something $something,
        \Ping\Pong\Pung\Pang $pungpang,
        \Ping\Pong\Pung $pongpung,
        \Ping\Pong\Pang\Pung $pangpung,
        \Ping\Pong\Pyng\Pung\Pong $pongpyngpangpang,
        \Foo\Bar\Baz\Buz $bazbuz
    ){}
}',
            ],
            'Test reference' => [
                '<?php
function withReference(Exception &$e) {}',
                '<?php
function withReference(\Exception &$e) {}',
            ],
        ];
    }

    public function provideCodeWithReturnTypesCasesWithNullableCases()
    {
        return [
            'Test namespace fixes with nullable types' => [
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, ?Zoof\Buz $barbuz): ?Baz
    {
    }
}',
                '<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, ?\Foo\Bar\Zoof\Buz $barbuz): ?\Foo\Bar\Baz
    {
    }
}',
            ],
            'Partial class name looks like FQCN' => [
                '<?php

namespace One;

use Two\Three;

class Two
{
    /**
     * Note that for this example, the following classes exist:
     *
     * - One\Two
     * - One\Two\Three
     * - Two\Three\Four
     */
    public function three(Three\Four $four): ?Two\Three
    {
    }
}',
            ],
        ];
    }
}
