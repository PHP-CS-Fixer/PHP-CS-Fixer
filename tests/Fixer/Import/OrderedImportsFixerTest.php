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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\OrderedImportsFixer
 */
final class OrderedImportsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixWithMultipleNamespaceCases
     */
    public function testFixWithMultipleNamespace(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixWithMultipleNamespaceCases(): iterable
    {
        $expected = <<<'EOF'
            <?php

            namespace FooRoo {

                use Foo\Bar;
                use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar as FooBaz;
                 use Foo\Bir as FBB;
                use Foo\Zar\Baz;
                use SomeClass;
                   use Symfony\Annotation\Template, Zoo\Bar as ZooBar;
                use Zoo\Tar1;

                $a = new Bar();
                $a = new FooBaz();
                $a = new someclass();

                use Zoo\Tar2;

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

                        return function () use ($bar, $foo) {};
                    }
                }
            }

            namespace BlaRoo {

                use Foo\Zar\Baz;
              use SomeClass;
                use Symfony\Annotation\Template;
              use Symfony\Doctrine\Entities\Entity, Zoo\Bar;

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

                        return function () use ($bar, $foo) {};
                    }
                }
            }
            EOF;

        $input = <<<'EOF'
            <?php

            namespace FooRoo {

                use Foo\Bar\FooBar as FooBaz;
                use Zoo\Bar as ZooBar, Zoo\Tar1;
                 use Foo\Bar;
                use Foo\Zar\Baz;
                use Symfony\Annotation\Template;
                   use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
                use SomeClass;

                $a = new Bar();
                $a = new FooBaz();
                $a = new someclass();

                use Zoo\Tar2;

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

                        return function () use ($bar, $foo) {};
                    }
                }
            }

            namespace BlaRoo {

                use Foo\Zar\Baz;
              use Zoo\Bar;
                use SomeClass;
              use Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity;

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

                        return function () use ($bar, $foo) {};
                    }
                }
            }
            EOF;

        yield [$expected, $input];

        $expected = <<<'EOF'
            <?php namespace Space1 {
                use Foo\Bar\Foo;
                use Symfony\Annotation\Template;
            }

            namespace Space2 { use A,B; }

            namespace Space3 {
                use Symfony\Annotation\Template;
                use Symfony\Doctrine\Entities\Entity0, Zoo\Bar;
                echo Bar::C;
                use A\B;
            }

            namespace Space4{}
            EOF;

        $input = <<<'EOF'
            <?php namespace Space1 {
                use Symfony\Annotation\Template;
                use Foo\Bar\Foo;
            }

            namespace Space2 { use B,A; }

            namespace Space3 {
                use Zoo\Bar;
                use Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity0;
                echo Bar::C;
                use A\B;
            }

            namespace Space4{}
            EOF;

        yield [$expected, $input];

        $expected =
            '<?php
                use B;
                use C;
                $foo = new C();
                use A;
            ';

        $input =
            '<?php
                use C;
                use B;
                $foo = new C();
                use A;
            ';

        yield [$expected, $input];

        yield 'open-close groups' => [
            '
                <?php use X ?>
                <?php use Z ?>
                <?php echo X::class ?>
                <?php use E ?>   output
                <?php use F ?><?php echo E::class; use A; ?>
            ',
            '
                <?php use Z ?>
                <?php use X ?>
                <?php echo X::class ?>
                <?php use F ?>   output
                <?php use E ?><?php echo E::class; use A; ?>
            ',
        ];
    }

    public function testFixWithComment(): void
    {
        $expected = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Foo\Bar;
            use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar /* He there */ as FooBaz;
             use Foo\Bir as FBB;
            use Foo\Zar\Baz;
            use SomeClass;
               use /* check */Symfony\Annotation\Template, Zoo\Bar as ZooBar;
            use Zoo\Tar;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

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

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $input = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Foo\Bar\FooBar /* He there */ as FooBaz;
            use Zoo\Bar as ZooBar, Zoo\Tar;
             use Foo\Bar;
            use Foo\Zar\Baz;
            use /* check */Symfony\Annotation\Template;
               use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
            use SomeClass;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

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

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testWithTraits(): void
    {
        $expected = <<<'EOF'
            <?php

            use Foo\Bar;
            use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar as FooBaz;
             use Foo\Bir as FBB;
            use Foo\Zar\Baz;
            use SomeClass;
               use Symfony\Annotation\Template, Zoo\Bar as ZooBar;
            use Zoo\Tar;

            trait Foo {}

            trait Zoo {}

            class AnnotatedClass
            {
                use Foo, Bar;

                /**
                 * @Template(foobar=21)
                 * @param Entity $foo
                 */
                public function doSomething($foo)
                {
                    $bar = $foo->toArray();
                    /** @var ArrayInterface $bar */

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $input = <<<'EOF'
            <?php

            use Foo\Bar\FooBar as FooBaz;
            use Zoo\Bar as ZooBar, Zoo\Tar;
             use Foo\Bar;
            use Foo\Zar\Baz;
            use Symfony\Annotation\Template;
               use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
            use SomeClass;

            trait Foo {}

            trait Zoo {}

            class AnnotatedClass
            {
                use Foo, Bar;

                /**
                 * @Template(foobar=21)
                 * @param Entity $foo
                 */
                public function doSomething($foo)
                {
                    $bar = $foo->toArray();
                    /** @var ArrayInterface $bar */

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithTraitImports(): void
    {
        $expected = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Acme\MyReusableTrait;
            use Foo\Bar, Foo\Bar\Foo as Fooo;
             use Foo\Bar\FooBar as FooBaz;
            use Foo\Bir as FBB;
            use Foo\Zar\Baz;
            use SomeClass;
               use Symfony\Annotation\Template, Zoo\Bar as ZooBar;
            use Zoo\Tar;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

            class AnnotatedClass
            {
                use MyReusableTrait;

                /**
                 * @Template(foobar=21)
                 * @param Entity $foo
                 */
                public function doSomething($foo)
                {
                    $bar = $foo->toArray();
                    /** @var ArrayInterface $bar */

                    return function () use ($bar, $baz) {};
                }
            }
            EOF;

        $input = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Foo\Bar\FooBar as FooBaz;
            use Zoo\Bar as ZooBar, Zoo\Tar;
             use Foo\Bar;
            use Foo\Zar\Baz;
            use Acme\MyReusableTrait;
            use Symfony\Annotation\Template;
               use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
            use SomeClass;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

            class AnnotatedClass
            {
                use MyReusableTrait;

                /**
                 * @Template(foobar=21)
                 * @param Entity $foo
                 */
                public function doSomething($foo)
                {
                    $bar = $foo->toArray();
                    /** @var ArrayInterface $bar */

                    return function () use ($bar, $baz) {};
                }
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithDifferentCases(): void
    {
        $expected = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Baz;
            use abc\Bar;

            <?php

            use abc\Bar;
            use Zoo\Baz;

            class Test
            {
            }
            EOF;

        $input = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Baz;
            use abc\Bar;

            <?php

            use Zoo\Baz;
            use abc\Bar;

            class Test
            {
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testWithoutUses(): void
    {
        $expected = <<<'EOF'
            <?php

            $c = 1;
            EOF;

        $this->doTest($expected);
    }

    public function testOrderWithTrailingDigit(): void
    {
        $expected = <<<'EOF'
            <?php

            use abc\Bar;
            use abc2\Bar2;
            use xyz\abc\Bar6;
            use xyz\abc2\Bar7;
            use xyz\xyz\Bar4;
            use xyz\xyz\Bar5;

            class Test
            {
            }
            EOF;

        $input = <<<'EOF'
            <?php

            use abc2\Bar2;
            use abc\Bar;
            use xyz\abc2\Bar7;
            use xyz\abc\Bar6;
            use xyz\xyz\Bar4;
            use xyz\xyz\Bar5;

            class Test
            {
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testCodeWithImportsOnly(): void
    {
        $expected = <<<'EOF'
            <?php

            use Aaa;
            use Bbb;
            EOF;

        $input = <<<'EOF'
            <?php

            use Bbb;
            use Aaa;
            EOF;

        $this->doTest($expected, $input);
    }

    public function testCodeWithCloseTag(): void
    {
        $this->doTest(
            '<?php
                use A\C1;
                use A\D?><?php use B\C2; use E\F ?>',
            '<?php
                use A\C1;
                use B\C2?><?php use A\D; use E\F ?>'
        );
    }

    public function testCodeWithComments(): void
    {
        $this->doTest(
            '<?php
                use A\C1 /* A */;
                use /* B */ B\C2;',
            '<?php
                use /* B */ B\C2;
                use A\C1 /* A */;'
        );
    }

    /**
     * @requires PHP <8.0
     */
    public function testCodeWithCommentsAndMultiLine(): void
    {
        $this->doTest(
            '<?php
                    use#
A\C1;
                    use B#
\C2#
#
;',
            '<?php
                    use#
B#
\C2#
#
;
                    use A\C1;'
        );
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOF'
                The normal
                use of this fixer
                should not change this sentence nor those statements below
                use Zoo\Bar as ZooBar;
                use Foo\Bar;
                use Foo\Zar\Baz;

                <?php

                use Foo\Bar;
                use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar as FooBaz;
                 use Foo\Bir as FBB;
                use Foo\Zar\Baz;
                use SomeClass;
                   use Symfony\Annotation\Template, Zoo\Bar as ZooBar;
                use Zoo\Tar;

                $a = new Bar();
                $a = new FooBaz();
                $a = new someclass();

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

                        return function () use ($bar, $foo) {};
                    }
                }
                EOF,
            <<<'EOF'
                The normal
                use of this fixer
                should not change this sentence nor those statements below
                use Zoo\Bar as ZooBar;
                use Foo\Bar;
                use Foo\Zar\Baz;

                <?php

                use Foo\Bar\FooBar as FooBaz;
                use Zoo\Bar as ZooBar, Zoo\Tar;
                 use Foo\Bar;
                use Foo\Zar\Baz;
                use Symfony\Annotation\Template;
                   use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
                use SomeClass;

                $a = new Bar();
                $a = new FooBaz();
                $a = new someclass();

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

                        return function () use ($bar, $foo) {};
                    }
                }
                EOF,
        ];

        yield [
            '<?php
use A\B;
use some\a\{ClassA, ClassB, ClassC as C};
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstA, ConstB, ConstC};
use const some\b\{
    ConstX,
    ConstY,
    ConstZ
};
use function some\a\{fn_a, fn_b, fn_c};
use function some\b\{
    fn_x,
    fn_y,
    fn_z
};
',
            '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\b\{
    fn_y,
    fn_z,
    fn_x
};
use function some\a\{fn_a, fn_b, fn_c};
use A\B;
use const some\b\{
    ConstZ,
    ConstX,
    ConstY
};
use const some\a\{ConstA, ConstB, ConstC};
use some\b\{
    ClassG,
    ClassF
};
',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['class', 'const', 'function'],
            ],
        ];

        yield [
            '<?php
use A\B;
use some\a\{ClassA as A /*z*/, ClassB, ClassC};
use const some\a\{
    ConstA,
    ConstB,
    ConstC
};
use function some\a\{fn_a, fn_b, fn_c};
',
            '<?php
use some\a\{  ClassB,ClassC, /*z*/ ClassA as A};
use function some\a\{fn_c,  fn_a,fn_b   };
use A\B;
use const some\a\{
    ConstA,
    ConstB,
    ConstC
};
',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['class', 'const', 'function'],
            ],
        ];

        yield [
            '<?php
use A\B;
use some\a\{ClassA, ClassB, ClassC as C};
use const some\a\{ConstA, ConstB, ConstC};
use function some\a\{fn_a, fn_b, fn_c};
use some\b\{
    ClassF,
    ClassG
};
use const some\b\{
    ConstX,
    ConstY,
    ConstZ
};
use function some\b\{
    fn_x,
    fn_y,
    fn_z
};
',
            '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\b\{
    fn_y,
    fn_z,
    fn_x
};
use function some\a\{fn_a, fn_b, fn_c};
use A\B;
use const some\b\{
    ConstZ,
    ConstX,
    ConstY
};
use const some\a\{ConstA, ConstB, ConstC};
use some\b\{
    ClassG,
    ClassF
};
',
        ];

        yield [
            '<?php
use A\B;
use const some\a\{
    ConstA,
    ConstB,
    ConstC
};
use some\a\{ClassA as A /*z2*/, ClassB, ClassC};
use function some\a\{fn_a, fn_b, fn_c};
',
            '<?php
use some\a\{  ClassB,ClassC, /*z2*/ ClassA as A};
use function some\a\{fn_c,  fn_a,fn_b   };
use A\B;
use const some\a\{
    ConstA,
    ConstB,
    ConstC
};
',
        ];

        yield [
            '<?php
use C\B;
use function B\fn_a;
use const A\ConstA;
            ',
            '<?php
use const A\ConstA;
use function B\fn_a;
use C\B;
            ',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['class', 'function', 'const'],
            ],
        ];

        yield [
            '<?php
use Foo\Bar\Baz;use Foo\Bar\{ClassA, ClassB, ClassC};
use Foo\Bir;
',
            '<?php
use Foo\Bar\Baz, Foo\Bir;
use Foo\Bar\{ClassC, ClassB, ClassA};
',
        ];

        yield [
            '<?php
use A\A;use Foo3\Bar\{ClassA};use G\G;use H\H;use Ioo2\Bar\{ClassB};use J\J;use K\K;use Loo1\Bar\{ClassC};use M\M;
',
            '<?php
use A\A,G\G;use Foo3\Bar\{ClassA};use H\H,J\J;use Ioo2\Bar\{ClassB};use K\K,M\M;use Loo1\Bar\{ClassC};
',
        ];

        yield [
            '<?php
use Foo\Bar\Baz;use Foo\Bar\{ClassA, ClassB, ClassC};
use Foo\Bir;
',
            '<?php
use Foo\Bar\Baz, Foo\Bir;
use Foo\Bar\{ClassC, ClassB, ClassA};
',
        ];

        yield [
            '<?php
use Foo\Bar\{ClassA, ClassB, ClassC};
use Foo\Bir\{
    ClassD,
    ClassE,
    ClassF
};
use Foo\Bor\{
    ClassG,
    ClassH,
    ClassI,
    ClassJ
};
',
            '<?php
use Foo\Bar\{ClassC, ClassB, ClassA};
use Foo\Bir\{ClassE, ClassF,
    ClassD};
use Foo\Bor\{
            ClassJ,
                    ClassI,
    ClassH,
                        ClassG
};
',
        ];

        yield 'alpha - [\'class\', \'function\', \'const\']' => [
            '<?php
use Z\Z;
use function X\X;
use const Y\Y;
            ',
            '<?php
use const Y\Y;
use function X\X;
use Z\Z;
            ',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['class', 'function', 'const'],
            ],
        ];

        yield 'alpha - [\'class\', \'const\', \'function\']' => [
            '<?php
use Z\Z;
use const Y\Y;
use function X\X;
            ',
            '<?php
use function X\X;
use const Y\Y;
use Z\Z;
            ',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['class', 'const', 'function'],
            ],
        ];

        yield 'alpha - [\'function\', \'class\', \'const\']' => [
            '<?php
use function Z\Z;
use Y\Y;
use const X\X;
            ',
            '<?php
use const X\X;
use Y\Y;
use function Z\Z;
            ',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['function', 'class', 'const'],
            ],
        ];

        yield 'alpha - [\'function\', \'const\', \'class\']' => [
            '<?php
use function Z\Z;
use const Y\Y;
use X\X;
            ',
            '<?php
use X\X;
use const Y\Y;
use function Z\Z;
            ',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['function', 'const', 'class'],
            ],
        ];

        yield 'alpha - [\'const\', \'function\', \'class\']' => [
            '<?php
use const Z\Z;
use function Y\Y;
use X\X;
            ',
            '<?php
use X\X;
use function Y\Y;
use const Z\Z;
            ',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['const', 'function', 'class'],
            ],
        ];

        yield 'alpha - [\'const\', \'class\', \'function\']' => [
            '<?php
use const Z\Z;
use Y\Y;
use function X\X;
            ',
            '<?php
use function X\X;
use Y\Y;
use const Z\Z;
            ',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => ['const', 'class', 'function'],
            ],
        ];

        yield '"strcasecmp" vs. "strnatcasecmp"' => [
            '<?php
use A\A1;
use A\A10;
use A\A2;
use A\A20;
            ',
            '<?php
use A\A20;
use A\A2;
use A\A10;
use A\A1;
            ',
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
            ],
        ];

        yield [
            '<?php
use A\{B,};
use C\{D,E,};
',
            '<?php
use C\{D,E,};
use A\{B,};
',
        ];

        yield [
            '<?php
use Foo\{
    Aaa,
    Bbb,
};',
            '<?php
use Foo\{
    Bbb,
    Aaa,
};',
        ];

        yield [
            '<?php
use Foo\{
    Aaa /* 3 *//* 4 *//* 5 */,
    Bbb /* 1 *//* 2 */,
};',
            '<?php
use Foo\{
    /* 1 */Bbb/* 2 */,/* 3 */
    /* 4 */Aaa/* 5 */,/* 6 */
};',
        ];

        $input =
            '<?php use A\{B,};
