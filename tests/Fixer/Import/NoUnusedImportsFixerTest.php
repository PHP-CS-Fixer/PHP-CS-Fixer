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
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'simple' => [
                <<<'EOF'
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
EOF
                ,
                <<<'EOF'
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
EOF
                ,
            ],
            'with_indents' => [
                <<<'EOF'
<?php

use Foo\Bar;
    $foo = 1;
use Foo\Bar\FooBar as FooBaz;
    use SomeClassIndented;

$a = new Bar();
$a = new FooBaz();
$a = new SomeClassIndented();

EOF
                ,
                <<<'EOF'
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

EOF
                ,
            ],
            'in_same_namespace_1' => [
                <<<'EOF'
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
EOF
                ,
                <<<'EOF'
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
EOF
                ,
            ],
            'in_same_namespace_2' => [
                <<<'EOF'
<?php namespace App\Http\Controllers;


EOF
                ,
                <<<'EOF'
<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

EOF
                ,
            ],
            'in_same_namespace_multiple_1' => [
                <<<'EOF'
<?php

namespace Foooooooo;
namespace Foo;

use Foooooooo\Baaaaz;

$a = new Bar();
$b = new Baz();
$c = new Baaaaz();
EOF
                ,
                <<<'EOF'
<?php

namespace Foooooooo;
namespace Foo;

use Foo\Bar;
use Foo\Baz;
use Foooooooo\Baaaar;
use Foooooooo\Baaaaz;

$a = new Bar();
$b = new Baz();
$c = new Baaaaz();
EOF
                ,
            ],
            'in_same_namespace_multiple_2' => [
                <<<'EOF'
<?php

namespace Foooooooo;

use Foo\Bar;

$a = new Baaaar();
$b = new Baaaaz();
$c = new Bar();

namespace Foo;

use Foooooooo\Baaaaz;

$a = new Bar();
$b = new Baz();
$c = new Baaaaz();
EOF
                ,
                <<<'EOF'
<?php

namespace Foooooooo;

use Foo\Bar;
use Foo\Baz;
use Foooooooo\Baaaar;
use Foooooooo\Baaaaz;

$a = new Baaaar();
$b = new Baaaaz();
$c = new Bar();

namespace Foo;

use Foo\Bar;
use Foo\Baz;
use Foooooooo\Baaaar;
use Foooooooo\Baaaaz;

$a = new Bar();
$b = new Baz();
$c = new Baaaaz();
EOF
                ,
            ],
            'in_same_namespace_multiple_braces' => [
                <<<'EOF'
<?php

namespace Foooooooo
{
    use Foo\Bar;

    $a = new Baaaar();
    $b = new Baaaaz();
    $c = new Bar();
}

namespace Foo
{
    use Foooooooo\Baaaaz;

    $a = new Bar();
    $b = new Baz();
    $c = new Baaaaz();
}
EOF
                ,
                <<<'EOF'
<?php

namespace Foooooooo
{
    use Foo\Bar;
    use Foo\Baz;
    use Foooooooo\Baaaar;
    use Foooooooo\Baaaaz;

    $a = new Baaaar();
    $b = new Baaaaz();
    $c = new Bar();
}

namespace Foo
{
    use Foo\Bar;
    use Foo\Baz;
    use Foooooooo\Baaaar;
    use Foooooooo\Baaaaz;

    $a = new Bar();
    $b = new Baz();
    $c = new Baaaaz();
}
EOF
                ,
            ],
            'multiple_use' => [
                <<<'EOF'
<?php

namespace Foo;

use BarB, BarC as C, BarD;
use BarE;

$c = new D();
$e = new BarE();
EOF
                ,

                <<<'EOF'
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
EOF
                ,
            ],
            'with_braces' => [
                <<<'EOF'
<?php

namespace Foo\Bar\FooBar {
    use Foo\Bar\FooBar\Foo as Fooz;
    use Foo\Bar\FooBar\Aaa\Bbb;

    $a = new Baz();
    $b = new Fooz();
    $c = new Bar\Fooz();
    $d = new Bbb();
}
EOF
                ,
                <<<'EOF'
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
EOF
                ,
            ],
            'trailing_spaces' => [
                <<<'EOF'
<?php

use Foo\Bar ;
use Foo\Bar\FooBar as FooBaz ;

$a = new Bar();
$a = new FooBaz();
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar ;
use Foo\Bar\FooBar as FooBaz ;
use Foo\Bar\Foo as Fooo ;
use SomeClass ;

$a = new Bar();
$a = new FooBaz();
EOF
                ,
            ],
            'traits' => [
                <<<'EOF'
<?php

use Foo as Bar;

class MyParent
{
    use MyTrait1;
use MyTrait2;
    use Bar;
}
EOF
                ,
                <<<'EOF'
<?php

use Foo;
use Foo as Bar;

class MyParent
{
    use MyTrait1;
use MyTrait2;
    use Bar;
}
EOF
                ,
            ],
            'function_use' => [
                <<<'EOF'
<?php

use Foo;

$f = new Foo();
$a = function ($item) use ($f) {
    return !in_array($item, $f);
};
EOF
                ,
                <<<'EOF'
<?php

use Foo;
use Bar;

$f = new Foo();
$a = function ($item) use ($f) {
    return !in_array($item, $f);
};
EOF
                ,
            ],
            'similar_names' => [
                <<<'EOF'
<?php

use SomeEntityRepository;

class SomeService
{
    public function __construct(SomeEntityRepository $repo)
    {
        $this->repo = $repo;
    }
}
EOF
                ,
                <<<'EOF'
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
EOF
                ,
            ],
            'variable_name' => [
                <<<'EOF'
<?php


$bar = null;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar;

$bar = null;
EOF
                ,
            ],
            'property_name' => [
                <<<'EOF'
<?php


$foo->bar = null;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar;

$foo->bar = null;
EOF
                ,
            ],
            'constant_name' => [
                <<<'EOF'
<?php


class Baz
{
    const BAR = 0;
}
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar;

class Baz
{
    const BAR = 0;
}
EOF
                ,
            ],
            'namespace_part' => [
                <<<'EOF'
<?php


new \Baz\Bar();
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar;

new \Baz\Bar();
EOF
                ,
            ],
            'use_in_string_1' => [
                <<<'EOF'
<?php
$x=<<<'EOA'
use a;
use b;
EOA;

EOF
                ,
            ],
            'use_in_string_2' => [
                <<<'EOF'
<?php
$x='
use a;
use b;
';
EOF
                ,
            ],
            'use_in_string_3' => [
                <<<'EOF'
<?php
$x="
use a;
use b;
";
EOF
                ,
            ],
            'import_in_global_namespace' => [
                <<<'EOF'
<?php
namespace A;
use \SplFileInfo;
new SplFileInfo(__FILE__);
EOF
                ,
            ],
            'use_as_last_statement' => [
                <<<'EOF'
<?php

EOF
                ,

                <<<'EOF'
<?php
use Bar\Finder;
EOF
                ,
            ],
            'use_with_same_last_part_that_is_in_namespace' => [
                <<<'EOF'
<?php

namespace Foo\Finder;


EOF
                ,
                <<<'EOF'
<?php

namespace Foo\Finder;

use Bar\Finder;
EOF
                ,
            ],
            'used_use_with_same_last_part_that_is_in_namespace' => [
                <<<'EOF'
<?php

namespace Foo\Finder;

use Bar\Finder;

class Baz extends Finder
{
}
EOF
                ,
            ],
            'foo' => [
                <<<'EOF'
<?php
namespace Aaa;


class Ddd
{
}

EOF
                ,
                <<<'EOF'
<?php
namespace Aaa;

use Aaa\Bbb;
use Ccc;

class Ddd
{
}

EOF
                ,
            ],
            'close_tag_1' => [
                '<?php
?>inline content<?php ?>',
                '<?php
     use A\AA;
     use B\C?>inline content<?php use A\D; use E\F ?>',
            ],
            'close_tag_2' => [
                '<?php ?>',
                '<?php use A\B;?>',
            ],
            'close_tag_3' => [
                '<?php ?>',
                '<?php use A\B?>',
            ],
            'with_comments' => [
                '<?php
# 1
# 2
# 3
# 4
  use /**/A\B/**/;
  echo 1;
  new B();
',
                '<?php
use# 1
\# 2
Exception# 3
# 4





  ;
use /**/A\B/**/;
  echo 1;
  new B();
',
            ],
            'with_matches_in_comments' => [
                '<?php
use Foo;
use Bar;
use Baz;

//Foo
#Bar
/*Baz*/',
            ],
            'with_case_insensitive_matches_in_comments' => [
                '<?php
use Foo;
use Bar;
use Baz;

//foo
#bar
/*baz*/',
            ],
            'with_same_namespace_import_and_unused_import' => [
                <<<'EOF'
<?php

namespace Foo;

use Bar\C;
/* test */

abstract class D extends A implements C
{
}

EOF
                ,
                <<<'EOF'
<?php

namespace Foo;

use Bar\C;
use Foo\A;
use Foo\Bar\B /* test */ ;

abstract class D extends A implements C
{
}

EOF
                ,
            ],
            'with_same_namespace_import_and_unused_import_after_namespace_statement' => [
                <<<'EOF'
<?php

namespace Foo;

use Foo\Bar\C;

abstract class D extends A implements C
{
}

EOF
                ,
                <<<'EOF'
<?php

namespace Foo;

use Foo\A;
use Foo\Bar\B;
use Foo\Bar\C;

abstract class D extends A implements C
{
}

EOF
                ,
            ],
            'wrong_casing' => [
                <<<'EOF'
<?php

use Foo\Foo;
use Bar\Bar;

$a = new FOO();
$b = new bar();
EOF
                ,
            ],
            'phpdoc_unused' => [
                <<<'EOF'
<?php

class Foo extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testBar()
    { }
}
EOF
                ,
                <<<'EOF'
