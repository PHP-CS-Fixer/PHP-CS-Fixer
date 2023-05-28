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

use PhpCsFixer\Cache\Signature\ConfigSignature;
use PhpCsFixer\Cache\Signature\ConfigSignatureInterface;
use PhpCsFixer\Cache\Signature\FixerSignature;
use PhpCsFixer\Cache\Signature\RulesSignature;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\Signature\ConfigSignature
 */
final class ConfigSignatureTest extends TestCase
{
    public function testIsFinal(): void
    {
        $reflection = new \ReflectionClass(ConfigSignature::class);

        self::assertTrue($reflection->isFinal());
    }

    public function testImplementsSignatureInterface(): void
    {
        $reflection = new \ReflectionClass(ConfigSignature::class);

        self::assertTrue($reflection->implementsInterface(ConfigSignatureInterface::class));
    }

    public function testConstructorSetsValues(): void
    {
        $php = PHP_VERSION;
        $version = '3.0';
        $indent = '    ';
        $lineEnding = PHP_EOL;

        $signature = new ConfigSignature($php, $version, $indent, $lineEnding, new RulesSignature(
            FixerSignature::fromRawValues('foo', '', true),
            FixerSignature::fromRawValues('bar', '', false)
        ));

        self::assertSame($php, $signature->getPhpVersion());
        self::assertSame($version, $signature->getFixerVersion());
        self::assertSame($indent, $signature->getIndent());
        self::assertSame($lineEnding, $signature->getLineEnding());
        self::assertSame(
            ['bar' => ['hash' => '', 'config' => false], 'foo' => ['hash' => '', 'config' => true]],
            $signature->getRules()
        );
    }

    /**
     * @dataProvider provideEqualsReturnsFalseIfValuesAreNotIdenticalCases
     */
    public function testEqualsReturnsFalseIfValuesAreNotIdentical(ConfigSignature $signature, ConfigSignature $anotherSignature): void
    {
        self::assertFalse($signature->equals($anotherSignature));
    }

    public static function provideEqualsReturnsFalseIfValuesAreNotIdenticalCases(): iterable
    {
        $php = PHP_VERSION;
        $version = '2.0';
        $indent = '    ';
        $lineEnding = "\n";
        $rules = new RulesSignature(
            FixerSignature::fromRawValues('foo', '', true),
            FixerSignature::fromRawValues('bar', '', false)
        );

        $base = new ConfigSignature($php, $version, $indent, $lineEnding, $rules);

        yield 'php' => [
            $base,
            new ConfigSignature('50400', $version, $indent, $lineEnding, $rules),
        ];

        yield 'version' => [
            $base,
            new ConfigSignature($php, '2.12', $indent, $lineEnding, $rules),
        ];

        yield 'indent' => [
            $base,
            new ConfigSignature($php, $version, "\t", $lineEnding, $rules),
        ];

        yield 'lineEnding' => [
            $base,
            new ConfigSignature($php, $version, $indent, "\r\n", $rules),
        ];

        yield 'rules' => [
            $base,
            new ConfigSignature($php, $version, $indent, $lineEnding, new RulesSignature(
                FixerSignature::fromRawValues('foo', '', false)
            )),
        ];
    }

    public function testEqualsReturnsTrueIfValuesAreIdentical(): void
    {
        $php = PHP_VERSION;
        $version = '2.0';
        $indent = '    ';
        $lineEnding = PHP_EOL;
        $rules = new RulesSignature(FixerSignature::fromRawValues('foo', '', true));

        $signature = new ConfigSignature($php, $version, $indent, $lineEnding, $rules);
        $anotherSignature = new ConfigSignature($php, $version, $indent, $lineEnding, $rules);

        self::assertTrue($signature->equals($anotherSignature));
    }
}
