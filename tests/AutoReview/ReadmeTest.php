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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Tests\TestCase;

/**
 * @author Victor Bocharsky <bocharsky.bw@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group auto-review
 * @group covers-nothing
 */
final class ReadmeTest extends TestCase
{
    public function testSupportedPhpVersions(): void
    {
        $phpVersionIdLines = [];

        $file = new \SplFileObject(__DIR__.'/../../php-cs-fixer');
        while (!$file->eof()) {
            $line = $file->fgets();
            if (str_contains($line, 'PHP_VERSION_ID')) {
                $phpVersionIdLines[] = $line;
            }
        }
        // Unset the file to call __destruct(), closing the file handle.
        $file = null;

        static::assertEqualsCanonicalizing([
            '    if (\PHP_VERSION_ID === 80000) {'."\n",
            '    if (\PHP_VERSION_ID < 70400 || \PHP_VERSION_ID >= 80300) {'."\n",
        ], $phpVersionIdLines, 'Seems supported PHP versions changed in "./php-cs-fixer" - edit the README.md (and this test file) to match them!');
    }
}
