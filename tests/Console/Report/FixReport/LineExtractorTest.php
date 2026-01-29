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

namespace PhpCsFixer\Tests\Console\Report\FixReport;

use PhpCsFixer\Console\Report\FixReport\LineExtractor;
use PhpCsFixer\Tests\TestCase;
use SebastianBergmann\Diff\Parser;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\LineExtractor
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class LineExtractorTest extends TestCase
{
    /**
     * @dataProvider provideGetLinesCases
     *
     * @param array{begin: int, end: int} $expected
     */
    public function testGetLines(array $expected, string $diff): void
    {
        $parser = new Parser();
        self::assertSame($expected, LineExtractor::getLines($parser->parse($diff)));
    }

    /**
     * @return iterable<string, array{0: array{begin: int, end: int}, 1: string}>
     */
    public static function provideGetLinesCases(): iterable
    {
        yield 'empty diff' => [
            ['begin' => 0, 'end' => 0],
            '',
        ];

        yield 'simple diff' => [
            ['begin' => 5, 'end' => 9],
            '--- Original
+++ New
@@ -2,7 +2,7 @@

 class Foo
 {
-    public function bar($foo = 1, $bar)
+    public function bar($foo, $bar)
     {
     }
 }',
        ];

        yield 'diff with multiple chunks (only first one is used)' => [
            ['begin' => 5, 'end' => 9],
            '--- Original
+++ New
@@ -2,7 +2,7 @@

 class Foo
 {
-    public function bar($foo = 1, $bar)
+    public function bar($foo, $bar)
     {
     }
 }
@@ -12,4 +12,4 @@
 {
-    public function baz()
+    public function qux()
     {
     }',
        ];

        yield 'diff with modification on the first line of the chunk' => [
            ['begin' => 2, 'end' => 6],
            '--- Original
+++ New
@@ -2,4 +2,4 @@
-class Foo
+class Bar
 {
 }',
        ];

        yield 'diff with modification after some unchanged lines' => [
            ['begin' => 8, 'end' => 13],
            '--- Original
+++ New
@@ -8,5 +8,5 @@
-        $a = 1;
+        $a = 2;
         $b = 2;
     }',
        ];
    }
}
