<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\UnusedUseStatementsFixer;

class UnusedUseStatementsFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new UnusedUseStatementsFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
use Foo\Bar;
use Foo\Bar\FooBar as FooBaz;
use SomeClass;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();

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
use Foo\Bar;
use Foo\Bar\Baz;
use Foo\Bar\FooBar as FooBaz;
use Foo\Bar\Foo as Fooo;
use Foo\Bar\Baar\Baar;
use SomeClass;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();

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

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function testFixUseInTheSameNamespace()
    {
        $fixer = new UnusedUseStatementsFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
namespace Foo\Bar\FooBar;

use Foo\Bar\FooBar\Foo as Fooz;

$a = new Baz();
$b = new Fooz();
$c = new Bar\Fooz();
EOF;

        $input = <<<'EOF'
namespace Foo\Bar\FooBar;

use Foo\Bar\FooBar\Baz;
use Foo\Bar\FooBar\Foo as Fooz;
use Foo\Bar\FooBar\Bar;

$a = new Baz();
$b = new Fooz();
$c = new Bar\Fooz();
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }

    public function testTrailingSpaces()
    {
        $fixer = new UnusedUseStatementsFixer();
        $file = $this->getTestFile();

        $expected = <<<'EOF'
use Foo\Bar ;
use Foo\Bar\FooBar as FooBaz ;

$a = new Bar();
$a = new FooBaz();
EOF;

        $input = <<<'EOF'
use Foo\Bar ;
use Foo\Bar\FooBar as FooBaz ;
use Foo\Bar\Foo as Fooo ;
use SomeClass ;

$a = new Bar();
$a = new FooBaz();
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
