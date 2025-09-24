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

namespace PhpCsFixer\Tests\Runner\Parallel;

use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Runner\Parallel\ProcessUtils
 */
final class ProcessUtilsTest extends TestCase
{
    public function testEscapeArgument(): void
    {
        self::markTestSkipped('The `escapeArgument` method is copy-pasted from Symfony codebase. Is tested indirectly on testing Parallel flow under Windows.');
    }
}
