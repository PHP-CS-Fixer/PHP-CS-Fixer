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

    /**
     * @requires PHP 5.4
     */
    public function test54()
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

    /**
     * @requires PHP 5.4
     */
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
        return array(
            array(
                '<?php
                    use A\C1 /* A */;
                    use /* B */ B\C2;',
                '<?php
                    use /* B */ B\C2;
                    use A\C1 /* A */;',
            ),
            array(
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
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provide70Cases
     * @requires PHP 7.0
     */
    public function test70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provide70Cases()
    {
        return array(
            array(
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
            ),
            array(
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
            ),
            array(
                '<?php
use Foo\Bar\Baz;use Foo\Bar\{ClassA, ClassB, ClassC};
use Foo\Bir;
',
                '<?php
use Foo\Bar\Baz, Foo\Bir;
use Foo\Bar\{ClassC, ClassB, ClassA};
',
            ),
            array(
                '<?php
use A\A;use Foo3\Bar\{ClassA};use G\G;use H\H;use Ioo2\Bar\{ClassB};use J\J;use K\K;use Loo1\Bar\{ClassC};use M\M;
',
                '<?php
use A\A,G\G;use Foo3\Bar\{ClassA};use H\H,J\J;use Ioo2\Bar\{ClassB};use K\K,M\M;use Loo1\Bar\{ClassC};
',
            ),
            array(
                '<?php
use Foo\Bar\Baz;use Foo\Bar\{ClassA, ClassB, ClassC};
use Foo\Bir;
',
                '<?php
use Foo\Bar\Baz, Foo\Bir;
use Foo\Bar\{ClassC, ClassB, ClassA};
',
            ),
            array(
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
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Test sorting by length
    |--------------------------------------------------------------------------
    */

    public function testInvalidOrderTypesSize()
    {
        $this->setExpectedException(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '[ordered_imports] Invalid configuration: Missing sort type "function".'
        );

        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_ALPHA,
            'importsOrder' => array('class', 'const'),
        ));
    }

    public function testInvalidOrderType()
    {
        $this->setExpectedException(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '[ordered_imports] Invalid configuration: Missing sort type "class".'
        );

        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_ALPHA,
            'importsOrder' => array('const', 'function', 'bar'),
        ));
    }

    /**
     * @dataProvider provideInvalidSortAlgorithmConfiguration
     *
     * @param array  $configuration
     * @param string $expectedValue
     */
    public function testInvalidSortAlgorithm($configuration, $expectedValue)
    {
        $this->setExpectedException(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            sprintf(
                '[ordered_imports] Invalid configuration: The option "sortAlgorithm" with value %s is invalid. Accepted values are: "alpha", "length".',
                $expectedValue
            )
        );

        $this->fixer->configure($configuration);
    }

    public function provideInvalidSortAlgorithmConfiguration()
    {
        return array(
            array(
                array(
                    'sortAlgorithm' => 'dope',
                    'importsOrder' => null,
                ),
                '"dope"',
            ),
            array(
                array(
                    'sortAlgorithm' => array(OrderedImportsFixer::SORT_ALPHA, OrderedImportsFixer::SORT_LENGTH),
                    'importsOrder' => null,
                ),
                'array',
            ),
            array(
                array(
                    'sortAlgorithm' => new \stdClass(),
                    'importsOrder' => null,
                ),
                'stdClass',
            ),
        );
    }

    public function testFixByLength()
    {
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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

    /**
     * @requires PHP 5.4
     */
    public function testByLength54()
    {
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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

    /**
     * @requires PHP 5.4
     */
    public function testByLengthFixWithTraitImports()
    {
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

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
     * @dataProvider provide70CasesByLength
     * @requires PHP 7.0
     */
    public function test70ByLength($expected, $input = null)
    {
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => null,
        ));

        $this->doTest($expected, $input);
    }

    public function provide70CasesByLength()
    {
        return array(
            array(
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
            ),
        );
    }

    /**
     * @dataProvider provide70TypesOrderAndLength
     * @requires PHP 7.0
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function test70TypesOrderAndLength($expected, $input = null)
    {
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_LENGTH,
            'importsOrder' => array(OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION),
        ));

        $this->doTest($expected, $input);
    }

    public function provide70TypesOrderAndLength()
    {
        return array(
            array(
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
            ),
        );
    }

    /**
     * @dataProvider provide70TypesOrderAndAlphabet
     * @requires PHP 7.0
     *
     * @param string      $expected
     * @param null|string $input
     * @param string[]    $importOrder
     */
    public function test70TypesOrderAndAlphabet($expected, $input = null, array $importOrder = null)
    {
        $this->fixer->configure(array(
            'sortAlgorithm' => OrderedImportsFixer::SORT_ALPHA,
            'importsOrder' => $importOrder,
        ));

        $this->doTest($expected, $input);
    }

    public function provide70TypesOrderAndAlphabet()
    {
        return array(
            array(
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
                array(OrderedImportsFixer::IMPORT_TYPE_CLASS, OrderedImportsFixer::IMPORT_TYPE_CONST, OrderedImportsFixer::IMPORT_TYPE_FUNCTION),
            ),
        );
    }
}
