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
use PhpCsFixer\Console\Report\ListRulesReport\ReportSummary;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
abstract class AbstractReporterTestCase extends TestCase
{
    protected ?ReporterInterface $reporter = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reporter = $this->createReporter();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->reporter = null;
    }

    final public function testGetFormat(): void
    {
        self::assertSame(
            $this->getFormat(),
            $this->reporter->getFormat(),
        );
    }

    /**
     * @dataProvider provideGenerateCases
     */
    #[DataProvider('provideGenerateCases')]
    final public function testGenerate(string $expectedReport, ReportSummary $reportSummary): void
    {
        $actualReport = $this->reporter->generate($reportSummary);

        $this->assertFormat($expectedReport, $actualReport);
    }

    /**
     * @return iterable<string, array{string, ReportSummary}>
     */
    final public static function provideGenerateCases(): iterable
    {
        $fixer1 = new class implements FixerInterface {
            public function getName(): string
            {
                return 'fixer_1';
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                return new FixerDefinition('Summary 1.', []);
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
                return new FixerDefinition('Summary 2.', []);
            }

            public function isRisky(): bool
            {
                return true;
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

        yield 'example' => [
            static::createSimpleReport(),
            new ReportSummary([
                $fixer1,
                $fixer2,
            ]),
        ];
    }

    abstract protected function createReporter(): ReporterInterface;

    abstract protected function getFormat(): string;

    abstract protected function assertFormat(string $expected, string $input): void;

    abstract protected static function createSimpleReport(): string;
}
