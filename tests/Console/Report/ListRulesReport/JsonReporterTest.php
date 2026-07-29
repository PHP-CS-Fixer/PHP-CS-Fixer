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

use PhpCsFixer\Console\Report\ListRulesReport\JsonReporter;
use PhpCsFixer\Console\Report\ListRulesReport\ReporterInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\ListRulesReport\JsonReporter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(JsonReporter::class)]
final class JsonReporterTest extends AbstractReporterTestCase
{
    protected function createReporter(): ReporterInterface
    {
        return new JsonReporter();
    }

    protected function getFormat(): string
    {
        return 'json';
    }

    protected function assertFormat(string $expected, string $input): void
    {
        self::assertJsonStringEqualsJsonString($expected, $input);
    }

    protected static function createSimpleReport(): string
    {
        return <<<'JSON'
            {
                "rules": {
                    "fixer_1": {
                        "isRisky": false,
                        "name": "fixer_1",
                        "summary": "Summary 1."
                    },
                    "fixer_2": {
                        "isRisky": true,
                        "name": "fixer_2",
                        "summary": "Summary 2."
                    }
                }
            }
            JSON;
    }
}
