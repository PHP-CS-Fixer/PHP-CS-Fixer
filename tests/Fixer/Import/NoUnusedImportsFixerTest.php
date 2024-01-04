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

    public static function provideFixCases(): iterable
    {
        yield 'simple' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'with_indents' => [
            <<<'EOD'
                <?php

                use Foo\Bar;
                    $foo = 1;
                use Foo\Bar\FooBar as FooBaz;
                    use SomeClassIndented;

                $a = new Bar();
                $a = new FooBaz();
                $a = new SomeClassIndented();

                EOD,
            <<<'EOD'
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

                EOD,
        ];

        yield 'in_same_namespace_1' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'in_same_namespace_2' => [
            <<<'EOD'
                <?php namespace App\Http\Controllers;


                EOD,
            <<<'EOD'
                <?php namespace App\Http\Controllers;

                use Illuminate\Http\Request;
                use App\Http\Controllers\Controller;

                EOD,
        ];

        yield 'in_same_namespace_multiple_1' => [
            <<<'EOD'
                <?php

                namespace Foooooooo;
                namespace Foo;

                use Foooooooo\Baaaaz;

                $a = new Bar();
                $b = new Baz();
                $c = new Baaaaz();
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'in_same_namespace_multiple_2' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'in_same_namespace_multiple_braces' => [
            <<<'EOD'
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
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'multiple_use' => [
            <<<'EOD'
                <?php

                namespace Foo;

                use BarB, BarC as C, BarD;
                use BarE;

                $c = new D();
                $e = new BarE();
                EOD,

            <<<'EOD'
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
                EOD,
        ];

        yield 'with_braces' => [
            <<<'EOD'
                <?php

                namespace Foo\Bar\FooBar {
                    use Foo\Bar\FooBar\Foo as Fooz;
                    use Foo\Bar\FooBar\Aaa\Bbb;

                    $a = new Baz();
                    $b = new Fooz();
                    $c = new Bar\Fooz();
                    $d = new Bbb();
                }
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'trailing_spaces' => [
            <<<'EOD'
                <?php

                use Foo\Bar ;
                use Foo\Bar\FooBar as FooBaz ;

                $a = new Bar();
                $a = new FooBaz();
                EOD,
            <<<'EOD'
                <?php

                use Foo\Bar ;
                use Foo\Bar\FooBar as FooBaz ;
                use Foo\Bar\Foo as Fooo ;
                use SomeClass ;

                $a = new Bar();
                $a = new FooBaz();
                EOD,
        ];

        yield 'traits' => [
            <<<'EOD'
                <?php

                use Foo as Bar;
                use A\MyTrait1;

                class MyParent
                {
                    use MyTrait1;
                use MyTrait2;
                    use Bar;
                }
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'function_use' => [
            <<<'EOD'
                <?php

                use Foo;

                $f = new Foo();
                $a = function ($item) use ($f) {
                    return !in_array($item, $f);
                };
                EOD,
            <<<'EOD'
                <?php

                use Foo;
                use Bar;

                $f = new Foo();
                $a = function ($item) use ($f) {
                    return !in_array($item, $f);
                };
                EOD,
        ];

        yield 'similar_names' => [
            <<<'EOD'
                <?php

                use SomeEntityRepository;

                class SomeService
                {
                    public function __construct(SomeEntityRepository $repo)
                    {
                        $this->repo = $repo;
                    }
                }
                EOD,
            <<<'EOD'
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
                EOD,
        ];

        yield 'variable_name' => [
            <<<'EOD'
                <?php


                $bar = null;
                EOD,
            <<<'EOD'
                <?php

                use Foo\Bar;

                $bar = null;
                EOD,
        ];

        yield 'property name, method name, static method call, static property' => [
            <<<'EOD'
                <?php


                $foo->bar = null;
                $foo->bar();
                $foo::bar();
                $foo::bar;
                EOD,
            <<<'EOD'
                <?php

                use Foo\Bar;

                $foo->bar = null;
                $foo->bar();
                $foo::bar();
                $foo::bar;
                EOD,
        ];

        yield 'constant_name' => [
            <<<'EOD'
                <?php


                class Baz
                {
                    const BAR = 0;
                }
                EOD,
            <<<'EOD'
                <?php

                use Foo\Bar;

                class Baz
                {
                    const BAR = 0;
                }
                EOD,
        ];

        yield 'namespace_part' => [
            <<<'EOD'
                <?php


                new \Baz\Bar();
                EOD,
            <<<'EOD'
                <?php

                use Foo\Bar;

                new \Baz\Bar();
                EOD,
        ];

        yield 'use_in_string_1' => [
            <<<'EOD'
                <?php
                $x=<<<'EOA'
                use a;
                use b;
                EOA;

                EOD,
        ];

        yield 'use_in_string_2' => [
            <<<'EOD'
                <?php
                $x='
                use a;
                use b;
                ';
                EOD,
        ];

        yield 'use_in_string_3' => [
            <<<'EOD'
                <?php
                $x="
                use a;
                use b;
                ";
                EOD,
        ];

        yield 'import_in_global_namespace' => [
            <<<'EOD'
                <?php
                namespace A;
                use \SplFileInfo;
                new SplFileInfo(__FILE__);
                EOD,
        ];

        yield 'no_import_in_global_namespace' => [
            <<<'EOD'
                <?php
                namespace A;
                new \SplFileInfo(__FILE__);
                EOD,
            <<<'EOD'
                <?php
                namespace A;
                use SplFileInfo;
                new \SplFileInfo(__FILE__);
                EOD,
        ];

        yield 'no_import_attribute_in_global_namespace' => [
            <<<'EOD'
                <?php
                namespace A;
                #[\Attribute(\Attribute::TARGET_PROPERTY)]
                final class B {}
                EOD,
            <<<'EOD'
                <?php
                namespace A;
                use Attribute;
                #[\Attribute(\Attribute::TARGET_PROPERTY)]
                final class B {}
                EOD,
        ];

        yield 'use_as_last_statement' => [
            <<<'EOD'
                <?php

                EOD,

            <<<'EOD'
                <?php
                use Bar\Finder;
                EOD,
        ];

        yield 'use_with_same_last_part_that_is_in_namespace' => [
            <<<'EOD'
                <?php

                namespace Foo\Finder;


                EOD,
            <<<'EOD'
                <?php

                namespace Foo\Finder;

                use Bar\Finder;
                EOD,
        ];

        yield 'used_use_with_same_last_part_that_is_in_namespace' => [
            <<<'EOD'
                <?php

                namespace Foo\Finder;

                use Bar\Finder;

                class Baz extends Finder
                {
                }
                EOD,
        ];

        yield 'foo' => [
            <<<'EOD'
                <?php
                namespace Aaa;


                class Ddd
                {
                }

                EOD,
            <<<'EOD'
                <?php
                namespace Aaa;

                use Aaa\Bbb;
                use Ccc;

                class Ddd
                {
                }

                EOD,
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
            <<<'EOD'
                <?php

                namespace Foo;

                use Bar\C;
                /* test */

                abstract class D extends A implements C
                {
                }

                EOD,
            <<<'EOD'
                <?php

                namespace Foo;

                use Bar\C;
                use Foo\A;
                use Foo\Bar\B /* test */ ;

                abstract class D extends A implements C
                {
                }

                EOD,
        ];

        yield 'with_same_namespace_import_and_unused_import_after_namespace_statement' => [
            <<<'EOD'
                <?php

                namespace Foo;

                use Foo\Bar\C;

                abstract class D extends A implements C
                {
                }

                EOD,
            <<<'EOD'
                <?php

                namespace Foo;

                use Foo\A;
                use Foo\Bar\B;
                use Foo\Bar\C;

                abstract class D extends A implements C
                {
                }

                EOD,
        ];

        yield 'wrong_casing' => [
            <<<'EOD'
                <?php

                use Foo\Foo;
                use Bar\Bar;

                $a = new FOO();
                $b = new bar();
                EOD,
        ];

        yield 'phpdoc_unused' => [
            <<<'EOD'
                <?php

                class Foo extends \PHPUnit_Framework_TestCase
                {
                    /**
                     * @expectedException \Exception
                     */
                    public function testBar()
                    { }
                }
                EOD,
            <<<'EOD'
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
                EOD,
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
            <<<'EOD'
                <?php


                class Dummy
                {
                const C = 'bar-bados';
                }
                EOD,
            <<<'EOD'
                <?php

                use Foo\Bar;

                class Dummy
                {
                const C = 'bar-bados';
                }
                EOD,
        ];

        yield 'imported_class_name_is_suffix_with_dash_of_constant' => [
            <<<'EOD'
                <?php


                class Dummy
                {
                    const C = 'tool-bar';
                }
                EOD,
            <<<'EOD'
                <?php

                use Foo\Bar;

                class Dummy
                {
                    const C = 'tool-bar';
                }
                EOD,
        ];

        yield 'imported_class_name_is_inside_with_dash_of_constant' => [
            <<<'EOD'
                <?php


                class Dummy
                {
                    const C = 'tool-bar-bados';
                }
                EOD,
            <<<'EOD'
                <?php

                use Foo\Bar;

                class Dummy
                {
                    const C = 'tool-bar-bados';
                }
                EOD,
        ];

        yield 'functions_in_the_global_namespace_should_not_be_removed_even_when_declaration_has_new_lines_and_is_uppercase' => [
            <<<'EOD'
                <?php

                namespace Foo;

                use function is_int;

                is_int(1);

                EOD,
            <<<'EOD'
                <?php

                namespace Foo;

                use function is_int;
                use function is_float;

                is_int(1);

                EOD,
        ];

        yield 'constants_in_the_global_namespace_should_not_be_removed' => [
            $expected = <<<'EOD'
                <?php

                namespace Foo;

                use const PHP_INT_MAX;

                echo PHP_INT_MAX;

                EOD,
            <<<'EOD'
                <?php

                namespace Foo;

                use const PHP_INT_MAX;
                use const PHP_INT_MIN;

                echo PHP_INT_MAX;

                EOD,
        ];

        yield 'functions_in_the_global_namespace_should_not_be_removed_even_when_declaration_has_ne_lines_and_is_uppercase' => [
            <<<'EOD'
                <?php

                namespace Foo;use/**/FUNCTION#1
                is_int;#2

                is_int(1);

                EOD,
            <<<'EOD'
                <?php

                namespace Foo;use/**/FUNCTION#1
                is_int;#2
                use function
                    is_float;
                use
                    const
                        PHP_INT_MIN;

                is_int(1);

                EOD,
        ];

        yield 'use_trait should never be removed' => [
            <<<'EOD'
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

                EOD
        ];

        yield 'imported_name_is_part_of_namespace' => [
            <<<'EOD'
                <?php

                namespace App\Foo;


                class Baz
                {
                }

                EOD,
            <<<'EOD'
                <?php

                namespace App\Foo;

                use Foo\Bar\App;

                class Baz
                {
                }

                EOD
        ];

        yield 'imported_name_is_part_of_namespace with closing tag' => [
            <<<'EOD'
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
                EOD
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
            $expected = <<<'EOD'
                <?php
                use some\a\{ClassD};
                use some\b\{ClassA, ClassB, ClassC as C};
                use function some\c\{fn_a, fn_b, fn_c};
                use const some\d\{ConstA, ConstB, ConstC};

                new CLassD();
                echo fn_a();
                EOD
        ];

        yield [ // TODO test shows lot of cases where imports are not removed while could be
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
'
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
}
