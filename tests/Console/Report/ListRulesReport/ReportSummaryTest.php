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

use PhpCsFixer\Console\Report\ListRulesReport\ReportSummary;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\ListRulesReport\ReportSummary
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ReportSummaryTest extends TestCase
{
    public function testGetFixers(): void
    {
        $fixer1 = new class implements FixerInterface {
            public function getName(): string
            {
                return 'fixer_1';
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            public function isRisky(): bool
            {
                return false;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                return true;
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void {}

            public function getPriority(): int
            {
                return 0;
            }

            public function supports(\SplFileInfo $file): bool
            {
                return true;
            }
        };

        $fixer2 = new class implements FixerInterface {
            public function getName(): string
            {
                return 'fixer_2';
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            public function isRisky(): bool
            {
                return false;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                return true;
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void {}

            public function getPriority(): int
            {
                return 0;
            }

            public function supports(\SplFileInfo $file): bool
            {
                return true;
            }
        };

        $fixers = [$fixer1, $fixer2];

        $summary = new ReportSummary($fixers);

        self::assertSame($fixers, $summary->getFixers());
    }
}