<?php
use Some\Exception;

class Foo extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testBar()
    { }
}
EOF
                ,
            ],
            'imported_class_is_used_for_constants_1' => [
                '<?php
use A\ABC;
$a = 5-ABC::Test;
$a = 5-ABC::Test-5;
$a = ABC::Test-5;
',
            ],
            'imported_class_is_used_for_constants_2' => [
                '<?php
use A\ABC;
$a = 5-ABC::Test;
$a = 5-ABC::Test-5;
',
            ],
            'imported_class_is_used_for_constants_3' => [
                '<?php
use A\ABC;
$a = 5-ABC::Test;
',
            ],
            'imported_class_is_used_for_constants_4' => ['<?php
use A\ABC;
$a = ABC::Test-5;
',
            ],
            'imported_class_is_used_for_constants_5' => ['<?php
use A\ABC;
$a = 5-ABC::Test-5;
',
            ],
            'imported_class_is_used_for_constants_6' => ['<?php
use A\ABC;
$b = $a-->ABC::Test;
',
            ],
            'imported_class_name_is_prefix_with_dash_of_constant' => [
                <<<'EOF'
<?php


class Dummy
{
const C = 'bar-bados';
}
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar;

class Dummy
{
const C = 'bar-bados';
}
EOF
                ,
            ],
            'imported_class_name_is_suffix_with_dash_of_constant' => [
                <<<'EOF'
<?php


class Dummy
{
    const C = 'tool-bar';
}
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar;

class Dummy
{
    const C = 'tool-bar';
}
EOF
                ,
            ],
            'imported_class_name_is_inside_with_dash_of_constant' => [
                <<<'EOF'
<?php


class Dummy
{
    const C = 'tool-bar-bados';
}
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar;

class Dummy
{
    const C = 'tool-bar-bados';
}
EOF
                ,
            ],
            'functions_in_the_global_namespace_should_not_be_removed_even_when_declaration_has_new_lines_and_is_uppercase' => [
                <<<'EOF'
<?php

namespace Foo;

use function is_int;

is_int(1);

EOF
                ,
                <<<'EOF'
<?php

namespace Foo;

use function is_int;
use function is_float;

is_int(1);

EOF
                ,
            ],
            'constants_in_the_global_namespace_should_not_be_removed' => [
                $expected = <<<'EOF'
<?php

namespace Foo;

use const PHP_INT_MAX;

echo PHP_INT_MAX;

EOF
                ,
                <<<'EOF'
<?php

namespace Foo;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

echo PHP_INT_MAX;

EOF
                ,
            ],
            'functions_in_the_global_namespace_should_not_be_removed_even_when_declaration_has_ne_lines_and_is_uppercase' => [
                <<<'EOF'
<?php

namespace Foo;use/**/FUNCTION#1
is_int;#2

is_int(1);

EOF
                ,
                <<<'EOF'
<?php

namespace Foo;use/**/FUNCTION#1
is_int;#2
use function
    is_float;
use
    const
        PHP_INT_MIN;

is_int(1);

EOF
                ,
            ],
        ];
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
        return [
            [ // TODO test shows lot of cases where imports are not removed while could be
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
            ],
        ];
    }
}
