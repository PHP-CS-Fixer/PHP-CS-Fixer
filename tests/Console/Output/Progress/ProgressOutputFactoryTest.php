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

namespace PhpCsFixer\Tests\Console\Output\Progress;

use PhpCsFixer\Console\Output\OutputContext;
use PhpCsFixer\Console\Output\Progress\DotsOutput;
use PhpCsFixer\Console\Output\Progress\NullOutput;
use PhpCsFixer\Console\Output\Progress\ProgressOutputFactory;
use PhpCsFixer\Console\Output\Progress\ProgressOutputType;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Output\NullOutput as SymfonyNullOutput;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\Progress\ProgressOutputFactory
 */
final class ProgressOutputFactoryTest extends TestCase
{
    /**
     * @dataProvider provideValidProcessOutputIsCreatedCases
     */
    public function testValidProcessOutputIsCreated(
        string $outputType,
        OutputContext $context,
        string $expectedOutputClass
    ): void {
        self::assertInstanceOf($expectedOutputClass, (new ProgressOutputFactory())->create($outputType, $context));
    }

    public static function provideValidProcessOutputIsCreatedCases(): iterable
    {
        $context = new OutputContext(new SymfonyNullOutput(), 100, 10);
        $nullContext = new OutputContext(null, 100, 10);

        yield 'none' => [ProgressOutputType::NONE, $context, NullOutput::class];

        yield 'dots' => [ProgressOutputType::DOTS, $context, DotsOutput::class];

        yield 'dots with null output' => [ProgressOutputType::DOTS, $nullContext, NullOutput::class];

        yield 'unsupported type with null output' => ['boom', $nullContext, NullOutput::class];
    }

    public function testExceptionIsThrownForUnsupportedProcessOutputType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ProgressOutputFactory())->create(
            'boom',
            new OutputContext(new SymfonyNullOutput(), 100, 10)
        );
    }
}
