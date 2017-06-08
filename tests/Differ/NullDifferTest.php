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

namespace PhpCsFixer\Tests\Differ;

use PhpCsFixer\Differ\NullDiffer;
use PHPUnit\Framework\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Differ\NullDiffer
 */
final class NullDifferTest extends TestCase
{
    public function testIsDiffer()
    {
        $differ = new NullDiffer();

        $this->assertInstanceOf('PhpCsFixer\Differ\DifferInterface', $differ);
    }

    public function testDiffReturnsEmptyString()
    {
        $old = <<<'PHP'
<?php
class Foo extends Bar {
    function __construct($foo, $bar) {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
PHP;

        $new = <<<'PHP'
<?php
class Foo extends Bar {
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
PHP;

        $differ = new NullDiffer();

        $this->assertSame('', $differ->diff($old, $new));
    }
}
