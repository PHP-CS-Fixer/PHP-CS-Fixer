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

use Symfony\CS\Fixer\UseStatementsOrderFixer;

class UseStatementsOrderFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new UseStatementsOrderFixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
use Abc\Bar\AbcBar as Baz;
use Abc\Class;
use Foo\Bar;
use Vendor/Component/SomeClass;
use Vendor/CS/SomeClass;
use XyzClass;

$bar = new Bar();

// sample comment: don't use abc;
EOF;

        $input = <<<'EOF'
use XyzClass;
use Foo\Bar;

use Abc\Class;
use Abc\Bar\AbcBar as Baz;
use Vendor/CS/SomeClass;
use Vendor/Component/SomeClass;

$bar = new Bar();

// sample comment: don't use abc;
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }
}
