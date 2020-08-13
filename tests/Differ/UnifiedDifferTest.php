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
        $diff = <<<'DIFF'
--- /tests/Fixtures/FixerTest/invalid	2020-05-14 17:29:40.000000 0
+++ /tests/Fixtures/FixerTest/invalid	2020-05-14 17:29:40.000000 0
@@ -2,7 +2,7 @@
 
 function baz($options)
 {
-    if (!array_key_exists("foo", $options)) {
+    if (!\array_key_exists("foo", $options)) {
         throw new \InvalidArgumentException();
     }
 

DIFF;
        $differ = new UnifiedDiffer();

        $path = realpath(__DIR__.\DIRECTORY_SEPARATOR.'..').\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'invalid';
        $fakeFile = new \SplFileInfo($path.\DIRECTORY_SEPARATOR.'somefile.php');

        static::assertSame($diff, $differ->diff($this->oldCode(), $this->newCode(), $fakeFile));
    }
}
