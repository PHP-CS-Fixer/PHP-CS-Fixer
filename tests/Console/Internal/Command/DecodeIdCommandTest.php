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

namespace PhpCsFixer\Tests\Console\Internal\Command;

use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Internal\Command\DecodeIdCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DecodeIdCommandTest extends TestCase
{
    public function testNotice(): void
    {
        self::markTestIncomplete('Tests is not implemented for this internal command, consider to support and implement it.');
    }
}
