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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\NoUnusedImportsFixer
 */
final class NoUnusedImportsFixerTest extends AbstractFixerTestCase
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
use A\MyTrait1;

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
use A\MyTrait1;

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
            'property name, method name, static method call, static property' => [
                <<<'EOF'
<?php


$foo->bar = null;
$foo->bar();
$foo::bar();
$foo::bar;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar;

$foo->bar = null;
$foo->bar();
$foo::bar();
$foo::bar;
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
            'use_trait should never be removed' => [
                <<<'EOF'
<?php

class UsesTraits
{
    /**
     * @see #4086
     */
    private function withComplexStringVariable()
    {
        $name = 'World';

        return "Hello, {$name}!";
    }

    use MyTrait;
}

EOF
            ],
            'imported_name_is_part_of_namespace' => [
                <<<'EOF'
<?php

namespace App\Foo;


class Baz
{
}

EOF
                ,
                <<<'EOF'
<?php

namespace App\Foo;

use Foo\Bar\App;

class Baz
{
}

EOF
            ],
            'imported_name_is_part_of_namespace with closing tag' => [
                <<<'EOF'
<?php
    namespace A\B {?>
<?php
    require_once __DIR__.'/test2.php' ?>
<?php
    use X\Z\Y
?>
<?php
    $y = new Y() ?>
<?php
    var_dump($y);}
EOF
            ],
            [
                '<?php
use App\Http\Requests\StoreRequest;

class StoreController
{
    /**
     * @param \App\Http\Requests\StoreRequest $request
     */
    public function __invoke(StoreRequest $request)
    {}
}',
                '<?php
use App\Http\Requests\StoreRequest;
use Illuminate\Http\Request;

class StoreController
{
    /**
     * @param \App\Http\Requests\StoreRequest $request
     */
    public function __invoke(StoreRequest $request)
    {}
}',
            ],
            'unused import matching function call' => [
                '<?php
namespace Foo;
bar();',
                '<?php
namespace Foo;
use Bar;
bar();',
            ],
            'unused import matching function declaration' => [
                '<?php
namespace Foo;
function bar () {}',
                '<?php
namespace Foo;
use Bar;
function bar () {}',
            ],
            'unused import matching method declaration' => [
                '<?php
namespace Foo;
class Foo {
    public function bar () {}
}',
                '<?php
namespace Foo;
use Bar;
class Foo {
    public function bar () {}
}',
            ],
            'unused import matching constant usage' => [
                '<?php
namespace Foo;
echo BAR;',
                '<?php
namespace Foo;
use Bar;
echo BAR;',
            ],
            'unused import matching class constant' => [
                '<?php
namespace Foo;
class Foo {
    const BAR = 1;
}',
                '<?php
namespace Foo;
use Bar;
class Foo {
    const BAR = 1;
}',
            ],
            'unused function import matching class usage' => [
                '<?php
namespace Foo;
new Bar();
Baz::method();',
                '<?php
namespace Foo;
use function bar;
use function baz;
new Bar();
Baz::method();',
            ],
            'unused function import matching method call' => [
                '<?php
namespace Foo;
Foo::bar();',
                '<?php
namespace Foo;
use function bar;
Foo::bar();',
            ],
            'unused function import matching method declaration' => [
                '<?php
namespace Foo;
class Foo {
    public function bar () {}
}',
                '<?php
namespace Foo;
use function bar;
class Foo {
    public function bar () {}
}',
            ],
            'unused function import matching constant usage' => [
                '<?php
namespace Foo;
echo BAR;',
                '<?php
namespace Foo;
use function bar;
echo BAR;',
            ],
            'unused function import matching class constant' => [
                '<?php
namespace Foo;
class Foo {
    const BAR = 1;
}',
                '<?php
namespace Foo;
use function bar;
class Foo {
    const BAR = 1;
}',
            ],
            'unused constant import matching function call' => [
                '<?php
namespace Foo;
bar();',
                '<?php
namespace Foo;
use const BAR;
bar();',
            ],
            'unused constant import matching function declaration' => [
                '<?php
namespace Foo;
function bar () {}',
                '<?php
namespace Foo;
use const BAR;
function bar () {}',
            ],
            'unused constant import matching method declaration' => [
                '<?php
namespace Foo;
class Foo {
    public function bar () {}
}',
                '<?php
namespace Foo;
use const BAR;
class Foo {
    public function bar () {}
}',
            ],
            'unused constant import matching class constant' => [
                '<?php
namespace Foo;
class Foo {
    const BAR = 1;
}',
                '<?php
namespace Foo;
use const BAR;
class Foo {
    const BAR = 1;
}',
            ],
            'attribute without braces' => [
                '<?php
use Foo;
class Controller
{
    #[Foo]
    public function foo() {}
}',
            ],
            'attribute with braces' => [
                '<?php
use Foo;
class Controller
{
    #[Foo()]
    public function foo() {}
}',
            ],
            'go to' => [
                '<?php
Bar1:
Bar2:
Bar3:
',
                '<?php
use Bar1;
use const Bar2;
use function Bar3;
Bar1:
Bar2:
Bar3:
',
            ],
            [
                $expected = <<<'EOF'
<?php
use some\a\{ClassD};
use some\b\{ClassA, ClassB, ClassC as C};
use function some\c\{fn_a, fn_b, fn_c};
use const some\d\{ConstA, ConstB, ConstC};

new CLassD();
echo fn_a();
EOF
            ],
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

    /**
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(): void
    {
        $this->doTest(
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
'
        );
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider providePhp80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function providePhp80Cases(): iterable
    {
        yield [
            '<?php


$x = $foo?->bar;
$y = foo?->bar();
',
            '<?php

use Foo\Bar;

$x = $foo?->bar;
$y = foo?->bar();
',
        ];

        yield 'with union type in non-capturing catch' => [
            '<?php
use Foo;
use Bar;
try {} catch (Foo | Bar) {}',
        ];

        yield 'union return' => [
            '<?php

use Foo;
use Bar;

abstract class Baz
{
    abstract public function test(): Foo|Bar;
}
',
        ];

        yield 'attribute' => [
            "<?php
use Acme\\JsonSchemaValidationBundle\\Annotation\\JsonSchema;
use Sensio\\Bundle\\FrameworkExtraBundle\\Configuration\\IsGranted;
use Symfony\\Component\\Routing\\Annotation\\Route;

#[
  Route('/basket/{uuid}/item', name: 'addBasketItem', requirements: ['uuid' => '%regex.uuid%'], methods: ['POST']),
  IsGranted('ROLE_USER'),
  JsonSchema('Public/Basket/addItem.json'),
]
class Foo {}
",
        ];

        yield 'attribute 2' => [
            '<?php

use Psr\Log\LoggerInterface;
function f( #[Target(\'xxx\')] LoggerInterface|null $logger) {}
',
        ];
    }

    /**
     * @requires PHP 8.1
     *
     * @dataProvider providePhp81Cases
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function providePhp81Cases(): iterable
    {
        yield 'final const' => [
            '<?php

class Foo
{
    final public const B1 = "2";
}
',
            '<?php
use A\B1;

class Foo
{
    final public const B1 = "2";
}
',
        ];

        yield 'first callable class' => [
            '<?php
use Foo;
Foo::method(...);',
            '<?php
use Foo;
use Bar;
Foo::method(...);',
        ];

        yield 'New in initializers' => [
            '<?php
namespace A\B\C;

use Foo1;
use Foo2;
use Foo3;
use Foo4;
use Foo5;
use Foo6;
use Foo7;

class Test {
    public function __construct(
        public $prop = (new Foo1),
    ) {}
}

function test(
    $foo = (new Foo2),
    $baz = (new Foo3(x: 2)),
) {
}

static $x = new Foo4();

const C = (new Foo5);

function test2($param = (new Foo6)) {}

const D = new Foo7(1,2);
',
        ];
    }

    /**
     * @requires PHP 8.1
     *
     * @dataProvider provideFixPhp81Cases
     */
    public function testFixPhp81(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideFixPhp81Cases(): iterable
    {
        yield [
            '<?php
                enum Foo: string
                {
                    use Bar;

                    case Test1 = "a";
                }
            ',
        ];

        yield [
            '<?php
                use Foo\Class1;
                use Foo\Class2;
                class C
                {
                   public function t(Class1 | Class2 $fields) {}
                }
            ',
        ];

        yield [
            '<?php
                use Foo\Class1;
                use Foo\Class2;
                class C
                {
                   public function t(Class1 | Class2 ...$fields) {}
                }
            ',
        ];
    }
}
