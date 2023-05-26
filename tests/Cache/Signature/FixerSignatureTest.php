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

namespace PhpCsFixer\Tests\Cache\Signature;

use PhpCsFixer\Cache\Signature\FixerSignature;
use PhpCsFixer\Tests\Fixtures\SignatureFixer;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Cache\Signature\FixerSignature
 */
final class FixerSignatureTest extends TestCase
{
    public function testIsPossibleToCreateFromFixerInstance(): void
    {
        $signature = FixerSignature::fromInstance(
            new SignatureFixer(),
            true
        );

        self::assertSame('signature_fixer', $signature->getName());
        self::assertSame('413d3c9e640e242fe6a3c62b8d4bb9f5', $signature->getContentHash());
        self::assertTrue($signature->getConfig());
    }

    public function testIsPossibleToCreateFromRawValues(): void
    {
        $signature = FixerSignature::fromRawValues(
            $name = 'dummy_fixer',
            $hash = 'abcdefghijklmnopqrstuvwxyz123456',
            $config = ['foo' => 'bar']
        );

        self::assertSame($name, $signature->getName());
        self::assertSame($hash, $signature->getContentHash());
        self::assertSame($config, $signature->getConfig());
    }
}
