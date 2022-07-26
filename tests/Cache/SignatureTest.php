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

namespace PhpCsFixer\Tests\Cache;

use PhpCsFixer\Cache\Signature;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\Signature
 */
final class SignatureTest extends TestCase
{
    public function testIsFinal(): void
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\Signature::class);

        static::assertTrue($reflection->isFinal());
    }

    public function testImplementsSignatureInterface(): void
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\Signature::class);

        static::assertTrue($reflection->implementsInterface(\PhpCsFixer\Cache\SignatureInterface::class));
    }

    public function testConstructorSetsValues(): void
    {
        $php = PHP_VERSION;
        $version = '2.0';
        $indent = '    ';
        $continuationIndent = '    ';
        $lineEnding = PHP_EOL;
        $rules = [
            'foo',
            'bar',
        ];

        $signature = new Signature(
            $php,
            $version,
            $indent,
            $continuationIndent,
            $lineEnding,
            $rules
        );

        static::assertSame($php, $signature->getPhpVersion());
        static::assertSame($version, $signature->getFixerVersion());
        static::assertSame($indent, $signature->getIndent());
        static::assertSame($continuationIndent, $signature->getContinuationIndent());
        static::assertSame($lineEnding, $signature->getLineEnding());
        static::assertSame($rules, $signature->getRules());
    }

    /**
     * @dataProvider provideEqualsReturnsFalseIfValuesAreNotIdenticalCases
     */
    public function testEqualsReturnsFalseIfValuesAreNotIdentical(Signature $signature, Signature $anotherSignature): void
    {
        static::assertFalse($signature->equals($anotherSignature));
    }

    public function provideEqualsReturnsFalseIfValuesAreNotIdenticalCases(): iterable
    {
        $php = PHP_VERSION;
        $version = '2.0';
        $indent = '    ';
        $continuationIndent = '    ';
        $lineEnding = "\n";
        $rules = [
            'foo',
            'bar',
        ];

        $base = new Signature($php, $version, $indent, $continuationIndent, $lineEnding, $rules);

        yield 'php' => [
            $base,
            new Signature('50400', $version, $indent, $continuationIndent, $lineEnding, $rules),
        ];

        yield 'version' => [
            $base,
            new Signature($php, '2.12', $indent, $continuationIndent, $lineEnding, $rules),
        ];

        yield 'indent' => [
            $base,
            new Signature($php, $version, "\t", $continuationIndent, $lineEnding, $rules),
        ];

        yield 'continuationIndent' => [
            $base,
            new Signature($php, $version, $indent, "\t", $lineEnding, $rules),
        ];

        yield 'lineEnding' => [
            $base,
            new Signature($php, $version, $indent, $continuationIndent, "\r\n", $rules),
        ];

        yield 'rules' => [
            $base,
            new Signature($php, $version, $indent, $continuationIndent, $lineEnding, ['foo']),
        ];
    }

    public function testEqualsReturnsTrueIfValuesAreIdentical(): void
    {
        $php = PHP_VERSION;
        $version = '2.0';
        $indent = '    ';
        $continuationIndent = '    ';
        $lineEnding = PHP_EOL;
        $rules = [
            'foo',
            'bar',
        ];

        $signature = new Signature(
            $php,
            $version,
            $indent,
            $continuationIndent,
            $lineEnding,
            $rules
        );

        $anotherSignature = new Signature(
            $php,
            $version,
            $indent,
            $continuationIndent,
            $lineEnding,
            $rules
        );

        static::assertTrue($signature->equals($anotherSignature));
    }
}
