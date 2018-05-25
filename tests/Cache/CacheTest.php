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

use PhpCsFixer\Cache\Cache;
use PhpCsFixer\Cache\Signature;
use PhpCsFixer\Cache\SignatureInterface;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\Cache
 */
final class CacheTest extends TestCase
{
    public function testIsFinal()
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\Cache::class);

        $this->assertTrue($reflection->isFinal());
    }

    public function testImplementsCacheInterface()
    {
        $reflection = new \ReflectionClass(\PhpCsFixer\Cache\Cache::class);

        $this->assertTrue($reflection->implementsInterface(\PhpCsFixer\Cache\CacheInterface::class));
    }

    public function testConstructorSetsValues()
    {
        $signature = $this->getSignatureDouble();

        $cache = new Cache($signature);

        $this->assertSame($signature, $cache->getSignature());
    }

    public function testDefaults()
    {
        $signature = $this->getSignatureDouble();

        $cache = new Cache($signature);

        $file = 'test.php';

        $this->assertFalse($cache->has($file));
        $this->assertNull($cache->get($file));
    }

    public function testSetThrowsInvalidArgumentExceptionIfValueIsNotAnInteger()
    {
        $this->expectException(\InvalidArgumentException::class);

        $signature = $this->getSignatureDouble();

        $cache = new Cache($signature);

        $file = 'test.php';

        $cache->set($file, null);
    }

    public function testCanSetAndGetValue()
    {
        $signature = $this->getSignatureDouble();

        $cache = new Cache($signature);

        $file = 'test.php';
        $hash = crc32('hello');

        $cache->set($file, $hash);

        $this->assertTrue($cache->has($file));
        $this->assertSame($hash, $cache->get($file));
    }

    public function testCanClearValue()
    {
        $signature = $this->getSignatureDouble();

        $cache = new Cache($signature);

        $file = 'test.php';
        $hash = crc32('hello');

        $cache->set($file, $hash);
        $cache->clear($file);

        $this->assertNull($cache->get($file));
    }

    public function testFromJsonThrowsInvalidArgumentExceptionIfJsonIsInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);

        $json = '{"foo';

        Cache::fromJson($json);
    }

    /**
     * @dataProvider provideMissingDataCases
     *
     * @param array $data
     */
    public function testFromJsonThrowsInvalidArgumentExceptionIfJsonIsMissingKey(array $data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $json = json_encode($data);

        Cache::fromJson($json);
    }

    /**
     * @return array
     */
    public function provideMissingDataCases()
    {
        $data = [
            'php' => '7.1.2',
            'version' => '2.0',
            'rules' => [
                'foo' => true,
                'bar' => false,
            ],
            'hashes' => [],
        ];

        return array_map(static function ($missingKey) use ($data) {
            unset($data[$missingKey]);

            return [
                $data,
            ];
        }, array_keys($data));
    }

    /**
     * @dataProvider provideCanConvertToAndFromJsonCases
     */
    public function testCanConvertToAndFromJson(SignatureInterface $signature)
    {
        $cache = new Cache($signature);

        $file = 'test.php';
        $hash = crc32('hello');

        $cache->set($file, $hash);
        $cached = Cache::fromJson($cache->toJson());

        $this->assertTrue($cached->getSignature()->equals($signature));
        $this->assertTrue($cached->has($file));
        $this->assertSame($hash, $cached->get($file));
    }

    public function provideCanConvertToAndFromJsonCases()
    {
        $toolInfo = new ToolInfo();

        return [
            [new Signature(
                PHP_VERSION,
                '2.0',
                [
                    'foo' => true,
                    'bar' => true,
                ]
            )],
            [new Signature(
                PHP_VERSION,
                $toolInfo->getVersion(),
                [
                    // value encoded in ANSI, not UTF
                    'header_comment' => ['header' => 'Dariusz '.base64_decode('UnVtafFza2k=', true)],
                ]
            )],
        ];
    }

    public function testToJsonThrowsExceptionOnInvalid()
    {
        $invalidUtf8Sequence = "\xB1\x31";

        $signature = $this->prophesize('PhpCsFixer\Cache\SignatureInterface');
        $signature->getPhpVersion()->willReturn('7.1.0');
        $signature->getFixerVersion()->willReturn('2.2.0');
        $signature->getRules()->willReturn([
            $invalidUtf8Sequence => true,
        ]);

        $cache = new Cache($signature->reveal());

        $this->expectException(
            'UnexpectedValueException'
        );
        $this->expectExceptionMessage(
            'Can not encode cache signature to JSON, error: "Malformed UTF-8 characters, possibly incorrectly encoded". If you have non-UTF8 chars in your signature, like in license for `header_comment`, consider enabling `ext-mbstring` or install `symfony/polyfill-mbstring`.'
        );

        $cache->toJson();
    }

    /**
     * @return SignatureInterface
     */
    private function getSignatureDouble()
    {
        return $this->prophesize(\PhpCsFixer\Cache\SignatureInterface::class)->reveal();
    }
}
