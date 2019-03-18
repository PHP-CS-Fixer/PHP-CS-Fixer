<?php

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
    public function testIsFinal()
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\Signature::class);

        $this->assertTrue($reflection->isFinal());
    }

    public function testImplementsSignatureInterface()
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\Signature::class);

        $this->assertTrue($reflection->implementsInterface(\PhpCsFixer\Cache\SignatureInterface::class));
    }

    public function testConstructorSetsValues()
    {
        $php = PHP_VERSION;
        $version = '2.0';
        $indent = '    ';
        $lineEnding = PHP_EOL;
        $rules = [
            'foo',
            'bar',
        ];

        $signature = new Signature(
            $php,
            $version,
            $indent,
            $lineEnding,
            $rules
        );

        $this->assertSame($php, $signature->getPhpVersion());
        $this->assertSame($version, $signature->getFixerVersion());
        $this->assertSame($indent, $signature->getIndent());
        $this->assertSame($lineEnding, $signature->getLineEnding());
        $this->assertSame($rules, $signature->getRules());
    }

    /**
     * @dataProvider provideEqualsReturnsFalseIfValuesAreNotIdenticalCases
     *
     * @param Signature $signature
     * @param Signature $anotherSignature
     */
    public function testEqualsReturnsFalseIfValuesAreNotIdentical($signature, $anotherSignature)
    {
        $this->assertFalse($signature->equals($anotherSignature));
    }

    public function provideEqualsReturnsFalseIfValuesAreNotIdenticalCases()
    {
        $php = PHP_VERSION;
        $version = '2.0';
        $indent = '    ';
        $lineEnding = "\n";
        $rules = [
            'foo',
            'bar',
        ];

        $base = new Signature($php, $version, $indent, $lineEnding, $rules);

        yield 'php' => [
            $base,
            new Signature('50400', $version, $indent, $lineEnding, $rules),
        ];

        yield 'version' => [
            $base,
            new Signature($php, '2.12', $indent, $lineEnding, $rules),
        ];

        yield 'indent' => [
            $base,
            new Signature($php, $version, "\t", $lineEnding, $rules),
        ];

        yield 'lineEnding' => [
            $base,
            new Signature($php, $version, $indent, "\r\n", $rules),
        ];

        yield 'rules' => [
            $base,
            new Signature($php, $version, $indent, $lineEnding, ['foo']),
        ];
    }

    public function testEqualsReturnsTrueIfValuesAreIdentical()
    {
        $php = PHP_VERSION;
        $version = '2.0';
        $indent = '    ';
        $lineEnding = PHP_EOL;
        $rules = [
            'foo',
            'bar',
        ];

        $signature = new Signature(
            $php,
            $version,
            $indent,
            $lineEnding,
            $rules
        );

        $anotherSignature = new Signature(
            $php,
            $version,
            $indent,
            $lineEnding,
            $rules
        );

        $this->assertTrue($signature->equals($anotherSignature));
    }
}
