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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\WhitespacesFixerConfig
 */
final class WhitespacesFixerConfigTest extends TestCase
{
    /**
     * @dataProvider provideTestCases
     */
    public function testCases(string $indent, string $lineEnding, ?string $exceptionRegExp = null): void
    {
        if (null !== $exceptionRegExp) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessageMatches('%^'.preg_quote($exceptionRegExp, '%').'$%');
        }

        $config = new WhitespacesFixerConfig($indent, $lineEnding);

        static::assertSame($indent, $config->getIndent());
        static::assertSame($lineEnding, $config->getLineEnding());
    }

    public static function provideTestCases(): array
    {
        return [
            ['    ', "\n"],
            ["\t", "\n"],
            ['    ', "\r\n"],
            ["\t", "\r\n"],
            ['    ', 'asd', 'Invalid "lineEnding" param, expected "\n" or "\r\n".'],
            ['std', "\n", 'Invalid "indent" param, expected tab or two or four spaces.'],
        ];
    }
}
