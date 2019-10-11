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
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer
 */
final class GlobalNamespaceImportFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixImportConstantsCases
     */
    public function testFixImportConstants($expected, $input = null)
    {
        $this->fixer->configure(['import_constants' => true]);
        $this->doTest($expected, $input);
    }

    public function provideFixImportConstantsCases()
    {
        return [
            'non-global names' => [
                <<<'EXPECTED'
<?php
namespace Test;
echo FOO, \Bar\BAZ, namespace\FOO2;
EXPECTED
            ],
            'name already used [1]' => [
                <<<'EXPECTED'
<?php
namespace Test;
echo \FOO, FOO, \FOO;
EXPECTED
            ],
            'name already used [2]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use const Bar\FOO;
echo \FOO;
EXPECTED
            ],
            'name already used [3]' => [
                <<<'EXPECTED'
<?php
namespace Test;
const FOO = 1;
echo \FOO;
EXPECTED
            ],
            'without namespace / only import once' => [
                <<<'EXPECTED'
<?php

use const BAR;
use const FOO;
echo FOO, BAR, FOO;
EXPECTED
                ,
                <<<'INPUT'
<?php
echo \FOO, \BAR, \FOO;
INPUT
            ],
            'with namespace' => [
                <<<'EXPECTED'
<?php
namespace Test;
use const FOO;
use const BAR;
echo FOO, BAR;
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
echo \FOO, \BAR;
INPUT
            ],
            'with namespace with {} syntax' => [
                <<<'EXPECTED'
<?php
namespace Test {
use const FOO;
use const BAR;
    echo FOO, BAR;
}
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test {
    echo \FOO, \BAR;
}
INPUT
            ],
            'ignore other imported types' => [
                <<<'EXPECTED'
<?php
namespace Test;
use BAR;
use const FOO;
use const BAR;
echo FOO, BAR;
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use BAR;
echo \FOO, \BAR;
INPUT
            ],
            'respect already imported names [1]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use const BAR;
use const FOO;
echo FOO, BAR;
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use const BAR;
echo \FOO, \BAR;
INPUT
            ],
            'respect already imported names [2]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use const \BAR;