use some\y\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use C\{D,E,};
';

        yield [
            '<?php use A\{B,};
use C\{D,E,};
use some\y\{ClassA, ClassB, ClassC as C,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use function some\a\{fn_a, fn_b, fn_c,};
',
            $input,
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
                'imports_order' => [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
            ],
        ];

        yield [
            '<?php use A\{B,};
use C\{D,E,};
use some\y\{ClassA, ClassB, ClassC as C,};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use function some\a\{fn_a, fn_b, fn_c,};
',
            $input,
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
                'imports_order' => [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
            ],
        ];

        yield [
            '<?php use A\{B,};
use some\y\{ClassA, ClassB, ClassC as C,};
use C\{D,E,};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use function some\a\{fn_a, fn_b, fn_c,};
',
            $input,
            [
                'sort_algorithm' => OrderedImportsFixer::SORT_NONE,
                'imports_order' => [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
            ],
        ];

        yield [
            '<?php use const CONST_A, CONST_B, CONST_C;',
            '<?php use const CONST_C, CONST_B, CONST_A;',
        ];

        yield [
            '<?php use function Foo\A, Foo\B, Foo\C;',
            '<?php use function Foo\B, Foo\C, Foo\A;',
        ];
    }

    public function testUnknownOrderTypes(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('[ordered_imports] Invalid configuration: Unknown sort types "foo" and "bar".');

        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
            'imports_order' => ['class', 'const', 'function', 'foo', 'bar'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Test sorting by length
    |--------------------------------------------------------------------------
    */

    public function testInvalidOrderTypesSize(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('[ordered_imports] Invalid configuration: Missing sort type "function".');

        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
            'imports_order' => ['class', 'const'],
        ]);
    }

    public function testInvalidOrderType(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('[ordered_imports] Invalid configuration: Missing sort type "class".');

        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
            'imports_order' => ['const', 'function', 'bar'],
        ]);
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideInvalidSortAlgorithmCases
     */
    public function testInvalidSortAlgorithm(array $configuration, string $expectedValue): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage(sprintf(
            '[ordered_imports] Invalid configuration: The option "sort_algorithm" with value %s is invalid. Accepted values are: "alpha", "length", "none".',
            $expectedValue
        ));

        $this->fixer->configure($configuration);
    }

    public static function provideInvalidSortAlgorithmCases(): iterable
    {
        yield [
            [
                'sort_algorithm' => 'dope',
                'imports_order' => null,
            ],
            '"dope"',
        ];

        yield [
            [
                'sort_algorithm' => [OrderedImportsFixer::SORT_ALPHA, OrderedImportsFixer::SORT_LENGTH],
                'imports_order' => null,
            ],
            'array',
        ];

        yield [
            [
                'sort_algorithm' => new \stdClass(),
                'imports_order' => null,
            ],
            \stdClass::class,
        ];
    }

    public function testByLengthFixWithSameLength(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            <?php

            use Acme;
            use Bar1;
            use Barr;
            use Fooo;

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

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $input = <<<'EOF'
            <?php

            use Acme;
            use Fooo;
            use Barr;
            use Bar1;

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

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLengthFixWithSameLengthAndCaseSensitive(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
            'case_sensitive' => true,
        ]);

        $expected = <<<'EOF'
            <?php

            use Acme;
            use BaRr;
            use Bar1;
            use Fooo;

            class AnnotatedClass { }
            EOF;

        $input = <<<'EOF'
            <?php

            use Acme;
            use Fooo;
            use Bar1;
            use BaRr;

            class AnnotatedClass { }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLengthFixWithMultipleNamespace(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            <?php

            namespace FooRoo {

                use Foo\Bar;
                use Zoo\Tar1, Zoo\Tar2;
                use SomeClass;
                use Foo\Zar\Baz;
                use Foo\Bir as FBB;
                use Zoo\Bar as ZooBar, Foo\Bar\Foo as Fooo;
                use Foo\Bar\FooBar as FooBaz;

                use Symfony\Annotation\Template;

                $a = new Bar();
                $a = new FooBaz();
                $a = new someclass();

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

                        return function () use ($bar, $foo) {};
                    }
                }
            }

            namespace BlaRoo {

                use Zoo\Bar;
                use SomeClass;
                use Foo\Zar\Baz;
                use Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity;

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

                        return function () use ($bar, $foo) {};
                    }
                }
            }
            EOF;

        $input = <<<'EOF'
            <?php

            namespace FooRoo {

                use Foo\Bar\FooBar as FooBaz;
                use Zoo\Bar as ZooBar, Zoo\Tar1;
                use Foo\Bar;
                use Foo\Zar\Baz;
                use Symfony\Annotation\Template;
                use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
                use SomeClass;

                use Zoo\Tar2;

                $a = new Bar();
                $a = new FooBaz();
                $a = new someclass();

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

                        return function () use ($bar, $foo) {};
                    }
                }
            }

            namespace BlaRoo {

                use Foo\Zar\Baz;
                use Zoo\Bar;
                use SomeClass;
                use Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity;

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

                        return function () use ($bar, $foo) {};
                    }
                }
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLengthFixWithComment(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Foo\Bar;
            use Zoo\Tar, SomeClass;
            use Foo\Zar\Baz;
            use Foo\Bir as FBB;
            use Zoo\Bar as ZooBar;
            use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar /* He there */ as FooBaz;
            use /* FIXME */Symfony\Annotation\Template;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

            use Symfony\Doctrine\Entities\Entity;

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

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $input = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Foo\Bar\FooBar /* He there */ as FooBaz;
            use Zoo\Bar as ZooBar, Zoo\Tar;
            use Foo\Bar;
            use Foo\Zar\Baz;
            use /* FIXME */Symfony\Annotation\Template;
            use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
            use SomeClass;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

            use Symfony\Doctrine\Entities\Entity;

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

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLength(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            <?php

            use Foo\Bar;
            use Zoo\Tar, SomeClass;
            use Foo\Zar\Baz;
            use Foo\Bir as FBB;
            use Zoo\Bar as ZooBar;
            use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar as FooBaz;
            use Symfony\Annotation\Template;

            use Symfony\Doctrine\Entities\Entity;

            trait Foo {}

            trait Zoo {}

            class AnnotatedClass
            {
                use Foo, Bar;

                /**
                 * @Template(foobar=21)
                 * @param Entity $foo
                 */
                public function doSomething($foo)
                {
                    $bar = $foo->toArray();
                    /** @var ArrayInterface $bar */

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $input = <<<'EOF'
            <?php

            use Foo\Bar\FooBar as FooBaz;
            use Zoo\Bar as ZooBar, Zoo\Tar;
            use Foo\Bar;
            use Foo\Zar\Baz;
            use Symfony\Annotation\Template;
            use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
            use SomeClass;

            use Symfony\Doctrine\Entities\Entity;

            trait Foo {}

            trait Zoo {}

            class AnnotatedClass
            {
                use Foo, Bar;

                /**
                 * @Template(foobar=21)
                 * @param Entity $foo
                 */
                public function doSomething($foo)
                {
                    $bar = $foo->toArray();
                    /** @var ArrayInterface $bar */

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLengthFixWithTraitImports(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Foo\Bar;
            use Zoo\Tar, SomeClass;
            use Foo\Zar\Baz;
            use Foo\Bir as FBB;
            use Zoo\Bar as ZooBar;
            use Foo\Bar\Foo as Fooo;
            use Acme\MyReusableTrait, Foo\Bar\FooBar as FooBaz;
            use Symfony\Annotation\Template;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

            use Symfony\Doctrine\Entities\Entity;

            class AnnotatedClass
            {
                use MyReusableTrait;

                /**
                 * @Template(foobar=21)
                 * @param Entity $foo
                 */
                public function doSomething($foo)
                {
                    $bar = $foo->toArray();
                    /** @var ArrayInterface $bar */

                    return function () use ($bar, $baz) {};
                }
            }
            EOF;

        $input = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Foo\Bar\FooBar as FooBaz;
            use Zoo\Bar as ZooBar, Zoo\Tar;
            use Foo\Bar;
            use Foo\Zar\Baz;
            use Acme\MyReusableTrait;
            use Symfony\Annotation\Template;
            use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
            use SomeClass;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

            use Symfony\Doctrine\Entities\Entity;

            class AnnotatedClass
            {
                use MyReusableTrait;

                /**
                 * @Template(foobar=21)
                 * @param Entity $foo
                 */
                public function doSomething($foo)
                {
                    $bar = $foo->toArray();
                    /** @var ArrayInterface $bar */

                    return function () use ($bar, $baz) {};
                }
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLengthFixWithDifferentCases(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Baz;
            use abc\Bar;

            <?php

            use abc\Bar;
            use Zoo\Baz;

            class Test
            {
            }
            EOF;

        $input = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Baz;
            use abc\Bar;

            <?php

            use Zoo\Baz;
            use abc\Bar;

            class Test
            {
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLengthOrderWithTrailingDigit(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            <?php

            use abc\Bar;
            use abc2\Bar2;
            use xyz\abc\Bar6;
            use xyz\xyz\Bar4;
            use xyz\xyz\Bar5;
            use xyz\abc2\Bar7;

            class Test
            {
            }
            EOF;

        $input = <<<'EOF'
            <?php

            use abc2\Bar2;
            use abc\Bar;
            use xyz\abc2\Bar7;
            use xyz\abc\Bar6;
            use xyz\xyz\Bar4;
            use xyz\xyz\Bar5;

            class Test
            {
            }
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLengthCodeWithImportsOnly(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            <?php

            use Aaa;
            use Bbb;
            EOF;

        $input = <<<'EOF'
            <?php

            use Bbb;
            use Aaa;
            EOF;

        $this->doTest($expected, $input);
    }

    public function testByLengthWithoutUses(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            <?php

            $c = 1;
            EOF;

        $this->doTest($expected);
    }

    /**
     * @dataProvider provideFixByLengthCases
     */
    public function testFixByLength(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => null,
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixByLengthCases(): iterable
    {
        yield [
            <<<'EOF'
                The normal
                use of this fixer
                should not change this sentence nor those statements below
                use Zoo\Bar as ZooBar;
                use Foo\Bar;
                use Foo\Zar\Baz;

                <?php

                use Foo\Bar;
                use Zoo\Tar, SomeClass;
                 use Foo\Zar\Baz;
                use Foo\Bir as FBB;
                use Zoo\Bar as ZooBar;
                   use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar as FooBaz;
                use Symfony\Annotation\Template;

                $a = new Bar();
                $a = new FooBaz();
                $a = new someclass();

                use Symfony\Doctrine\Entities\Entity;

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

                        return function () use ($bar, $foo) {};
                    }
                }
                EOF,

            <<<'EOF'
                The normal
                use of this fixer
                should not change this sentence nor those statements below
                use Zoo\Bar as ZooBar;
                use Foo\Bar;
                use Foo\Zar\Baz;

                <?php

                use Foo\Bar\FooBar as FooBaz;
                use Zoo\Bar as ZooBar, Zoo\Tar;
                 use Foo\Bar;
                use Foo\Zar\Baz;
                use Symfony\Annotation\Template;
                   use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
                use SomeClass;

                $a = new Bar();
                $a = new FooBaz();
                $a = new someclass();

                use Symfony\Doctrine\Entities\Entity;

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

                        return function () use ($bar, $foo) {};
                    }
                }
                EOF,
        ];

        yield [
            '<?php
use A\B;
use Foo\Bar\Biz;
use some\b\{
    ClassF,
    ClassG
};
use function some\a\{fn_a, fn_b, fn_c};
use some\b\{ClassA, ClassB, ClassC as C};
use const some\a\{ConstA, ConstB, ConstC};
use some\a\{ClassX as X /*z*/, ClassY, ClassZ};
use Some\Biz\Barz\Boozz\Foz\Which\Is\Really\Long;
use const some\b\{ConstG, ConstX, ConstY, ConstZ};
use some\c\{ClassR, ClassT, ClassV as V, NiceClassName};
',
            '<?php
use function some\a\{fn_a, fn_b, fn_c};
use Foo\Bar\Biz;
use some\c\{ClassR, ClassT, ClassV as V, NiceClassName};
use A\B;
use Some\Biz\Barz\Boozz\Foz\Which\Is\Really\Long;
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstB, ConstA, ConstC};
use const some\b\{ConstX, ConstY, ConstZ, ConstG};
use some\b\{ClassA, ClassB, ClassC as C};
use some\a\{  ClassY,ClassZ, /*z*/ ClassX as X};
',
        ];

        yield [
            '<?php
use const ZZZ;
use function B;
use function A123;
',
            '<?php
use function B;
use function A123;
use const ZZZ;
',
        ];
    }

    /**
     * @dataProvider provideFixTypesOrderAndLengthCases
     */
    public function testFixTypesOrderAndLength(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_LENGTH,
            'imports_order' => [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixTypesOrderAndLengthCases(): iterable
    {
        yield [
            '<?php
use A\B;
use Some\Bar;
use Foo\Zar\Baz;
use some\b\{
    ClassF,
    ClassG
};
use some\a\{ClassA, ClassB, ClassC as C};
use some\b\{ClassK, ClassL, ClassM as M};
use some\a\{ClassX as X /*z*/, ClassY, ClassZ};
use const some\a\{ConstA, ConstB, ConstC};
use const some\b\{ConstD, ConstE, ConstF};
use function some\a\{fn_a, fn_b};
use function some\f\{fn_c, fn_d, fn_e};
use function some\b\{fn_k, fn_l, func_m};
',
            '<?php
use const some\a\{ConstA, ConstB, ConstC};
use some\a\{ClassA, ClassB, ClassC as C};
use Foo\Zar\Baz;
use some\b\{ClassK, ClassL, ClassM as M};
use some\a\{ClassX as X /*z*/, ClassY, ClassZ};
use A\B;
use some\b\{
    ClassF,
    ClassG
};
use function some\b\{fn_k, fn_l, func_m};
use Some\Bar;
use function some\a\{fn_a, fn_b};
use const some\b\{ConstD, ConstE, ConstF};
use function some\f\{fn_c, fn_d, fn_e};
',
        ];
    }

    /**
     * @dataProvider provideFixTypesOrderAndAlphabetCases
     *
     * @param string[] $importOrder
     */
    public function testFixTypesOrderAndAlphabet(string $expected, ?string $input = null, array $importOrder = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
            'imports_order' => $importOrder,
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixTypesOrderAndAlphabetCases(): iterable
    {
        yield [
            '<?php
use Aaa\Bbb;
use Aaa\Ccc;
use Bar\Biz\Boooz\Bum;
use Foo\Zar\Baz;
use some\a\{ClassA};
use some\b\{
    ClassF,
    ClassG
};
use some\b\{ClassB, ClassC as C, ClassD};
use Some\Cloz;
use const some\a\{ConstA};
use const some\a\{ConstB, ConstC as CC};
use const some\a\{ConstD};
use const some\b\{ConstE};
use function some\a\{fn_a, fn_b};
use function some\a\{fn_bc};
use function some\b\{fn_c, fn_d, fn_e};
use function some\c\{fn_f};
use function some\f\{fn_g, fn_h, fn_i};
',
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;
use function some\f\{fn_g, fn_h, fn_i};
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use function some\c\{fn_f};
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstD};
use Some\Cloz;
use function some\a\{fn_bc};
use const some\a\{ConstA};
use function some\b\{fn_c, fn_d, fn_e};
use const some\a\{ConstB, ConstC as CC};
use Aaa\Bbb;
use const some\b\{ConstE};
use function some\a\{fn_a, fn_b};
',
            [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
        ];
    }

    /**
     * @dataProvider provideFixTypesOrderAndNoneCases
     *
     * @param null|string[] $importOrder
     */
    public function testFixTypesOrderAndNone(string $expected, ?string $input = null, array $importOrder = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_NONE,
            'imports_order' => $importOrder,
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixTypesOrderAndNoneCases(): iterable
    {
        yield [
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use some\b\{
    ClassF,
    ClassG
};
use Some\Cloz;
use Aaa\Bbb;
use const some\a\{ConstD};
use const some\a\{ConstA};
use const some\a\{ConstB, ConstC as CC};
use const some\b\{ConstE};
use function some\f\{fn_g, fn_h, fn_i};
use function some\c\{fn_f};
use function some\a\{fn_x};
use function some\b\{fn_c, fn_d, fn_e};
use function some\a\{fn_a, fn_b};
',
            '<?php
use Aaa\Ccc;
use Foo\Zar\Baz;
use function some\f\{fn_g, fn_h, fn_i};
use some\a\{ClassA};
use some\b\{ClassD, ClassB, ClassC as C};
use Bar\Biz\Boooz\Bum;
use function some\c\{fn_f};
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstD};
use Some\Cloz;
use function some\a\{fn_x};
use const some\a\{ConstA};
use function some\b\{fn_c, fn_d, fn_e};
use const some\a\{ConstB, ConstC as CC};
use Aaa\Bbb;
use const some\b\{ConstE};
use function some\a\{fn_a, fn_b};
',
            [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
        ];
    }

    public function testFixByNone(): void
    {
        $this->fixer->configure([
            'sort_algorithm' => OrderedImportsFixer::SORT_NONE,
            'imports_order' => null,
        ]);

        $expected = <<<'EOF'
            The normal
            use of this fixer
            should not change this sentence nor those statements below
            use Zoo\Bar as ZooBar;
            use Foo\Bar;
            use Foo\Zar\Baz;

            <?php

            use Foo\Bar\FooBar as FooBaz;
            use Zoo\Bar as ZooBar, Zoo\Tar;
             use Foo\Bar;
            use Foo\Zar\Baz;
            use Symfony\Annotation\Template;
               use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
            use SomeClass;

            $a = new Bar();
            $a = new FooBaz();
            $a = new someclass();

            use Symfony\Doctrine\Entities\Entity;

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

                    return function () use ($bar, $foo) {};
                }
            }
            EOF;

        $this->doTest($expected);
    }

    public function testFixWithCaseSensitive(): void
    {
        $this->fixer->configure([
            'case_sensitive' => true,
        ]);

        $expected = <<<'EOF'
            <?php

            use AA;
            use Aaa;

            class Foo { }
            EOF;

        $input = <<<'EOF'
            <?php

            use Aaa;
            use AA;

            class Foo { }
            EOF;

        $this->doTest($expected, $input);
    }
}
