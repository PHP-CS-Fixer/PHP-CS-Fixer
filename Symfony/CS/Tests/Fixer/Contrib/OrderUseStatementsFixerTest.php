<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Fixer\Contrib\OrderUseStatementsFixer as Fixer;

class OrderUseStatementsFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new Fixer();
        $file  = $this->getTestFile();

        $expected = <<<'EOF'
The normal
use of this fixer
should not change this sentence nor those statements below
use Zoo\Bar;
use Foo\Bar;
use Foo\Zar\Baz;

<?php

 use Foo\Bar;
   use Foo\Bar\Foo as Fooo, Foo\Bir as FBB;
use Foo\Bar\FooBar as FooBaz;
use Foo\Zar\Baz;
use SomeClass;
use Symfony\Annotation\Template;
use Symfony\Doctrine\Entities\Entity;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();

use Zoo\Bar, Zoo\Tar;

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
use Zoo\Bar, Zoo\Tar;
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

        return function () use ($bar, $baz) {};
    }
}
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
