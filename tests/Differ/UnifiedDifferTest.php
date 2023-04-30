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

use PhpCsFixer\Differ\UnifiedDiffer;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Differ\UnifiedDiffer
 */
final class UnifiedDifferTest extends AbstractDifferTestCase
{
    public function testDiffReturnsDiff(): void
    {
        $differ = new UnifiedDiffer();
        $file = __FILE__;

        $diff = '--- '.$file.'
+++ '.$file.'
@@ -2,7 +2,7 @@
 '.'
 function baz($options)
 {
-    if (!array_key_exists("foo", $options)) {
+    if (!\array_key_exists("foo", $options)) {
         throw new \InvalidArgumentException();
     }
 '.'
';
        self::assertSame($diff, $differ->diff($this->oldCode(), $this->newCode(), new \SplFileInfo($file)));
    }

    public function testDiffAddsQuotes(): void
    {
        $differ = new UnifiedDiffer();

        self::assertSame(
            '--- "test test test.txt"
+++ "test test test.txt"
@@ -1 +1 @@
-a
+b
',
            $differ->diff("a\n", "b\n", new DummyTestSplFileInfo('/foo/bar/test test test.txt'))
        );
    }

    public function testDiffWithoutFile(): void
    {
        $differ = new UnifiedDiffer();

        self::assertSame(
            '--- Original
+++ New
@@ -1 +1 @@
-a
\ No newline at end of file
+b
\ No newline at end of file
',
            $differ->diff('a', 'b')
        );
    }
}
