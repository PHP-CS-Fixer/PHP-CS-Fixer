<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *     and contributors <https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/graphs/contributors>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

use PhpCsFixer\PharChecker;

/**
 * @internal
 *
 * @covers \PhpCsFixer\PharChecker
 */
final class PharCheckerTest extends TestCase
{
    public function testPharChecker(): void
    {
        $checker = new PharChecker();
        self::assertNull($checker->checkFileValidity(__DIR__.'/Fixtures/empty.phar'));
    }

    public function testPharCheckerInvalidFile(): void
    {
        $checker = new PharChecker();
        self::assertStringStartsWith('Failed to create Phar instance.', $checker->checkFileValidity(__FILE__));
    }
}
