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

use PhpCsFixer\Differ\SebastianBergmannShortDiffer;
use PHPUnit\Framework\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Differ\SebastianBergmannShortDiffer
 */
final class SebastianBergmannShortDifferTest extends TestCase
{
    public function testIsDiffer()
    {
        $differ = new SebastianBergmannShortDiffer();

        $this->assertInstanceOf('PhpCsFixer\Differ\DifferInterface', $differ);
    }

    public function testDiffReturnsDiff()
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

        $diff = <<<'TXT'
--- Original
+++ New
-    function __construct($foo, $bar) {
+    public function __construct($foo, $bar)
+    {

TXT;

        $differ = new SebastianBergmannShortDiffer();

        $this->assertSame($diff, $differ->diff($old, $new));
    }
}
