<?php

declare(strict_types=1);

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

use PhpCsFixer\Differ\FullDiffer;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Differ\FullDiffer
 */
final class FullDifferTest extends AbstractDifferTestCase
{
    public function testDiffReturnsDiff(): void
    {
        $diff = '--- Original
+++ New
@@ -1,10 +1,10 @@
 <?php
 '.'
 function baz($options)
 {
-    if (!array_key_exists("foo", $options)) {
+    if (!\array_key_exists("foo", $options)) {
         throw new \InvalidArgumentException();
     }
 '.'
     return json_encode($options);
 }
';
        $differ = new FullDiffer();

        self::assertSame($diff, $differ->diff($this->oldCode(), $this->newCode()));
    }
}
