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
use PhpCsFixer\Cache\Signature\RulesSignature;
use PhpCsFixer\Tests\Fixtures\SignatureFixer;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Cache\Signature\RulesSignature
 */
final class RulesSignatureTest extends TestCase
{
    public function testConstructorSetsValues(): void
    {
        $signature = new RulesSignature(
            FixerSignature::fromInstance(new SignatureFixer(), true)
        );

        self::assertSame('c561b45d3402d7d0ef99640fea87da5e', $signature->getHash());
        self::assertCount(1, $signature->getFixerSignatures());
    }

    public function testItThrowsExceptionOnDuplicatedFixerSignature(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        new RulesSignature(
            FixerSignature::fromInstance(new SignatureFixer(), true),
            FixerSignature::fromInstance(new SignatureFixer(), true)
        );
    }

    public function testSignaturesAreEqual(): void
    {
        self::assertTrue(
            (new RulesSignature(
                FixerSignature::fromRawValues('dummy', '123abc', true),
                FixerSignature::fromInstance(new SignatureFixer(), true)
            ))
                ->equals(new RulesSignature(
                    FixerSignature::fromInstance(new SignatureFixer(), true),
                    FixerSignature::fromRawValues('dummy', '123abc', true),
                ))
        );
    }
}
