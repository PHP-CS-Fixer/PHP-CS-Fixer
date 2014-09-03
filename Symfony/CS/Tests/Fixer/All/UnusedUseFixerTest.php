<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\All;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class UnusedUseFixerTest extends AbstractFixerTestBase
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
use Symfony\Array\ArrayInterface;

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
use Symfony\Array\ArrayInterface;

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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
    }

    public function testFixUseInTheSameNamespace()
    {
        $expected = <<<'EOF'
<?php

namespace Foo\Bar\FooBar;

use Foo\Bar\FooBar\Foo as Fooz;
use Foo\Bar\FooBar\Aaa\Bbb;

$a = new Baz();
$b = new Fooz();
$c = new Bar\Fooz();
$d = new Bbb();
EOF;

        $input = <<<'EOF'
<?php

namespace Foo\Bar\FooBar;

use Foo\Bar\FooBar\Baz;
use Foo\Bar\FooBar\Foo as Fooz;
use Foo\Bar\FooBar\Bar;
use Foo\Bar\FooBar\Aaa\Bbb;

$a = new Baz();
$b = new Fooz();
$c = new Bar\Fooz();
$d = new Bbb();
EOF;

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider providerUseInString
     */
    public function testUseInString($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function providerUseInString()
    {
        $expected1 = <<<'EOF'
$x=<<<'EOA'
use a;
use b;
EOA;
EOF;

        $expected2 = <<<'EOF'
$x='
use a;
use b;
';
EOF;

        $expected3 = <<<'EOF'
$x="
use a;
use b;
";
EOF;

        return array(
            array($expected1),
            array($expected2),
            array($expected3),
        );
    }
}
