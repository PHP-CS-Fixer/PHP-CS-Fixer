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

use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\OrderedImportsFixer
 */
final class OrderedImportsFixerTest extends AbstractFixerTestCase
{
    public function testFix()
    {
        $expected = <<<'EOF'
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
   use Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity;
use Zoo\Bar as ZooBar;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();

use Zoo\Tar;

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

        $this->doTest($expected, $input);
    }

    public function testFixWithMultipleNamespace()
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

        $this->doTest($expected, $input);
    }

    public function testFixWithComment()
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
   use /* FIXME */Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity;
use Zoo\Bar as ZooBar;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();

use Zoo\Tar;

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

    public function testWithTraits()
    {
        $expected = <<<'EOF'
<?php

use Foo\Bar;
use Foo\Bar\Foo as Fooo, Foo\Bar\FooBar as FooBaz;
 use Foo\Bir as FBB;
use Foo\Zar\Baz;
use SomeClass;
   use Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity;
use Zoo\Bar as ZooBar;

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

    public function testFixWithTraitImports()
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
   use Symfony\Annotation\Template, Symfony\Doctrine\Entities\Entity;
use Zoo\Bar as ZooBar;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();

use Zoo\Tar;

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

    public function testFixWithDifferentCases()
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

    public function testWithoutUses()
    {
        $expected = <<<'EOF'
<?php

$c = 1;
EOF
        ;

        $this->doTest($expected);
    }

    public function testOrderWithTrailingDigit()
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

    public function testCodeWithImportsOnly()
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

    public function testCodeWithCloseTag()
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

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCommentCases
     */
    public function testCodeWithComments($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCommentCases()
    {
        return [
            [
                '<?php
                    use A\C1 /* A */;
                    use /* B */ B\C2;',
                '<?php
                    use /* B */ B\C2;
                    use A\C1 /* A */;',
            ],
            [
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
                    use A\C1;',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return [
            [
                '<?php
use A\B;
use some\a\{ClassA, ClassB, ClassC as C};
use some\b\{
    ClassF,
    ClassG
};
use const some\a\{ConstA, ConstB, ConstC};
use const some\b\{
    ConstA,
    ConstB,
    ConstC
};
use function some\a\{fn_a, fn_b, fn_c};
use function some\b\{
    fn_a,
    fn_b,
    fn_c
};
',
                '<?php
use some\a\{ClassA, ClassB, ClassC as C};
use function some\b\{
    fn_b,
    fn_c,
    fn_a
};
use function some\a\{fn_a, fn_b, fn_c};
use A\B;
use const some\b\{
    ConstC,
    ConstA,
    ConstB
};
use const some\a\{ConstA, ConstB, ConstC};
use some\b\{
    ClassG,
    ClassF
};
',
            ],
            [
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
            ],
            [
                '<?php
use Foo\Bar\Baz;use Foo\Bar\{ClassA, ClassB, ClassC};
use Foo\Bir;
',
                '<?php
use Foo\Bar\Baz, Foo\Bir;
use Foo\Bar\{ClassC, ClassB, ClassA};
',
            ],
            [
                '<?php
use A\A;use Foo3\Bar\{ClassA};use G\G;use H\H;use Ioo2\Bar\{ClassB};use J\J;use K\K;use Loo1\Bar\{ClassC};use M\M;
',
                '<?php
use A\A,G\G;use Foo3\Bar\{ClassA};use H\H,J\J;use Ioo2\Bar\{ClassB};use K\K,M\M;use Loo1\Bar\{ClassC};
',
            ],
            [
                '<?php
use Foo\Bar\Baz;use Foo\Bar\{ClassA, ClassB, ClassC};
use Foo\Bir;
',
                '<?php
use Foo\Bar\Baz, Foo\Bir;
use Foo\Bar\{ClassC, ClassB, ClassA};
',
            ],
            [
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
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Test sorting by length
    |--------------------------------------------------------------------------
    */

    public function testInvalidOrderTypesSize()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('[ordered_imports] Invalid configuration: Missing sort type "function".');

        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_ALPHA,
            'importsOrder' => ['class', 'const'],
        ]);
    }

    public function testInvalidOrderType()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('[ordered_imports] Invalid configuration: Missing sort type "class".');

        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_ALPHA,
            'importsOrder' => ['const', 'function', 'bar'],
        ]);
    }

    /**
     * @dataProvider provideInvalidSortAlgorithmCases
     *
     * @param array  $configuration
     * @param string $expectedValue
     */
    public function testInvalidSortAlgorithm($configuration, $expectedValue)
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage(sprintf(
            '[ordered_imports] Invalid configuration: The option "sortAlgorithm" with value %s is invalid. Accepted values are: "alpha", "length", "none".',
            $expectedValue
        ));

        $this->fixer->configure($configuration);
    }

    public function provideInvalidSortAlgorithmCases()
    {
        return [
            [
                [
                    'sortAlgorithm' => 'dope',
                    'importsOrder' => null,
                ],
                '"dope"',
            ],
            [
                [
                    'sortAlgorithm' => [OrderedImportsFixer::SORT_ALPHA, OrderedImportsFixer::SORT_LENGTH],
                    'importsOrder' => null,
                ],
                'array',
            ],
            [
                [
                    'sortAlgorithm' => new \stdClass(),
                    'importsOrder' => null,
                ],
                \stdClass::class,
            ],
        ];
    }

    public function testFixByLength()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ]);

        $expected = <<<'EOF'
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
EOF;

        $input = <<<'EOF'
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

        $this->doTest($expected, $input);
    }

    public function testByLengthFixWithSameLength()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
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

    public function testByLengthFixWithMultipleNamespace()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
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

    $a = new Bar();
    $a = new FooBaz();
    $a = new someclass();

    use Symfony\Annotation\Template;

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

        $this->doTest($expected, $input);
    }

    public function testByLengthFixWithComment()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
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

    public function testByLength()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
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

    public function testByLengthFixWithTraitImports()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
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

    public function testByLengthFixWithDifferentCases()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
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

    public function testByLengthOrderWithTrailingDigit()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
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

    public function testByLengthCodeWithImportsOnly()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
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

    public function testByLengthWithoutUses()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ]);

        $expected = <<<'EOF'
