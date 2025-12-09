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

use PhpCsFixer\Cache\Cache;
use PhpCsFixer\Cache\CacheInterface;
use PhpCsFixer\Cache\Signature;
use PhpCsFixer\Cache\SignatureInterface;
use PhpCsFixer\Config;
use PhpCsFixer\Hasher;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Cache\Cache
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class CacheTest extends TestCase
{
    public function testIsFinal(): void
    {
        $reflection = new \ReflectionClass(Cache::class);

        self::assertTrue($reflection->isFinal());
    }

    public function testImplementsCacheInterface(): void
    {
        $reflection = new \ReflectionClass(Cache::class);

        self::assertTrue($reflection->implementsInterface(CacheInterface::class));
    }

    public function testConstructorSetsValues(): void
    {
        $signature = $this->createSignatureDouble();

        $cache = new Cache($signature);

        self::assertSame($signature, $cache->getSignature());
    }

    public function testDefaults(): void
    {
        $signature = $this->createSignatureDouble();

        $cache = new Cache($signature);

        $file = 'test.php';

        self::assertFalse($cache->has($file));
        self::assertNull($cache->get($file));
    }

    public function testCanSetAndGetValue(): void
    {
        $signature = $this->createSignatureDouble();

        $cache = new Cache($signature);

        $file = 'test.php';
        $hash = Hasher::calculate('hello');

        $cache->set($file, $hash);

        self::assertTrue($cache->has($file));
        self::assertSame($hash, $cache->get($file));
    }

    public function testCanClearValue(): void
    {
        $signature = $this->createSignatureDouble();

        $cache = new Cache($signature);

        $file = 'test.php';
        $hash = Hasher::calculate('hello');

        $cache->set($file, $hash);
        $cache->clear($file);

        self::assertNull($cache->get($file));
    }

    public function testFromJsonThrowsInvalidArgumentExceptionIfJsonIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $json = '{"foo';

        Cache::fromJson($json);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider provideFromJsonThrowsInvalidArgumentExceptionIfJsonIsMissingKeyCases
     */
    public function testFromJsonThrowsInvalidArgumentExceptionIfJsonIsMissingKey(array $data): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $json = json_encode($data, \JSON_THROW_ON_ERROR);

        Cache::fromJson($json);
    }

    /**
     * @return iterable<int, array{array<string, mixed>}>
     */
    public static function provideFromJsonThrowsInvalidArgumentExceptionIfJsonIsMissingKeyCases(): iterable
    {
        $data = [
            'php' => '7.1.2',
            'version' => '2.0',
            'rules' => [
                'foo' => true,
                'bar' => false,
            ],
            'ruleCustomisationPolicyVersion' => '1.2.3',
            'hashes' => [],
        ];

        return array_map(static function (string $missingKey) use ($data): array {
            unset($data[$missingKey]);

            return [
                $data,
            ];
        }, array_keys($data));
    }

    /**
     * @dataProvider provideCanConvertToAndFromJsonCases
     */
    public function testCanConvertToAndFromJson(SignatureInterface $signature): void
    {
        $cache = new Cache($signature);

        $file = 'test.php';
        $hash = Hasher::calculate('hello');

        $cache->set($file, $hash);
        $cached = Cache::fromJson($cache->toJson());

        self::assertTrue($cached->getSignature()->equals($signature));
        self::assertTrue($cached->has($file));
        self::assertSame($hash, $cached->get($file));
    }

    /**
     * @return iterable<int, array{Signature}>
     */
    public static function provideCanConvertToAndFromJsonCases(): iterable
    {
        $toolInfo = new ToolInfo();
        $config = new Config();

        yield [new Signature(
            \PHP_VERSION,
            '2.0',
            '  ',
            "\r\n",
            [
                'foo' => true,
                'bar' => true,
            ],
            'fooBar'
        )];

        yield [new Signature(
            \PHP_VERSION,
            $toolInfo->getVersion(),
            $config->getIndent(),
            $config->getLineEnding(),
            [
                // value encoded in ANSI, not UTF
                'header_comment' => ['header' => 'Dariusz '.base64_decode('UnVtafFza2k=', true)],
            ],
            'fooBar'
        )];
    }

    public function testToJsonThrowsExceptionOnInvalid(): void
    {
        $signature = $this->createSignatureDouble();

        $cache = new Cache($signature);

        $this->expectException(
            \UnexpectedValueException::class
        );

        $this->expectExceptionMessage(
            'Cannot encode cache signature to JSON, error: "Malformed UTF-8 characters, possibly incorrectly encoded". If you have non-UTF8 chars in your signature, like in license for `header_comment`, consider enabling `ext-mbstring` or install `symfony/polyfill-mbstring`.'
        );

        $cache->toJson();
    }

    private function createSignatureDouble(): SignatureInterface
    {
        return new class implements SignatureInterface {
            public function getPhpVersion(): string
            {
                return '7.1.0';
            }

            public function getFixerVersion(): string
            {
                return '2.2.0';
            }

            public function getIndent(): string
            {
                return '    ';
            }

            public function getLineEnding(): string
            {
                return \PHP_EOL;
            }

            public function getRules(): array
            {
                return [
                    "\xB1\x31" => true, // invalid UTF8 sequence
                ];
            }

            public function getRuleCustomisationPolicyVersion(): string
            {
                return 'Policy Version';
            }

            public function equals(SignatureInterface $signature): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
