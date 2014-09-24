<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class RemoveLinesBetweenUsesFixerTest extends AbstractFixerTestBase
{
    public function testRemoveLinesBetweenUseStatements()
    {
        $expected = <<<'EOF'
<?php

use Zxy\Qux;
use Zoo\Bar as Bar2;
use Foo\Bar as Bar1;
use Foo\Zar\Baz;

$c = 1;

use Foo\Quxx as Quxx1;
use Foo\Zar\Quxx;

$a = new Bar1();
$a = new Bar2();
$a = new Baz();
$a = new Qux();
EOF
        ;

        $input = <<<'EOF'
<?php

use Zxy\Qux;

use Zoo\Bar as Bar2;

use Foo\Bar as Bar1;
use Foo\Zar\Baz;

$c = 1;

use Foo\Quxx as Quxx1;

use Foo\Zar\Quxx;

$a = new Bar1();
$a = new Bar2();
$a = new Baz();
$a = new Qux();
EOF
        ;

        $this->makeTest($expected, $input);
    }
}
