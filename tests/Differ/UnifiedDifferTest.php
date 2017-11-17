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

use PhpCsFixer\Differ\UnifiedDiffer;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Differ\UnifiedDiffer
 */
final class UnifiedDifferTest extends AbstractDifferTestCase
{
    public function testDiffReturnsDiff()
    {
        $diff = <<<'TXT'
--- Original
+++ New
@@ -1,7 +1,8 @@
 <?php
 class Foo extends Bar {
-    function __construct($foo, $bar) {
+    public function __construct($foo, $bar)
+    {
         $this->foo = $foo;
         $this->bar = $bar;
     }
 }
\ No newline at end of file

TXT;
        $differ = new UnifiedDiffer();

        $this->assertSame($diff, $differ->diff($this->oldCode(), $this->newCode()));
    }
}
