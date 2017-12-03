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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\NoUnusedImportsFixer
 */
final class NoUnusedImportsFixerTest extends AbstractFixerTestCase
{
    public function testFix()
    {
        $expected = <<<'EOF'
<?php

use Foo\Bar;
use Foo\Bar\FooBar as FooBaz;
use SomeClass;

$a = new Bar();
$a = new FooBaz();
$a = new SomeClass();

use Symfony\Annotation\Template;
use Symfony\Doctrine\Entities\Entity;
use Symfony\Array123\ArrayInterface;

class AnnotatedClass
{
    /**
     * @Template(foobar=21)
     * @param Entity $foo
     */
    public function doSomething($foo)
    {
        $bar = $foo->toArray();
        /** @var ArrayInterface $bar */
    }
}
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;
use Foo\Bar\Baz;
use Foo\Bar\FooBar as FooBaz;
use Foo\Bar\Foo as Fooo;
use Foo\Bar\Baar\Baar;
use SomeClass;

$a = new Bar();
$a = new FooBaz();
$a = new SomeClass();

use Symfony\Annotation\Template;
use Symfony\Doctrine\Entities\Entity;
use Symfony\Array123\ArrayInterface;

class AnnotatedClass
{
    /**
     * @Template(foobar=21)
     * @param Entity $foo
     */
    public function doSomething($foo)
    {
        $bar = $foo->toArray();
        /** @var ArrayInterface $bar */
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testFixFunWithIndent()
    {
        $expected = <<<'EOF'
<?php

use Foo\Bar;
    $foo = 1;
use Foo\Bar\FooBar as FooBaz;
    use SomeClassIndented;

$a = new Bar();
$a = new FooBaz();
$a = new SomeClassIndented();

EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;
use Foo\Bar\Baz;
    $foo = 1;
use Foo\Bar\FooBar as FooBaz;
use Foo\Bar\Foo as Fooo;
use Foo\Bar\Baar\Baar;
    use SomeClassIndented;

$a = new Bar();
$a = new FooBaz();
$a = new SomeClassIndented();

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixUseInTheSameNamespace()
    {
        $expected = <<<'EOF'
<?php

namespace Foo\Bar\FooBar;

use Foo\Bar\FooBar\Foo as Fooz;
use Foo\Bar\FooBar\Aaa\Bbb;
use XYZ\FQCN_XYZ;

$a = new Baz();
$b = new Fooz();
$c = new Bar\Fooz();
$d = new Bbb();
$e = new FQCN_Babo();
$f = new FQCN_XYZ();
EOF;

        $input = <<<'EOF'
<?php

namespace Foo\Bar\FooBar;

use Foo\Bar\FooBar\Baz;
use Foo\Bar\FooBar\Foo as Fooz;
use Foo\Bar\FooBar\Bar;
use Foo\Bar\FooBar\Aaa\Bbb;
use \Foo\Bar\FooBar\FQCN_Babo;
use XYZ\FQCN_XYZ;

$a = new Baz();
$b = new Fooz();
$c = new Bar\Fooz();
$d = new Bbb();
$e = new FQCN_Babo();
$f = new FQCN_XYZ();
EOF;

        $this->doTest($expected, $input);

        $expected = <<<'EOF'
<?php namespace App\Http\Controllers;


EOF;

        $input = <<<'EOF'
<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

EOF;

        $this->doTest($expected, $input);

        // the fixer doesn't support file with multiple namespace - test if we don't remove imports in that case
        $expected = <<<'EOF'
<?php

namespace Foooooooo;
namespace Foo;

use Foo\Bar;
use Foo\Baz;

$a = new Bar();
$b = new Baz();
EOF;

        $this->doTest($expected);
    }

    public function testMultipleUseStatements()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;

use BarB, BarC as C, BarD;
use BarE;

$c = new D();
$e = new BarE();
EOF;

        $input = <<<'EOF'
<?php

namespace Foo;

use Bar;
use BarA;
use BarB, BarC as C, BarD;
use BarB2;
use BarB\B2;
use BarE;

$c = new D();
$e = new BarE();
EOF;

        $this->doTest($expected, $input);
    }

    public function testNamespaceWithBraces()
    {
        $expected = <<<'EOF'
<?php

namespace Foo\Bar\FooBar {
    use Foo\Bar\FooBar\Foo as Fooz;
    use Foo\Bar\FooBar\Aaa\Bbb;

    $a = new Baz();
    $b = new Fooz();
    $c = new Bar\Fooz();
    $d = new Bbb();
}
EOF;

        $input = <<<'EOF'
<?php

namespace Foo\Bar\FooBar {
    use Foo\Bar\FooBar\Baz;
    use Foo\Bar\FooBar\Foo as Fooz;
    use Foo\Bar\FooBar\Bar;
    use Foo\Bar\FooBar\Aaa\Bbb;

    $a = new Baz();
    $b = new Fooz();
    $c = new Bar\Fooz();
    $d = new Bbb();
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testTrailingSpaces()
    {
        $expected = <<<'EOF'
<?php

use Foo\Bar ;
use Foo\Bar\FooBar as FooBaz ;

$a = new Bar();
$a = new FooBaz();
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar ;
use Foo\Bar\FooBar as FooBaz ;
use Foo\Bar\Foo as Fooo ;
use SomeClass ;

$a = new Bar();
$a = new FooBaz();
EOF;

        $this->doTest($expected, $input);
    }

    public function testTraits()
    {
        $expected = <<<'EOF'
<?php

use Foo as Bar;

class MyParent
{
    use MyTrait1;
use MyTrait2;
    use Bar;
}
EOF;

        $input = <<<'EOF'
<?php

use Foo;
use Foo as Bar;

class MyParent
{
    use MyTrait1;
use MyTrait2;
    use Bar;
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testFunctionUse()
    {
        $expected = <<<'EOF'
<?php

use Foo;

$f = new Foo();
$a = function ($item) use ($f) {
    return !in_array($item, $f);
};
EOF;

        $input = <<<'EOF'
<?php

use Foo;
use Bar;

$f = new Foo();
$a = function ($item) use ($f) {
    return !in_array($item, $f);
};
EOF;

        $this->doTest($expected, $input);
    }

    public function testSimilarNames()
    {
        $expected = <<<'EOF'
<?php

use SomeEntityRepository;

class SomeService
{
    public function __construct(SomeEntityRepository $repo)
    {
        $this->repo = $repo;
    }
}
EOF;

        $input = <<<'EOF'
<?php

use SomeEntityRepository;
use SomeEntity;

class SomeService
{
    public function __construct(SomeEntityRepository $repo)
    {
        $this->repo = $repo;
    }
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testVariableName()
    {
        $expected = <<<'EOF'
<?php


$bar = null;
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;

$bar = null;
EOF;

        $this->doTest($expected, $input);
    }

    public function testPropertyName()
    {
        $expected = <<<'EOF'
<?php


$foo->bar = null;
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;

$foo->bar = null;
EOF;

        $this->doTest($expected, $input);
    }

    public function testNamespacePart()
    {
        $expected = <<<'EOF'
<?php


new \Baz\Bar();
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;

new \Baz\Bar();
EOF;

        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideFixUseInStringCases
     */
    public function testFixUseInString($expected)
    {
        $this->doTest($expected);
    }

    public function provideFixUseInStringCases()
    {
        $expected1 = <<<'EOF'
<?php
$x=<<<'EOA'
use a;
use b;
EOA;

EOF;

        $expected2 = <<<'EOF'
<?php
$x='
use a;
use b;
';
EOF;

        $expected3 = <<<'EOF'
<?php
$x="
use a;
use b;
";
EOF;

        $expected4 = <<<'EOF'
<?php
namespace A;
use \SplFileInfo;
new SplFileInfo(__FILE__);
EOF;

        return array(
            array($expected1),
            array($expected2),
            array($expected3),
            array($expected4),
        );
    }

    public function testUseAsLastStatement()
    {
        $expected = <<<'EOF'
<?php

EOF;

        $input = <<<'EOF'
<?php
use Bar\Finder;
EOF;

        $this->doTest($expected, $input);
    }

    public function testUseWithSameLastPartThatIsInNamespace()
    {
        $expected = <<<'EOF'
<?php

namespace Foo\Finder;


EOF;

        $input = <<<'EOF'
<?php

namespace Foo\Finder;

use Bar\Finder;
EOF;

        $this->doTest($expected, $input);
    }

    public function testFoo()
    {
        $expected = <<<'EOF'
<?php
namespace Aaa;


class Ddd
{
}

EOF;

        $input = <<<'EOF'
<?php
namespace Aaa;

use Aaa\Bbb;
use Ccc;

class Ddd
{
}

EOF;

        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCloseTagCases
     */
    public function testFixABC($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCloseTagCases()
    {
        return array(
            array(
                '<?php
?>inline content<?php ?>',
                '<?php
     use A\AA;
     use B\C?>inline content<?php use A\D; use E\F ?>',
            ),
            array(
                '<?php ?>',
                '<?php use A\B;?>',
            ),
            array(
                '<?php ?>',
                '<?php use A\B?>',
            ),
        );
    }

    /**
     * @requires PHP 7.0
     */
    public function testPHP70()
    {
        $expected = <<<'EOF'
<?php
use some\a\{ClassD};
use some\b\{ClassA, ClassB, ClassC as C};
use function some\c\{fn_a, fn_b, fn_c};
use const some\d\{ConstA, ConstB, ConstC};

new CLassD();
echo fn_a();
EOF;
        $this->doTest($expected);
    }

    public function testFixWithComments()
    {
        $input = '<?php
use# 1
\# 2
Exception# 3
# 4





  ;
use /**/A\B/**/;
  echo 1;
  new B();
';

        $expected = '<?php
# 1
# 2
# 3
# 4
  use /**/A\B/**/;
  echo 1;
  new B();
';

        $this->doTest($expected, $input);
    }

    public function testWithSameNamespaceImportAndUnusedImport()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;

use Bar\C;
/* test */

abstract class D extends A implements C
{
}

EOF;

        $input = <<<'EOF'
<?php

namespace Foo;

use Bar\C;
use Foo\A;
use Foo\Bar\B /* test */ ;

abstract class D extends A implements C
{
}

EOF;

        $this->doTest($expected, $input);
    }

    public function testWithSameNamespaceImportAndUnusedImportAfterNamespaceStatement()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;

use Foo\Bar\C;

abstract class D extends A implements C
{
}

EOF;

        $input = <<<'EOF'
<?php

namespace Foo;

use Foo\A;
use Foo\Bar\B;
use Foo\Bar\C;

abstract class D extends A implements C
{
}

EOF;

        $this->doTest($expected, $input);
    }

    public function testWhenImportedClassNameIsPrefixWithDashOfConstant()
    {
        $expected = <<<'EOF'
<?php


class Dummy
{
    const C = 'bar-bados';
}
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;

class Dummy
{
    const C = 'bar-bados';
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testWhenImportedClassNameIsSuffixWithDashOfConstant()
    {
        $expected = <<<'EOF'
<?php


class Dummy
{
    const C = 'tool-bar';
}
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;

class Dummy
{
    const C = 'tool-bar';
}
EOF;

        $this->doTest($expected, $input);
    }

    public function testWhenImportedClassNameIsInsideWithDashOfConstant()
    {
        $expected = <<<'EOF'
<?php


class Dummy
{
    const C = 'tool-bar-bados';
}
EOF;

        $input = <<<'EOF'
<?php

use Foo\Bar;

class Dummy
{
    const C = 'tool-bar-bados';
}
EOF;

        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideWhenImportedClassIsUsedForConstantsCases
     */
    public function testWhenImportedClassIsUsedForConstants($expected)
    {
        $this->doTest($expected);
    }

    public function provideWhenImportedClassIsUsedForConstantsCases()
    {
        return array(
            array(
                '<?php
use A\ABC;
$a = 5-ABC::Test;
$a = 5-ABC::Test-5;
$a = ABC::Test-5;
',
            ),
            array(
                '<?php
use A\ABC;
$a = 5-ABC::Test;
$a = 5-ABC::Test-5;
',
            ),
            array(
                '<?php
use A\ABC;
$a = 5-ABC::Test;
',
            ),
            array('<?php
use A\ABC;
$a = ABC::Test-5;
',
            ),
            array('<?php
use A\ABC;
$a = 5-ABC::Test-5;
',
            ),
            array('<?php
use A\ABC;
$b = $a-->ABC::Test;
',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix72Cases
     * @requires PHP 7.2
     */
    public function testFix72($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix72Cases()
    {
        return array(
            array( // TODO test shows lot of cases where imports are not removed while could be
                '<?php use A\{B,};
use some\y\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use C\{D,E,};

    echo ConstA.ConstB.ConstC,ConstF;
    echo ConstBB.ConstCC;
    fn_a(ClassA::test, new C());
',
                '<?php use A\{B,};
use some\y\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use C\{D,E,};
use Z;

    echo ConstA.ConstB.ConstC,ConstF;
    echo ConstBB.ConstCC;
    fn_a(ClassA::test, new C());
',
            ),
        );
    }

    public function testFunctionsInTheGlobalNamespaceShouldNotBeRemoved()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;

use function is_int;

is_int(1);

EOF;

        $input = <<<'EOF'
<?php

namespace Foo;

use function is_int;
use function is_float;

is_int(1);

EOF;

        $this->doTest($expected, $input);
    }

    public function testConstantsInTheGlobalNamespaceShouldNotBeRemoved()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;

use const PHP_INT_MAX;

echo PHP_INT_MAX;

EOF;

        $input = <<<'EOF'
<?php

namespace Foo;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

echo PHP_INT_MAX;

EOF;

        $this->doTest($expected, $input);
    }

    public function testFunctionsInTheGlobalNamespaceShouldNotBeRemovedEvenWhenDeclarationHasNewLinesAndIsUppercase()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;use/**/FUNCTION#1
is_int;#2

is_int(1);

EOF;

        $input = <<<'EOF'
<?php

namespace Foo;use/**/FUNCTION#1
is_int;#2
use function
    is_float;
use
    const
        PHP_INT_MIN;

is_int(1);

EOF;

        $this->doTest($expected, $input);
    }
}
