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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocFullyQualifiedTypesFixer
 */
final class PhpdocFullyQualifiedTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixDocBlockCases
     */
    public function testFixDocBlock($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixDocBlockCases()
    {
        return [
            'Import common strict types' => [
                '<?php

use Foo\Bar;

class SomeClass
{
    /**
     * @param Bar $foo
     */
    public function doSomething(\Foo\Bar $foo)
    {
    }
}',
                '<?php

use Foo\Bar;

class SomeClass
{
    /**
     * @param \Foo\Bar $foo
     */
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
    /**
     * @param SomeClass $foo
     * @param Buz $buz
     * @param Zoof\Buz $barbuz
     */
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
    {
    }
}',
                '<?php

namespace Foo\Bar;

class SomeClass
{
    /**
     * @param \Foo\Bar\SomeClass $foo
     * @param \Foo\Bar\Buz $buz
     * @param \Foo\Bar\Zoof\Buz $barbuz
     */
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
        /**
         * @param \Foo\Bar\SomeClass $foo
         * @param \Foo\Bar\Buz $buz
         * @param \Foo\Bar\Zoof\Buz $barbuz
         */
        public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
        {
        }
    }
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
    /**
     * @param Ping\Something $something,
     * @param Pung\Pang $pungpang,
     * @param Pung $pongpung,
     * @param Pang\Pung $pangpung,
     * @param Pyng\Pung\Pong $pongpyngpangpang,
     * @param Bar\Baz\Buz $bazbuz
     */
    public function doSomething(
        \Ping\Something $something,
        \Ping\Pong\Pung\Pang $pungpang,
        \Ping\Pong\Pung $pongpung,
        \Ping\Pong\Pang\Pung $pangpung,
        \Ping\Pong\Pyng\Pung\Pong $pongpyngpangpang,
        \Foo\Bar\Baz\Buz $bazbuz
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
    /**
     * @param \Ping\Something $something,
     * @param \Ping\Pong\Pung\Pang $pungpang,
     * @param \Ping\Pong\Pung $pongpung,
     * @param \Ping\Pong\Pang\Pung $pangpung,
     * @param \Ping\Pong\Pyng\Pung\Pong $pongpyngpangpang,
     * @param \Foo\Bar\Baz\Buz $bazbuz
     */
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
/**
 * @return Exception
 */
function withReference(\Exception &$e) {}',
                '<?php
/**
 * @return \Exception
 */
function withReference(\Exception &$e) {}',
            ],
            'Skip reserved types' => [
                '<?php
/**
 * @param array $a
 * @param \int $b
 *
 * @return object
 */
function foo($a, $b) {}',
            ],
            'Handle union types' => [
                '<?php
use Foo\Bar;

/**
 * @return null|int|Bar
 */
function foo() {}',
                '<?php
use Foo\Bar;

/**
 * @return null|int|\Foo\Bar
 */
function foo() {}',
            ],
        ];
    }
}
