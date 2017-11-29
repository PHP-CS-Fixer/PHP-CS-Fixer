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
    public function testImportedStrictTypesFixWithoutReturn()
    {
        $expected = <<<'EOF'
<?php

use Foo\Bar;

class SomeClass
{
    public function doSomething(Bar $foo)
    {
    }
}
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo)
    {
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testImportedStrictTypesFixWithReturn()
    {
        if (PHP_VERSION_ID < 70000) {
            $this->markTestSkipped('The strict return type : operator is only avaiable from PHP 7.0.');
        }

        $expected = <<<'EOF'
<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(Bar $foo): Baz
    {
    }
}
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;
use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar $foo): \Foo\Bar\Baz
    {
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testNamespaceFixesWithoutReturn()
    {
        $expected = <<<'EOF'
<?php

namespace Foo\Bar;

class SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz)
    {
    }
}
EOF;

        $input = <<<'EOF'
<?php

namespace Foo\Bar;

class SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
    {
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testNamespaceFixesWithReturn()
    {
        if (PHP_VERSION_ID < 70000) {
            $this->markTestSkipped('The strict return type : operator is only avaiable from PHP 7.0.');
        }

        $expected = <<<'EOF'
<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(SomeClass $foo, Buz $buz, Zoof\Buz $barbuz): Baz
    {
    }
}
EOF;

        $input = <<<'EOF'
<?php

namespace Foo\Bar;

use Foo\Bar\Baz;

class SomeClass
{
    public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): \Foo\Bar\Baz
    {
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testMultiNamespaceFixesWithoutReturn()
    {
        $expected = <<<'EOF'
<?php
namespace Foo\Other {
}

namespace Foo\Bar {
    class SomeClass
    {
        public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz)
        {
        }
    }
}


EOF;

        $this->doTest($expected, null);
    }

    public function testMultiNamespaceFixesWithReturn()
    {
        if (PHP_VERSION_ID < 70000) {
            $this->markTestSkipped('The strict return type : operator is only avaiable from PHP 7.0.');
        }

        $expected = <<<'EOF'
<?php
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
}


EOF;

        $input = <<<'EOF'
<?php
namespace Foo\Other {
}

namespace Foo\Bar {
    use Foo\Bar\Baz;

    class SomeClass
    {
        public function doSomething(\Foo\Bar\SomeClass $foo, \Foo\Bar\Buz $buz, \Foo\Bar\Zoof\Buz $barbuz): \Foo\Bar\Baz
        {
        }
    }
}


EOF;

        $this->doTest($expected, $input);
    }

    public function testPartialNamespaces()
    {
        $expected = <<<'EOF'
<?php

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
}
EOF;

        $input = <<<'EOF'
<?php

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
}
EOF;

        $this->doTest($expected, $input);
    }
}