use const FOO;
echo FOO, BAR, BAR;
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use const \BAR;
echo \FOO, \BAR, BAR;
INPUT
            ],
            'handle case sensitivity' => [
                <<<'EXPECTED'
<?php
namespace Test;
use const fOO;
use const FOO;
use const Foo;
const foO = 1;
echo FOO, Foo;
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use const fOO;
const foO = 1;
echo \FOO, \Foo;
INPUT
            ],
            'handle aliased imports' => [
                <<<'EXPECTED'
<?php
namespace Test;
use const BAR as BAZ;
use const FOO;
echo FOO, BAZ;
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use const BAR as BAZ;
echo \FOO, \BAR;
INPUT
            ],
            'ignore class constants' => [
                <<<'EXPECTED'
<?php
namespace Test;
use const FOO;
class Bar {
    const FOO = 1;
}
echo FOO;
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
class Bar {
    const FOO = 1;
}
echo \FOO;
INPUT
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixImportFunctionsCases
     */
    public function testFixImportFunctions($expected, $input = null)
    {
        $this->fixer->configure(['import_functions' => true]);
        $this->doTest($expected, $input);
    }

    public function provideFixImportFunctionsCases()
    {
        return [
            'non-global names' => [
                <<<'EXPECTED'
<?php
namespace Test;
foo();
Bar\baz();
namespace\foo2();
EXPECTED
            ],
            'name already used [1]' => [
                <<<'EXPECTED'
<?php
namespace Test;
\foo();
Foo();
\foo();
EXPECTED
            ],
            'name already used [2]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use function Bar\foo;
\Foo();
EXPECTED
            ],
            'name already used [3]' => [
                <<<'EXPECTED'
<?php
namespace Test;
function foo() {}
\Foo();
EXPECTED
            ],
            'without namespace / only import once' => [
                <<<'EXPECTED'
<?php

use function bar;
use function foo;
foo();
bar();
Foo();
EXPECTED
                ,
                <<<'INPUT'
<?php
\foo();
\bar();
\Foo();
INPUT
            ],
            'with namespace' => [
                <<<'EXPECTED'
<?php
namespace Test;
use function foo;
use function bar;
foo();
bar();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
\foo();
\bar();
INPUT
            ],
            'with namespace with {} syntax' => [
                <<<'EXPECTED'
<?php
namespace Test {
use function foo;
use function bar;
    foo();
    bar();
}
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test {
    \foo();
    \bar();
}
INPUT
            ],
            'ignore other imported types' => [
                <<<'EXPECTED'
<?php
namespace Test;
use bar;
use function foo;
use function bar;
foo();
bar();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use bar;
\foo();
\bar();
INPUT
            ],
            'respect already imported names [1]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use function bar;
use function foo;
foo();
Bar();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use function bar;
\foo();
\Bar();
INPUT
            ],
            'respect already imported names [2]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use function \bar;
use function foo;
foo();
Bar();
bar();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use function \bar;
\foo();
\Bar();
bar();
INPUT
            ],
            'handle aliased imports' => [
                <<<'EXPECTED'
<?php
namespace Test;
use function bar as baz;
use function foo;
foo();
baz();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use function bar as baz;
\foo();
\Bar();
INPUT
            ],
            'ignore class methods' => [
                <<<'EXPECTED'
<?php
namespace Test;
use function foo;
class Bar {
    function foo() {}
}
foo();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
class Bar {
    function foo() {}
}
\foo();
INPUT
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixImportFunctions70Cases
     * @requires PHP 7.0
     */
    public function testFixImportFunctions70($expected, $input = null)
    {
        $this->fixer->configure(['import_functions' => true]);
        $this->doTest($expected, $input);
    }

    public function provideFixImportFunctions70Cases()
    {
        return [
            'name already used' => [
                <<<'EXPECTED'
<?php
namespace Test;
class Bar {
    function baz() {
        new class() {
            function baz() {
                function foo() {}
            }
        };
    }
}
\foo();
EXPECTED
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixImportClassesCases
     */
    public function testFixImportClasses($expected, $input = null)
    {
        $this->fixer->configure(['import_classes' => true]);
        $this->doTest($expected, $input);
    }

    public function provideFixImportClassesCases()
    {
        return [
            'non-global names' => [
                <<<'EXPECTED'
<?php
namespace Test;
new Foo();
new Bar\Baz();
new namespace\Foo2();

/** @var Foo|Bar\Baz $x */
$x = x();
EXPECTED
            ],
            'name already used [1]' => [
                <<<'EXPECTED'
<?php
namespace Test;
new \Foo();
new foo();

/** @var \Foo $foo */
$foo = new \Foo();
EXPECTED
            ],
            'name already used [2]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use Bar\foo;

/** @var \Foo $foo */
$foo = new \Foo();
EXPECTED
            ],
            'name already used [3]' => [
                <<<'EXPECTED'
<?php
namespace Test;
class foo {}

/** @var \Foo $foo */
$foo = new \Foo();
EXPECTED
            ],
            'name already used [4]' => [
                <<<'EXPECTED'
<?php
namespace Test;

/** @return array<string, foo> */
function x() {}

/** @var \Foo $foo */
$foo = new \Foo();
EXPECTED
            ],
            'without namespace / only import once' => [
                <<<'EXPECTED'
<?php

use Bar;
use Foo;
/** @var Foo $foo */
$foo = new foo();
new Bar();
FOO::baz();
EXPECTED
                ,
                <<<'INPUT'
<?php
/** @var \Foo $foo */
$foo = new \foo();
new \Bar();
\FOO::baz();
INPUT
            ],
            'with namespace' => [
                <<<'EXPECTED'
<?php
namespace Test;
use Bar;
use Baz;
use foo;

new Foo();
Bar::baz();

/** @return Baz<string, foo> */
function x() {}
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;

new \Foo();
\Bar::baz();

/** @return \Baz<string, \foo> */
function x() {}
INPUT
            ],
            'with namespace with {} syntax' => [
                <<<'EXPECTED'
<?php
namespace Test {
use Foo;
use Bar;
    new Foo();
    Bar::baz();
}
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test {
    new \Foo();
    \Bar::baz();
}
INPUT
            ],
            'ignore other imported types' => [
                <<<'EXPECTED'
<?php
namespace Test;
use function Bar;
use Foo;
use Bar;
new Foo();
Bar::baz();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use function Bar;
new \Foo();
\Bar::baz();
INPUT
            ],
            'respect already imported names [1]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use Bar;
use Foo;
new Foo();
bar::baz();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use Bar;
new \Foo();
\bar::baz();
INPUT
            ],
            'respect already imported names [2]' => [
                <<<'EXPECTED'
<?php
namespace Test;
use \Bar;
use Foo;
new Foo();
new bar();
new Bar();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use \Bar;
new \Foo();
new \bar();
new Bar();
INPUT
            ],
            'handle aliased imports' => [
                <<<'EXPECTED'
<?php
namespace Test;
use Bar as Baz;
use Foo;

new Foo();

/** @var Baz $bar */
$bar = new Baz();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use Bar as Baz;

new \Foo();

/** @var \bar $bar */
$bar = new \bar();
INPUT
            ],
            'handle typehints' => [
                <<<'EXPECTED'
<?php
namespace Test;
use Bar;
use Foo;
use Baz;
class Abc {
    function bar(Foo $a, Bar $b, foo &$c, Baz ...$d) {}
}
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
class Abc {
    function bar(\Foo $a, \Bar $b, \foo &$c, \Baz ...$d) {}
}
INPUT
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixImportClasses71Cases
     * @requires PHP 7.1
     */
    public function testFixImportClasses71($expected, $input = null)
    {
        $this->fixer->configure(['import_classes' => true]);
        $this->doTest($expected, $input);
    }

    public function provideFixImportClasses71Cases()
    {
        return [
            'handle typehints' => [
                <<<'EXPECTED'
<?php
namespace Test;
use Foo;
use Bar;
class Abc {
    function bar(?Foo $a): ?Bar {}
}
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
class Abc {
    function bar(?\Foo $a): ?\Bar {}
}
INPUT
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixFullyQualifyConstantsCases
     */
    public function testFixFullyQualifyConstants($expected, $input = null)
    {
        $this->fixer->configure(['import_constants' => false]);
        $this->doTest($expected, $input);
    }

    public function provideFixFullyQualifyConstantsCases()
    {
        return [
            'already fqn or sub namespace' => [
                <<<'EXPECTED'
<?php
use const FOO;
use const BAR;
echo \FOO, Baz\BAR;
EXPECTED
            ],
            'handle all occurrences' => [
                <<<'EXPECTED'
<?php
use const FOO;
use const BAR;
echo \FOO, \BAR, \FOO;
EXPECTED
                ,
                <<<'INPUT'
<?php
use const FOO;
use const BAR;
echo FOO, BAR, FOO;
INPUT
            ],
            'ignore other imports and non-imported names' => [
                <<<'EXPECTED'
<?php
namespace Test;
use FOO;
use const BAR;
use const Baz;
echo FOO, \BAR, BAZ, QUX;
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use FOO;
use const BAR;
use const Baz;
echo FOO, BAR, BAZ, QUX;
INPUT
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixFullyQualifyFunctionsCases
     */
    public function testFixFullyQualifyFunctions($expected, $input = null)
    {
        $this->fixer->configure(['import_functions' => false]);
        $this->doTest($expected, $input);
    }

    public function provideFixFullyQualifyFunctionsCases()
    {
        return [
            'already fqn or sub namespace' => [
                <<<'EXPECTED'
<?php
use function foo;
use function bar;
\foo();
Baz\bar();
EXPECTED
            ],
            'handle all occurrences' => [
                <<<'EXPECTED'
<?php
use function foo;
use function bar;
\foo();
\bar();
\Foo();
EXPECTED
                ,
                <<<'INPUT'
<?php
use function foo;
use function bar;
foo();
bar();
Foo();
INPUT
            ],
            'ignore other imports and non-imported names' => [
                <<<'EXPECTED'
<?php
namespace Test;
use foo;
use function bar;
foo();
\bar();
baz();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use foo;
use function bar;
foo();
bar();
baz();
INPUT
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixFullyQualifyClassesCases
     */
    public function testFixFullyQualifyClasses($expected, $input = null)
    {
        $this->fixer->configure(['import_classes' => false]);
        $this->doTest($expected, $input);
    }

    public function provideFixFullyQualifyClassesCases()
    {
        return [
            'already fqn or sub namespace' => [
                <<<'EXPECTED'
<?php
use Foo;
use Bar;

new \Foo();
Baz\Bar::baz();

/**
 * @param \Foo $foo
 * @param Baz\Bar $bar
 */
function abc(\Foo $foo, Baz\Bar $bar = null) {}
EXPECTED
            ],
            'handle all occurrences' => [
                <<<'EXPECTED'
<?php
use Foo;
use Bar;

new \Foo();
new \Bar();
\foo::baz();

/**
 * @param \Foo|string $foo
 * @param null|\Bar[] $bar
 * @return array<string, ?\Bar<int, \foo>>|null
 */
function abc($foo, \Bar $bar = null) {}
EXPECTED
                ,
                <<<'INPUT'
<?php
use Foo;
use Bar;

new Foo();
new Bar();
foo::baz();

/**
 * @param Foo|string $foo
 * @param null|Bar[] $bar
 * @return array<string, ?Bar<int, foo>>|null
 */
function abc($foo, Bar $bar = null) {}
INPUT
            ],
            'ignore other imports and non-imported names' => [
                <<<'EXPECTED'
<?php
namespace Test;
use function Foo;
use Bar;
new Foo();
new \Bar();
new Baz();
EXPECTED
                ,
                <<<'INPUT'
<?php
namespace Test;
use function Foo;
use Bar;
new Foo();
new Bar();
new Baz();
INPUT
            ],
        ];
    }
}
