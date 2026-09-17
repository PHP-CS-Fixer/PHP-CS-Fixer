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

namespace PhpCsFixer\Tests;

use PhpCsFixer\WhitespacesFixerConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\WhitespacesFixerConfig
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(WhitespacesFixerConfig::class)]
final class WhitespacesFixerConfigTest extends TestCase
{
    /**
     * @param non-empty-string  $indent
     * @param non-empty-string  $lineEnding
     * @param ?non-empty-string $exceptionRegExp
     *
     * @dataProvider provideFixCases
     */
    #[DataProvider('provideFixCases')]
    public function testFix(string $indent, string $lineEnding, ?string $exceptionRegExp = null): void
    {
        if (null !== $exceptionRegExp) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessageMatches('%^'.preg_quote($exceptionRegExp, '%').'$%');
        }

        $config = new WhitespacesFixerConfig($indent, $lineEnding);

        self::assertSame($indent, $config->getIndent());
        self::assertSame($lineEnding, $config->getLineEnding());
    }

    /**
     * @return iterable<int, array{0: non-empty-string, 1: non-empty-string, 2?: non-empty-string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['    ', "\n"];

        yield ["\t", "\n"];

        yield ['    ', "\r\n"];

        yield ["\t", "\r\n"];

        yield ['    ', 'asd', 'Invalid "lineEnding" param, expected "\n" or "\r\n".'];

        yield ['std', "\n", 'Invalid "indent" param, expected tab or two or four spaces.'];
    }
}
