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

namespace PhpCsFixer\Tests\FixerBlame;

use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class FixerBlameTest extends TestCase
{
    public function testTodo(): void
    {
        self::markTestSkipped();
    }
}