<?php

$c = 1;
EOF
        ;

        $this->doTest($expected);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70ByLengthCases
     * @requires PHP 7.0
     */
    public function testFix70ByLength($expected, $input = null)
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFix70ByLengthCases()
    {
        return [
            [
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
use some\a\{ClassA as A /*z*/, ClassB, ClassC};
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
use some\a\{  ClassB,ClassC, /*z*/ ClassA as A};
',
            ],
        ];
    }

    /**
     * @dataProvider provideFix70TypesOrderAndLengthCases
     * @requires PHP 7.0
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix70TypesOrderAndLength($expected, $input = null)
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFix70TypesOrderAndLengthCases()
    {
        return [
            [
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
use some\a\{ClassA as A /*z*/, ClassB, ClassC};
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
use some\a\{ClassA as A /*z*/, ClassB, ClassC};
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
            ],
        ];
    }

    /**
     * @dataProvider provideFix70TypesOrderAndAlphabetCases
     * @requires PHP 7.0
     *
     * @param string      $expected
     * @param null|string $input
     * @param string[]    $importOrder
     */
    public function testFix70TypesOrderAndAlphabet($expected, $input = null, array $importOrder = null)
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_ALPHA,
            'importsOrder' => $importOrder,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFix70TypesOrderAndAlphabetCases()
    {
        return [
            [
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
use function some\a\{fn};
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
use function some\a\{fn};
use const some\a\{ConstA};
use function some\b\{fn_c, fn_d, fn_e};
use const some\a\{ConstB, ConstC as CC};
use Aaa\Bbb;
use const some\b\{ConstE};
use function some\a\{fn_a, fn_b};
',
                [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
            ],
        ];
    }

    /**
     * @dataProvider provideFix70TypesOrderAndNoneCases
     * @requires PHP 7.0
     *
     * @param string      $expected
     * @param null|string $input
     * @param string[]    $importOrder
     */
    public function testFix70TypesOrderAndNone($expected, $input = null, array $importOrder = null)
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_NONE,
            'importsOrder' => $importOrder,
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFix70TypesOrderAndNoneCases()
    {
        return [
            [
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
use function some\a\{fn};
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
use function some\a\{fn};
use const some\a\{ConstA};
use function some\b\{fn_c, fn_d, fn_e};
use const some\a\{ConstB, ConstC as CC};
use Aaa\Bbb;
use const some\b\{ConstE};
use function some\a\{fn_a, fn_b};
',
                [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $config
     *
     * @dataProvider provideFix72Cases
     * @requires PHP 7.2
     */
    public function testFix72($expected, $input = null, array $config = null)
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideFix72Cases()
    {
        $input =
            '<?php use A\{B,};
use some\y\{ClassA, ClassB, ClassC as C,};
use function some\a\{fn_a, fn_b, fn_c,};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use C\{D,E,};
';

        return [
            [
                '<?php
use A\{B,};
use C\{D,E,};
',
                '<?php
use C\{D,E,};
use A\{B,};
',
            ],
            [
                '<?php use A\{B,};
use C\{D,E,};
use some\y\{ClassA, ClassB, ClassC as C,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use function some\a\{fn_a, fn_b, fn_c,};
',
                $input,
                [
                    'sortAlgorithm' => OrderedImportsFixer::SORT_ALPHA,
                    'importsOrder' => [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
                ],
            ],
            [
                '<?php use A\{B,};
use C\{D,E,};
use some\y\{ClassA, ClassB, ClassC as C,};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use function some\a\{fn_a, fn_b, fn_c,};
',
                $input,
                [
                    'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
                    'importsOrder' => [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
                ],
            ],
            [
                '<?php use A\{B,};
use some\y\{ClassA, ClassB, ClassC as C,};
use C\{D,E,};
use const some\Z\{ConstAA,ConstBB,ConstCC,};
use const some\X\{ConstA,ConstB,ConstC,ConstF};
use function some\a\{fn_a, fn_b, fn_c,};
',
                $input,
                [
                    'sortAlgorithm' => OrderedImportsFixer::SORT_NONE,
                    'importsOrder' => [OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION],
                ],
            ],
        ];
    }

    public function testFixByNone()
    {
        $this->fixer->configure([
            'sortAlgorithm' => OrderedImportsFixer::SORT_NONE,
            'importsOrder' => null,
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
}
