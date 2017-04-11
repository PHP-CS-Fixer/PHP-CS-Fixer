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

use PhpCsFixer\Differ\SebastianBergmannDiffer;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Differ\SebastianBergmannDiffer
 */
final class SebastianBergmannDifferTest extends AbstractDifferTestCase
{
    public function testDiffReturnsDiff()
    {
        $diff = <<<'TXT'
--- Original
+++ New
@@ @@
 <?php
 class Foo extends Bar {
-    function __construct($foo, $bar) {
+    public function __construct($foo, $bar)
+    {
         $this->foo = $foo;
         $this->bar = $bar;
     }
 }

TXT;

        $differ = new SebastianBergmannDiffer();

        $this->assertSame($diff, $differ->diff($this->oldCode(), $this->newCode()));
    }
}
