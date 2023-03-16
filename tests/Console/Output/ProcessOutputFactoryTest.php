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

namespace PhpCsFixer\Tests\Console\Output;

use PhpCsFixer\Console\Output\NullOutput;
use PhpCsFixer\Console\Output\OutputContext;
use PhpCsFixer\Console\Output\ProcessOutput;
use PhpCsFixer\Console\Output\ProcessOutputFactory;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Output\NullOutput as SymfonyNullOutput;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\ProcessOutputFactory
 */
final class ProcessOutputFactoryTest extends TestCase
{
    /**
     * @dataProvider provideValidProcessOutputContextCases
     */
    public function testValidProcessOutputIsCreated(
        string $outputType,
        OutputContext $context,
        string $expectedOutputClass
    ): void {
        self::assertInstanceOf($expectedOutputClass, ProcessOutputFactory::create($outputType, $context));
    }

    public static function provideValidProcessOutputContextCases(): iterable
    {
        $context = new OutputContext(new SymfonyNullOutput(), 100, 10);
        $nullContext = new OutputContext(null, 100, 10);

        yield 'none' => ['none', $context, NullOutput::class];

        yield 'dots' => ['dots', $context, ProcessOutput::class];

        yield 'dots with null output' => ['dots', $nullContext, NullOutput::class];

        yield 'unsupported type with null output' => ['boom', $nullContext, NullOutput::class];
    }

    public function testExceptionIsThrownForUnsupportedProcessOutputType(): void
    {
        $this->expectException(\RuntimeException::class);

        ProcessOutputFactory::create(
            'boom',
            new OutputContext(new SymfonyNullOutput(), 100, 10)
        );
    }
}
