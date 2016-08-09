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

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Denis Platov <d.platov@owox.com>
 */
class TwoEmptyLinesFixerTest extends AbstractFixerTestBase
{
    public function testFix()
    {
        $input = <<<'EOF'
<?php
abstract class Foo {
const TEST = 1;
public $foo1 = null;
public $foo2 = null;
protected $foo3 = null;
public function foo1() {}
public function &foo2() {}
protected function foo3() {}
abstract protected function foo4();
}
EOF;

        $expected = <<<'EOF'
<?php
abstract class Foo {

const TEST = 1;


public $foo1 = null;

public $foo2 = null;


protected $foo3 = null;


public function foo1() {}


public function &foo2() {}


protected function foo3() {}


abstract protected function foo4();
}
EOF;
        $this->makeTest($expected, $input);
    }
}
