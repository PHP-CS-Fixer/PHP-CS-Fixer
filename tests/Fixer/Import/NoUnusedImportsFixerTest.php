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
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Import\NoUnusedImportsFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
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

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple' => [
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
                EOF,
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
                EOF,
        ];

        yield 'with_indents' => [
            <<<'EOF'
                <?php

                use Foo\Bar;
                    $foo = 1;
                use Foo\Bar\FooBar as FooBaz;
                    use SomeClassIndented;

                $a = new Bar();
                $a = new FooBaz();
                $a = new SomeClassIndented();

                EOF,
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

                EOF,
        ];

        yield 'in_same_namespace_1' => [
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
                EOF,
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
                EOF,
        ];

        yield 'in_same_namespace_2' => [
            <<<'EOF'
                <?php namespace App\Http\Controllers;

                EOF,
            <<<'EOF'
                <?php namespace App\Http\Controllers;

                use Illuminate\Http\Request;
                use App\Http\Controllers\Controller;

                EOF,
        ];

        yield 'in_same_namespace_multiple_1' => [
            <<<'EOF'
                <?php

                namespace Foooooooo;
                namespace Foo;

                use Foooooooo\Baaaaz;

                $a = new Bar();
                $b = new Baz();
                $c = new Baaaaz();
                EOF,
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
                EOF,
        ];

        yield 'in_same_namespace_multiple_2' => [
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
                EOF,
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
                EOF,
        ];

        yield 'in_same_namespace_multiple_braces' => [
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
                EOF,
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
                EOF,
        ];

        yield 'multiple_use' => [
            <<<'EOF'
                <?php

                namespace Foo;

                use BarE;

                $c = new D();
                $e = new BarE();
                EOF,
            <<<'EOF'
                <?php

                namespace Foo;

                use Bar;
                use BarA;
                use BarB, BarC as C, BarD;
                use BarB2;
                use BarB\B2;
                use BarE;
                use function fun_a, fun_b, fun_c;
                use const CONST_A, CONST_B, CONST_C;

                $c = new D();
                $e = new BarE();
                EOF,
        ];

        yield 'with_braces' => [
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
                EOF,
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
                EOF,
        ];

        yield 'trailing_spaces' => [
            <<<'EOF'
                <?php

                use Foo\Bar ;
                use Foo\Bar\FooBar as FooBaz ;

                $a = new Bar();
                $a = new FooBaz();
                EOF,
            <<<'EOF'
                <?php

                use Foo\Bar ;
                use Foo\Bar\FooBar as FooBaz ;
                use Foo\Bar\Foo as Fooo ;
                use SomeClass ;

                $a = new Bar();
                $a = new FooBaz();
                EOF,
        ];

        yield 'traits' => [
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
                EOF,
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
                EOF,
        ];

        yield 'function_use' => [
            <<<'EOF'
                <?php

                use Foo;

                $f = new Foo();
                $a = function ($item) use ($f) {
                    return !in_array($item, $f);
                };
                EOF,
            <<<'EOF'
                <?php

                use Foo;
                use Bar;

                $f = new Foo();
                $a = function ($item) use ($f) {
                    return !in_array($item, $f);
                };
                EOF,
        ];

        yield 'similar_names' => [
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
                EOF,
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
                EOF,
        ];

        yield 'variable_name' => [
            <<<'EOF'
                <?php


                $bar = null;
                EOF,
            <<<'EOF'
                <?php

                use Foo\Bar;

                $bar = null;
                EOF,
        ];

        yield 'property name, method name, static method call, static property' => [
            <<<'EOF'
                <?php


                $foo->bar = null;
                $foo->bar();
                $foo::bar();
                $foo::bar;
                EOF,
            <<<'EOF'
                <?php

                use Foo\Bar;

                $foo->bar = null;
                $foo->bar();
                $foo::bar();
                $foo::bar;
                EOF,
        ];

        yield 'constant_name' => [
            <<<'EOF'
                <?php


                class Baz
                {
                    const BAR = 0;
                }
                EOF,
            <<<'EOF'
                <?php

                use Foo\Bar;

                class Baz
                {
                    const BAR = 0;
                }
                EOF,
        ];

        yield 'namespace_part' => [
            <<<'EOF'
                <?php


                new \Baz\Bar();
                EOF,
            <<<'EOF'
                <?php

                use Foo\Bar;

                new \Baz\Bar();
                EOF,
        ];

        yield 'use_in_string_1' => [
            <<<'EOF'
                <?php
                $x=<<<'EOA'
                use a;
                use b;
                EOA;

                EOF,
        ];

        yield 'use_in_string_2' => [
            <<<'EOF'
                <?php
                $x='
                use a;
                use b;
                ';
                EOF,
        ];

        yield 'use_in_string_3' => [
            <<<'EOF'
                <?php
                $x="
                use a;
                use b;
                ";
                EOF,
        ];

        yield 'import_in_global_namespace' => [
            <<<'EOF'
                <?php
                namespace A;
                use \SplFileInfo;
                new SplFileInfo(__FILE__);
                EOF,
        ];

        yield 'no_import_in_global_namespace' => [
            <<<'EOF'
                <?php
                namespace A;
                new \SplFileInfo(__FILE__);
                EOF,
            <<<'EOF'
                <?php
                namespace A;
                use SplFileInfo;
                new \SplFileInfo(__FILE__);
                EOF,
        ];

        yield 'no_import_attribute_in_global_namespace' => [
            <<<'EOF'
                <?php
                namespace A;
                #[\Attribute(\Attribute::TARGET_PROPERTY)]
                final class B {}
                EOF,
            <<<'EOF'
                <?php
                namespace A;
                use Attribute;
                #[\Attribute(\Attribute::TARGET_PROPERTY)]
                final class B {}
                EOF,
        ];

        yield 'use_as_last_statement' => [
            <<<'EOF'
                <?php

                EOF,
            <<<'EOF'
                <?php
                use Bar\Finder;
                EOF,
        ];

        yield 'use_with_same_last_part_that_is_in_namespace' => [
            <<<'EOF'
                <?php

                namespace Foo\Finder;


                EOF,
            <<<'EOF'
                <?php

                namespace Foo\Finder;

                use Bar\Finder;
                EOF,
        ];

        yield 'used_use_with_same_last_part_that_is_in_namespace' => [
            <<<'EOF'
                <?php

                namespace Foo\Finder;

                use Bar\Finder;

                class Baz extends Finder
                {
                }
                EOF,
        ];

        yield 'foo' => [
            <<<'EOF'
                <?php
                namespace Aaa;

                class Ddd
                {
                }

                EOF,
            <<<'EOF'
                <?php
                namespace Aaa;

                use Aaa\Bbb;
                use Ccc;

                class Ddd
                {
                }

                EOF,
        ];

        yield 'close_tag_1' => [
            '<?php
?>inline content<?php ?>',
            '<?php
     use A\AA;
     use B\C?>inline content<?php use A\D; use E\F ?>',
        ];

        yield 'close_tag_2' => [
            '<?php ?>',
            '<?php use A\B;?>',
        ];

        yield 'close_tag_3' => [
            '<?php ?>',
            '<?php use A\B?>',
        ];

        yield 'case_mismatch_typo' => [
            '<?php
use Foo\exception; // must be kept by non-risky fixer

try {
    x();
} catch (Exception $e) {
    echo \'Foo\Exception caught\';
} catch (\Exception $e) {
    echo \'Exception caught\';
}
',
        ];

        yield 'with_matches_in_comments' => [
            '<?php
use Foo;
use Bar;
use Baz;

//Foo
#Bar
/*Baz*/',
        ];

        yield 'with_case_insensitive_matches_in_comments' => [
            '<?php
use Foo;
use Bar;
use Baz;

//foo
#bar
/*baz*/',
        ];

        yield 'with_same_namespace_import_and_unused_import' => [
            <<<'EOF'
                <?php

                namespace Foo;

                use Bar\C;
                /* test */

                abstract class D extends A implements C
                {
                }

                EOF,
            <<<'EOF'
                <?php

                namespace Foo;

                use Bar\C;
                use Foo\A;
                use Foo\Bar\B /* test */ ;

                abstract class D extends A implements C
                {
                }

                EOF,
        ];

        yield 'with_same_namespace_import_and_unused_import_after_namespace_statement' => [
            <<<'EOF'
                <?php

                namespace Foo;

                use Foo\Bar\C;

                abstract class D extends A implements C
                {
                }

                EOF,
            <<<'EOF'
                <?php

                namespace Foo;

                use Foo\A;
                use Foo\Bar\B;
                use Foo\Bar\C;

                abstract class D extends A implements C
                {
                }

                EOF,
        ];

        yield 'wrong_casing' => [
            <<<'EOF'
                <?php

                use Foo\Foo;
                use Bar\Bar;

                $a = new FOO();
                $b = new bar();
                EOF,
        ];

        yield 'phpdoc_unused' => [
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
                EOF,
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
                EOF,
        ];

        yield 'imported_class_is_used_for_constants_1' => [
            '<?php
use A\ABC;
$a = 5-ABC::Test;
$a = 5-ABC::Test-5;
$a = ABC::Test-5;
',
        ];

        yield 'imported_class_is_used_for_constants_2' => [
            '<?php
use A\ABC;
$a = 5-ABC::Test;
$a = 5-ABC::Test-5;
',
        ];

        yield 'imported_class_is_used_for_constants_3' => [
            '<?php
use A\ABC;
$a = 5-ABC::Test;
',
        ];

        yield 'imported_class_is_used_for_constants_4' => ['<?php
use A\ABC;
$a = ABC::Test-5;
',
        ];

        yield 'imported_class_is_used_for_constants_5' => ['<?php
use A\ABC;
$a = 5-ABC::Test-5;
',
        ];

        yield 'imported_class_is_used_for_constants_6' => ['<?php
use A\ABC;
$b = $a-->ABC::Test;
',
        ];

        yield 'imported_class_name_is_prefix_with_dash_of_constant' => [
            <<<'EOF'
                <?php


                class Dummy
                {
                const C = 'bar-bados';
                }
                EOF,
            <<<'EOF'
                <?php

                use Foo\Bar;

                class Dummy
                {
                const C = 'bar-bados';
                }
                EOF,
        ];

        yield 'imported_class_name_is_suffix_with_dash_of_constant' => [
            <<<'EOF'
                <?php


                class Dummy
                {
                    const C = 'tool-bar';
                }
                EOF,
            <<<'EOF'
                <?php

                use Foo\Bar;

                class Dummy
                {
                    const C = 'tool-bar';
                }
                EOF,
        ];

        yield 'imported_class_name_is_inside_with_dash_of_constant' => [
            <<<'EOF'
                <?php


                class Dummy
                {
                    const C = 'tool-bar-bados';
                }
                EOF,
            <<<'EOF'
                <?php

                use Foo\Bar;

                class Dummy
                {
                    const C = 'tool-bar-bados';
                }
                EOF,
        ];

        yield 'functions_in_the_global_namespace_should_not_be_removed_even_when_declaration_has_new_lines_and_is_uppercase' => [
            <<<'EOF'
                <?php

                namespace Foo;

                use function is_int;

                is_int(1);

                EOF,
            <<<'EOF'
                <?php

                namespace Foo;

                use function is_int;
                use function is_float;

                is_int(1);

                EOF,
        ];

        yield 'constants_in_the_global_namespace_should_not_be_removed' => [
            <<<'EOF'
                <?php

                namespace Foo;

                use const PHP_INT_MAX;

                echo PHP_INT_MAX;

                EOF,
            <<<'EOF'
                <?php

                namespace Foo;

                use const PHP_INT_MAX;
                use const PHP_INT_MIN;

                echo PHP_INT_MAX;

                EOF,
        ];

        yield 'functions_in_the_global_namespace_should_not_be_removed_even_when_declaration_has_ne_lines_and_is_uppercase' => [
            <<<'EOF'
                <?php

                namespace Foo;use/**/FUNCTION#1
                is_int;#2

                is_int(1);

                EOF,
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

                EOF,
        ];

        yield 'use_trait should never be removed' => [
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

                EOF,
        ];

        yield 'imported_name_is_part_of_namespace' => [
            <<<'EOF'
                <?php

                namespace App\Foo;


                class Baz
                {
                }

                EOF,
            <<<'EOF'
                <?php

                namespace App\Foo;

                use Foo\Bar\App;

                class Baz
                {
                }

                EOF,
        ];

        yield 'imported_name_is_part_of_namespace with closing tag' => [
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
                EOF,
        ];

        yield [
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
        ];

        yield 'unused import matching function call' => [
            '<?php
namespace Foo;
bar();',
            '<?php
namespace Foo;
use Bar;
bar();',
        ];

        yield 'unused import matching function declaration' => [
            '<?php
namespace Foo;
function bar () {}',
            '<?php
namespace Foo;
use Bar;
function bar () {}',
        ];

        yield 'unused import matching method declaration' => [
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
        ];

        yield 'unused import matching constant usage' => [
            '<?php
namespace Foo;
echo BAR;',
            '<?php
namespace Foo;
use Bar;
echo BAR;',
        ];

        yield 'unused import matching class constant' => [
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
        ];

        yield 'unused function import matching class usage' => [
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
        ];

        yield 'unused function import matching method call' => [
            '<?php
namespace Foo;
Foo::bar();',
            '<?php
namespace Foo;
use function bar;
Foo::bar();',
        ];

        yield 'unused function import matching method declaration' => [
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
        ];

        yield 'unused function import matching constant usage' => [
            '<?php
namespace Foo;
echo BAR;',
            '<?php
namespace Foo;
use function bar;
echo BAR;',
        ];

        yield 'unused function import matching class constant' => [
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
        ];

        yield 'unused constant import matching function call' => [
            '<?php
namespace Foo;
bar();',
            '<?php
namespace Foo;
use const BAR;
bar();',
        ];

        yield 'unused constant import matching function declaration' => [
            '<?php
namespace Foo;
function bar () {}',
            '<?php
namespace Foo;
use const BAR;
function bar () {}',
        ];

        yield 'unused constant import matching method declaration' => [
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
        ];

        yield 'unused constant import matching class constant' => [
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
        ];

        yield 'attribute without braces' => [
            '<?php
use Foo;
class Controller
{
    #[Foo]
    public function foo() {}
}',
        ];

        yield 'attribute with braces' => [
            '<?php
use Foo;
class Controller
{
    #[Foo()]
    public function foo() {}
}',
        ];

        yield 'go to' => [
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
        ];

        yield [
            <<<'EOF'
                <?php
                use some\a\{ClassD};
                use function some\c\{fn_a};
                use const some\d\{ConstB};

                new CLassD();
                echo fn_a(ConstB);
                EOF,
            <<<'EOF'
                <?php
                use some\a\{ClassD};
                use some\b\{ClassA, ClassB, ClassC as C};
                use function some\c\{fn_a, fn_b, fn_c};
                use const some\d\{ConstA, ConstB, ConstC};

                new CLassD();
                echo fn_a(ConstB);
                EOF,
        ];

        yield 'grouped imports' => [
            <<<'EOF'
                <?php
                use some\y\{ClassA, ClassC as C,};
                use function some\a\{
                    fn_b,
                };
                use const some\Z\{ConstA,ConstC,};

                echo ConstA.ConstC;
                fn_b(ClassA::test, new C());
                EOF,
            <<<'EOF'
                <?php
                use A\{B,};
                use some\y\{ClassA, ClassB, ClassC as C,};
                use function some\a\{
                    fn_a,
                    fn_b,
                    fn_c,
                };
                use function some\b\{fn_x, fn_y, fn_z,};
                use const some\Z\{ConstA,ConstB,ConstC,};
                use const some\X\{ConstX,ConstY,ConstZ};
                use C\{D,E,};
                use Z;

                echo ConstA.ConstC;
                fn_b(ClassA::test, new C());
                EOF,
        ];

        yield 'multiline grouped imports with comments' => [
            <<<'EOF'
                <?php
                use function some\a\{
                     // Foo
                    fn_b,
                    /* Bar *//** Baz */
                     # Buzz
                };

                fn_b();
                EOF,
            <<<'EOF'
                <?php
                use function some\a\{
                    fn_a, // Foo
                    fn_b,
                    /* Bar */ fn_c, /** Baz */
                    fn_d, # Buzz
                };

                fn_b();
                EOF,
        ];

        yield 'comma-separated imports' => [
            <<<'EOF'
                <?php
                use A;
                use function fn_b;
                use const ConstC;

                fn_b(new A(), ConstC);
                EOF,
            <<<'EOF'
                <?php
                use A, B, C;
                use function fn_a, fn_b, fn_c;
                use const ConstA, ConstB, ConstC;

                fn_b(new A(), ConstC);
                EOF,
        ];

        yield 'only unused comma-separated imports in single line' => [
            '<?php ',
            '<?php use A, B, C;',
        ];

        yield 'only unused grouped imports in single line' => [
            '<?php ',
            '<?php use A\{B, C};',
        ];

        yield 'unused comma-separated imports right after open tag, with consecutive lines' => [
            "<?php \n# Comment",
            "<?php use A, B, C;\n\n# Comment",
        ];

        yield 'unused grouped imports right after open tag, with consecutive lines' => [
            "<?php \n# Comment",
            "<?php use A\\{B, C};\n\n# Comment",
        ];

        yield 'unused comma-separated imports right after open tag with a non-empty token after it, and with consecutive lines' => [
            "<?php # Comment\n\n# Another comment",
            "<?php use A, B, C; # Comment\n\n# Another comment",
        ];

        yield 'unused grouped imports right after open tag with a non-empty token after it, and with consecutive lines' => [
            "<?php # Comment\n\n# Another comment",
            "<?php use A\\{B, C}; # Comment\n\n# Another comment",
        ];

        yield 'only unused comma-separated imports in the last line, with whitespace after' => [
            "<?php \n",
            "<?php \nuse A, B, C;     \t\t",
        ];

        yield 'only unused grouped imports in the last line, with whitespace after' => [
            "<?php \n",
            "<?php \nuse A\\{B, C};     \t\t",
        ];

        yield 'imported class name with underscore before or after it in PHPDoc' => [
            <<<'PHP'
                <?php
                /** @var _UnusedImport1 */
                $foo = 1;
                /** @var UnusedImport2_ */
                $bar = 2;
                /** @var _UnusedImport3_ */
                $baz = 3;
                PHP,
            <<<'PHP'
                <?php
                use Vendor\UnusedImport1;
                use Vendor\UnusedImport2;
                use Vendor\UnusedImport3;
                /** @var _UnusedImport1 */
                $foo = 1;
                /** @var UnusedImport2_ */
                $bar = 2;
                /** @var _UnusedImport3_ */
                $baz = 3;
                PHP,
        ];
    }

    /**
     * @requires PHP <8.0
     */
    public function testFixPre80(): void
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
',
        );
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
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
     * @dataProvider provideFix81Cases
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFix81Cases(): iterable
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

    /**
     * @requires PHP 8.3
     *
     * @dataProvider provideFix83Cases
     */
    public function testFix83(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFix83Cases(): iterable
    {
        yield 'typed class constants' => [
            <<<'PHP'
                <?php
                use Vendor\Type04;
                use Vendor\Type08;
                use Vendor\Type10;
                use Vendor\Type14;
                use Vendor\Type16;
                use Vendor\Type20;
                use Vendor\Type22;
                use Vendor\Type24;
                use Vendor\Type26;
                use Vendor\Type28;
                use Vendor\Type30;
                use Vendor\Type32;
                use Vendor\Type34;
                use Vendor\Type36;
                class C
                {
                    public const bool BOOLEAN_TYPE = true;
                    public const Type02 REGULAR_TYPE = TheParentType::Foo;
                    public const ?Type04 NULLABLE_TYPE = TheParentType::Foo;
                    public const Type06|Type08|Type10 UNION_TYPE = TheParentType::Foo;
                    public const int INTEGER_TYPE = 42;
                    public const Type12&Type14&Type16 INTERSECTION_TYPE = TheParentType::Foo;
                    public const Type18|(Type20&Type22) UNION_AND_INTERSECTION_TYPE = TheParentType::Foo;
                    public const (Type24&Type26)|Type28 INTERSECTION_AND_UNION_TYPE = TheParentType::Foo;
                    public const string STRING_TYPE = 'Forty two';
                    public const (Type30&Type32)|(Type34&Type36) INTERSECTION_AND_UNION_AND_INTERSECTION_TYPE = TheParentType::Foo;
                }
                PHP,
            <<<'PHP'
                <?php
                use Vendor\Type01;
                use Vendor\Type02;
                use Vendor\Type03;
                use Vendor\Type04;
                use Vendor\Type05;
                use Vendor\Type06;
                use Vendor\Type07;
                use Vendor\Type08;
                use Vendor\Type09;
                use Vendor\Type10;
                use Vendor\Type11;
                use Vendor\Type12;
                use Vendor\Type13;
                use Vendor\Type14;
                use Vendor\Type15;
                use Vendor\Type16;
                use Vendor\Type17;
                use Vendor\Type18;
                use Vendor\Type19;
                use Vendor\Type20;
                use Vendor\Type21;
                use Vendor\Type22;
                use Vendor\Type23;
                use Vendor\Type24;
                use Vendor\Type25;
                use Vendor\Type26;
                use Vendor\Type27;
                use Vendor\Type28;
                use Vendor\Type29;
                use Vendor\Type30;
                use Vendor\Type31;
                use Vendor\Type32;
                use Vendor\Type33;
                use Vendor\Type34;
                use Vendor\Type35;
                use Vendor\Type36;
                use Vendor\Type37;
                class C
                {
                    public const bool BOOLEAN_TYPE = true;
                    public const Type02 REGULAR_TYPE = TheParentType::Foo;
                    public const ?Type04 NULLABLE_TYPE = TheParentType::Foo;
                    public const Type06|Type08|Type10 UNION_TYPE = TheParentType::Foo;
                    public const int INTEGER_TYPE = 42;
                    public const Type12&Type14&Type16 INTERSECTION_TYPE = TheParentType::Foo;
                    public const Type18|(Type20&Type22) UNION_AND_INTERSECTION_TYPE = TheParentType::Foo;
                    public const (Type24&Type26)|Type28 INTERSECTION_AND_UNION_TYPE = TheParentType::Foo;
                    public const string STRING_TYPE = 'Forty two';
                    public const (Type30&Type32)|(Type34&Type36) INTERSECTION_AND_UNION_AND_INTERSECTION_TYPE = TheParentType::Foo;
                }
                PHP,
        ];
    }
}
