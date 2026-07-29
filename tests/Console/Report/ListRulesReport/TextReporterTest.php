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

namespace PhpCsFixer\Tests\Console\Report\ListRulesReport;

use PhpCsFixer\Console\Report\ListRulesReport\ReporterInterface;
use PhpCsFixer\Console\Report\ListRulesReport\TextReporter;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\ListRulesReport\TextReporter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(TextReporter::class)]
final class TextReporterTest extends AbstractReporterTestCase
{
    protected function createReporter(): ReporterInterface
    {
        return new TextReporter();
    }

    protected function getFormat(): string
    {
        return 'txt';
    }

    protected function assertFormat(string $expected, string $input): void
    {
        self::assertSame($expected, $input);
    }

    protected static function createSimpleReport(): string
    {
        return str_replace(
            "\n",
            \PHP_EOL,
            <<<'LIST'
                  1) fixer_1
                       Summary 1.
                  2) fixer_2
                       Summary 2.
                       Rule is risky.

                LIST,
        );
    }
}
