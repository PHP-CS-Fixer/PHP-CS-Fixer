<?php

/*
 * This file is part of the Symfony CS utility.
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
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
use Foo\Bar;
use Foo\Bar\FooBar as FooBaz;

$a = new Bar();
$a = new FooBaz();
EOF;

        $input = <<<'EOF'
use Foo\Bar;
use Foo\Bar\Baz;
use Foo\Bar\FooBar as FooBaz;
use Foo\Bar\Foo as Fooo;
use Foo\Bar\Baar\Baar;

$a = new Bar();
$a = new FooBaz();
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }
}
